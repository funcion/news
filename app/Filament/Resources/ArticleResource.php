<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Contenido';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Muro de Producción Final')
                    ->description('Este es tu escaparate público. Aquí gestionas el contenido final, ajustas el SEO y publicas lo que la IA ha redactado.')
                    ->schema([
                        Tabs::make('Article')
                            ->tabs([
                                Tabs\Tab::make('Contenido')
                                    ->schema([
                                        TextInput::make('title')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                        TextInput::make('slug')
                                            ->required()
                                            ->unique(Article::class, 'slug', ignoreRecord: true),
                                        Textarea::make('excerpt')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        RichEditor::make('content')
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),
                                Tabs\Tab::make('Metadatos')
                                    ->schema([
                                        Select::make('author_id')
                                            ->relationship('author', 'name')
                                            ->required(),
                                        Select::make('category_id')
                                            ->relationship('category', 'name')
                                            ->required(),
                                        Select::make('status')
                                            ->options([
                                                'draft' => 'Borrador',
                                                'pending_review' => 'Pendiente de Revisión',
                                                'published' => 'Publicado',
                                            ])
                                            ->required()
                                            ->default('draft'),
                                        DateTimePicker::make('published_at'),
                                        TextInput::make('image_url')
                                            ->url(),
                                        TextInput::make('image_alt'),
                                    ]),
                                Tabs\Tab::make('SEO')
                                    ->schema([
                                        TextInput::make('meta_title'),
                                        Textarea::make('meta_description')
                                            ->rows(3),
                                        TextInput::make('meta_keywords'),
                                        TextInput::make('seo_score')
                                            ->numeric()
                                            ->disabled(),
                                    ]),
                            ])->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Imagen'),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('author.name')
                    ->sortable(),
                TextColumn::make('category.name')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending_review' => 'warning',
                        'published' => 'success',
                    }),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('views')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Borrador',
                        'pending_review' => 'Pendiente de Revisión',
                        'published' => 'Publicado',
                    ]),
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
