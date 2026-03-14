<?php

namespace App\Filament\Resources\Editions;

use App\Filament\Resources\Editions\Schemas\EditionForm;
use App\Filament\Resources\Editions\Tables\EditionsTable;
use App\Models\Edition;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EditionResource extends Resource
{
    protected static ?string $model = Edition::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    // ✅ Must be UnitEnum|string|null — just ensure it's ?string like this:
    protected static string|\UnitEnum|null $navigationGroup = 'Newspaper';  

    public static function form(Schema $schema): Schema
    {
        return EditionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EditionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEditions::route('/'),
            'create' => Pages\CreateEdition::route('/create'),
            'edit' => Pages\EditEdition::route('/{record}/edit'),
        ];
    }
}