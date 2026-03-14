<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Illuminate\Support\Str;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('News Content')
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($set, ?string $state) => $set('slug', Str::slug($state))),

                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),

                                TextInput::make('author')
                                    ->maxLength(255),

                                Textarea::make('summary')
                                    ->columnSpanFull()
                                    ->rows(3),

                                RichEditor::make('content')
                                    ->columnSpanFull()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'h2',
                                        'h3',
                                        'bulletList',
                                        'orderedList',
                                        'link',
                                        'redo',
                                        'undo',
                                    ]),
                            ])
                            ->columns(2)
                            ->columnSpan(2),

                        Section::make('Placement Details')
                            ->schema([
                                Select::make('edition_id')
                                    ->relationship('edition', 'title')
                                    ->required()
                                    ->searchable(),

                                Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('page_number')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->helperText('The page where this article starts.'),
                            ])
                            ->columnSpan(1),
                    ])
            ]);
    }
}