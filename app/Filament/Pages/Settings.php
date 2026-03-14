<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

// ✅ Correct v5.3 Namespaces
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section; // Layout goes to Schemas
use Filament\Forms\Components\TextInput; // Inputs go to Forms

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedCog;
    
    protected static string | \UnitEnum | null $navigationGroup = 'System';
    
    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = SystemSetting::firstOrCreate([], ['archive_retention_days' => 7]);
        $this->form->fill($settings->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('ePaper Retention Policy')
                    ->description('Configure how long old newspaper editions are kept before being permanently deleted from the server.')
                    ->components([
                        TextInput::make('archive_retention_days')
                            ->label('Archive Retention (Days)')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(365)
                            ->helperText('Example: 7 days. After this period, the background cleanup job will permanently delete the edition and its images to save cPanel storage.'),
                    ])
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $settings = SystemSetting::first();
        $settings->update($this->form->getState());

        Notification::make()
            ->title('Settings Saved')
            ->success()
            ->send();
    }
}