<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriberResource\Pages;
use App\Models\Subscriber;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SubscriberResource extends Resource
{
    protected static ?string $model = Subscriber::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Audience';

    protected static ?string $navigationLabel = 'Subscribers';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form
            ->components([
                Section::make('Subscriber Details')
                    ->schema([
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(Subscriber::class, 'email', ignoreRecord: true),
                        Select::make('locale')
                            ->options([
                                'en' => '🇺🇸 English',
                                'es' => '🇪🇸 Español',
                            ])
                            ->default('en')
                            ->required(),
                        TextInput::make('source')
                            ->default('footer')
                            ->disabled(),
                        DateTimePicker::make('verified_at')
                            ->label('Verified At (Double Opt-In)'),
                        DateTimePicker::make('unsubscribed_at')
                            ->label('Unsubscribed At'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                TextColumn::make('status')
                    ->label('Status')
                    ->state(function (Subscriber $record): string {
                        if ($record->unsubscribed_at) {
                            return 'Unsubscribed';
                        }
                        return $record->verified_at ? 'Verified' : 'Pending';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Verified' => 'success',
                        'Pending' => 'warning',
                        'Unsubscribed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('locale')
                    ->label('Lang')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'es' => 'warning',
                        'en' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('source')
                    ->label('Source')
                    ->toggleable(),
                TextColumn::make('verified_at')
                    ->label('Verified At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Subscribed On')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('verified')
                    ->label('Verification')
                    ->placeholder('All Subscribers')
                    ->trueLabel('Verified Only')
                    ->falseLabel('Pending Verification')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('verified_at'),
                        false: fn ($query) => $query->whereNull('verified_at'),
                    ),
                SelectFilter::make('locale')
                    ->options([
                        'en' => 'English',
                        'es' => 'Español',
                    ]),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => SubscriberResource\Pages\ListSubscribers::route('/'),
            'create' => SubscriberResource\Pages\CreateSubscriber::route('/create'),
            'edit'   => SubscriberResource\Pages\EditSubscriber::route('/{record}/edit'),
        ];
    }
}