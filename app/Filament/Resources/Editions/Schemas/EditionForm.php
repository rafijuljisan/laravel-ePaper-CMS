<?php

namespace App\Filament\Resources\Editions\Schemas;

use Filament\Forms\Components\DatePicker;                       // ✅
use Filament\Forms\Components\Select;                           // ✅
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;                        // ✅
use Filament\Schemas\Components\Section;                        // ✅ Section stays here
use Filament\Schemas\Schema;                                    // ✅ Schema stays here
use Filament\Forms\Components\FileUpload;

class EditionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Edition Details')
                    ->components([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Daily Morning Edition'),

                        DatePicker::make('edition_date')
                            ->required()
                            ->default(now()),

                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'processing' => 'Processing',
                                'published' => 'Published',
                                'failed' => 'Failed',
                            ])
                            ->default('draft')
                            ->required(),
                    ])->columns(2),

                Section::make('Upload Newspaper')
                    ->components([
                        SpatieMediaLibraryFileUpload::make('pdf')
                            ->collection('editions')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()
                            ->helperText('Upload the full QuarkXPress exported PDF here.')
                            ->columnSpanFull(),
                        FileUpload::make('xml_file')
                            ->label('QuarkXPress XML Export (Optional)')
                            ->disk('public')
                            ->directory('epaper/xml')
                            ->acceptedFileTypes(['text/xml', 'application/xml']),
                    ]),
            ]);
    }
}