<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin Glodaxia',
            'email' => 'admin@glodaxia.com',
            'password' => bcrypt('password'),
        ]);

        // Create default AI Author
        Author::create([
            'name' => [
                'en' => 'Glodaxia Editorial Team',
                'es' => 'Equipo Editorial Glodaxia',
            ],
            'slug' => 'glodaxia-editorial-team',
            'bio' => [
                'en' => 'Expert editorial team focused on technology, innovation, and digital trends from Glodaxia.',
                'es' => 'Equipo editorial experto en tecnología, innovación y tendencias digitales de Glodaxia.',
            ],
            'type' => 'ai',
            'voice_style' => 'Analytical',
            'is_active' => true,
        ]);

        // Create Categories
        $categories = [
            ['en' => 'Artificial Intelligence', 'es' => 'Inteligencia Artificial', 'slug_en' => 'artificial-intelligence', 'slug_es' => 'inteligencia-artificial'],
            ['en' => 'Machine Learning', 'es' => 'Aprendizaje Automático', 'slug_en' => 'machine-learning', 'slug_es' => 'aprendizaje-automatico'],
            ['en' => 'Startups', 'es' => 'Startups', 'slug_en' => 'startups', 'slug_es' => 'startups'],
            ['en' => 'Cybersecurity', 'es' => 'Ciberseguridad', 'slug_en' => 'cybersecurity', 'slug_es' => 'ciberseguridad'],
            ['en' => 'Tech Industry', 'es' => 'Industria Tech', 'slug_en' => 'tech-industry', 'slug_es' => 'industria-tech'],
            ['en' => 'Science', 'es' => 'Ciencia', 'slug_en' => 'science', 'slug_es' => 'ciencia'],
            ['en' => 'Innovation', 'es' => 'Innovación', 'slug_en' => 'innovation', 'slug_es' => 'innovacion'],
            ['en' => 'Automation', 'es' => 'Automatización', 'slug_en' => 'automation', 'slug_es' => 'automatizacion'],
        ];

        foreach ($categories as $catData) {
            Category::create([
                'name' => ['en' => $catData['en'], 'es' => $catData['es']],
                'slug_en' => $catData['slug_en'],
                'slug_es' => $catData['slug_es'],
                'is_active' => true,
            ]);
        }

        // Create Featured/Sample Tags
        $tags = [
            ['en' => 'OpenAI', 'es' => 'OpenAI'],
            ['en' => 'GPT', 'es' => 'GPT'],
            ['en' => 'Neural Networks', 'es' => 'Redes Neuronales'],
            ['en' => 'Deep Learning', 'es' => 'Deep Learning'],
            ['en' => 'Robotics', 'es' => 'Robótica'],
            ['en' => 'Data Science', 'es' => 'Ciencia de Datos'],
            ['en' => 'Cloud Computing', 'es' => 'Cloud Computing'],
            ['en' => 'Blockchain', 'es' => 'Blockchain'],
        ];

        foreach ($tags as $tagData) {
            Tag::create([
                'name' => ['en' => $tagData['en'], 'es' => $tagData['es']],
                'slug' => strtolower(str_replace(' ', '-', $tagData['en'])),
                'is_featured' => true,
                'article_count' => 0,
            ]);
        }

        // Create Sample RSS Sources
        $rssSources = [
            [
                'name' => 'TechCrunch AI',
                'url' => 'https://techcrunch.com/category/artificial-intelligence/feed/',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'OpenAI Blog',
                'url' => 'https://openai.com/blog/feed.xml',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'The Verge AI',
                'url' => 'https://www.theverge.com/ai-artificial-intelligence/rss/index.xml',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'MIT Tech Review AI',
                'url' => 'https://www.technologyreview.com/topic/artificial-intelligence/feed/',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 60,
                'is_active' => true,
            ],
        ];

        foreach ($rssSources as $sourceData) {
            Source::create($sourceData);
        }
    }
}