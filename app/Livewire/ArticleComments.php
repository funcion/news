<?php

namespace App\Livewire;

use App\Models\Article;
use App\Models\Comment;
use App\Models\CommentLike;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

class ArticleComments extends Component
{
    public int $articleId;
    public string $newComment = '';
    public ?int $replyingTo = null;
    public string $replyContent = '';
    public int $perPage = 10;
    public string $honeypot = ''; // Bot trap

    public ?string $feedbackMessage = null;
    public string $feedbackType = 'success';

    public function mount(Article|int $article)
    {
        $this->articleId = is_int($article) ? $article : $article->id;
    }

    public function postComment()
    {
        if (!empty($this->honeypot)) {
            return;
        }

        if (!Auth::check()) {
            return redirect()->guest(route('login'));
        }

        $this->validate([
            'newComment' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $throttleKey = 'comment-user:' . Auth::id();
        if (RateLimiter::tooManyAttempts($throttleKey, 4)) {
            $this->feedbackMessage = __('ui.comment_too_fast');
            $this->feedbackType = 'error';
            return;
        }
        RateLimiter::hit($throttleKey, 60);

        // Anti-spam basic check
        $status = $this->detectSpam($this->newComment) ? 'pending' : 'approved';

        Comment::create([
            'article_id' => $this->articleId,
            'user_id'    => Auth::id(),
            'parent_id'  => null,
            'content'    => trim($this->newComment),
            'status'     => $status,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->newComment = '';
        $this->feedbackMessage = $status === 'approved' 
            ? __('ui.comment_posted_success') 
            : __('ui.comment_pending_moderation');
        $this->feedbackType = 'success';
    }

    public function startReply(int $commentId)
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'));
        }

        $this->replyingTo = $commentId;
        $this->replyContent = '';
    }

    public function cancelReply()
    {
        $this->replyingTo = null;
        $this->replyContent = '';
    }

    public function postReply(int $parentId)
    {
        if (!empty($this->honeypot)) {
            return;
        }

        if (!Auth::check()) {
            return redirect()->guest(route('login'));
        }

        $this->validate([
            'replyContent' => ['required', 'string', 'min:2', 'max:2000'],
        ]);

        $throttleKey = 'comment-user:' . Auth::id();
        if (RateLimiter::tooManyAttempts($throttleKey, 4)) {
            $this->feedbackMessage = __('ui.comment_too_fast');
            $this->feedbackType = 'error';
            return;
        }
        RateLimiter::hit($throttleKey, 60);

        $status = $this->detectSpam($this->replyContent) ? 'pending' : 'approved';

        Comment::create([
            'article_id' => $this->articleId,
            'user_id'    => Auth::id(),
            'parent_id'  => $parentId,
            'content'    => trim($this->replyContent),
            'status'     => $status,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->replyingTo = null;
        $this->replyContent = '';
        $this->feedbackMessage = $status === 'approved' 
            ? __('ui.comment_posted_success') 
            : __('ui.comment_pending_moderation');
        $this->feedbackType = 'success';
    }

    public function toggleLike(int $commentId)
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'));
        }

        $comment = Comment::find($commentId);
        if (!$comment) {
            return;
        }

        $existing = CommentLike::where('comment_id', $commentId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            $existing->delete();
            $comment->decrement('likes_count');
        } else {
            CommentLike::create([
                'comment_id' => $commentId,
                'user_id'    => Auth::id(),
            ]);
            $comment->increment('likes_count');
        }
    }

    public function deleteComment(int $commentId)
    {
        if (!Auth::check()) {
            return;
        }

        $comment = Comment::find($commentId);
        if ($comment && ($comment->user_id === Auth::id() || Auth::user()->slug === 'admin' || Auth::user()->id === 1)) {
            $comment->delete();
            $this->feedbackMessage = __('ui.comment_deleted_success');
            $this->feedbackType = 'success';
        }
    }

    public function loadMore()
    {
        $this->perPage += 10;
    }

    protected function detectSpam(string $text): bool
    {
        // Simple heuristic: excessive URLs or known spam patterns
        if (preg_match_all('/https?:\/\//i', $text) > 2) {
            return true;
        }

        $spamWords = ['viagra', 'casino', 'free crypto', 't.me/', 'whatsapp://'];
        foreach ($spamWords as $word) {
            if (stripos($text, $word) !== false) {
                return true;
            }
        }

        return false;
    }

    public function render()
    {
        $commentsQuery = Comment::where('article_id', $this->articleId)
            ->approved()
            ->root()
            ->with(['user', 'replies.user', 'likes'])
            ->orderBy('created_at', 'desc');

        $totalCount = Comment::where('article_id', $this->articleId)->approved()->count();
        $comments = $commentsQuery->take($this->perPage)->get();
        $hasMore = $commentsQuery->count() > $this->perPage;

        return view('livewire.article-comments', [
            'comments'   => $comments,
            'totalCount' => $totalCount,
            'hasMore'    => $hasMore,
        ]);
    }
}