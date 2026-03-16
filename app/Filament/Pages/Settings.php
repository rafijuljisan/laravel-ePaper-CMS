<?php
// app/Filament/Pages/Settings.php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog;
    protected static string|\UnitEnum|null $navigationGroup = 'System';
    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SystemSetting::instance()->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('সাইট পরিচয়')
                    ->description('Site name, logo and tagline shown in the header.')
                    ->columns(2)
                    ->components([
                        TextInput::make('site_name')
                            ->label('Site Name (Bengali)')
                            ->required(),

                        TextInput::make('site_tagline')
                            ->label('Tagline / Subtitle'),

                        FileUpload::make('site_logo')
                            ->label('Site Logo')
                            ->image()
                            ->disk('public')
                            ->directory('settings')
                            ->helperText('Shown instead of text logo if uploaded.'),

                        FileUpload::make('site_favicon')
                            ->label('Favicon')
                            ->image()
                            ->disk('public')
                            ->directory('settings'),
                    ]),

                Section::make('যোগাযোগ তথ্য')
                    ->description('Contact details shown in the footer.')
                    ->columns(2)
                    ->components([
                        TextInput::make('editor_name')
                            ->label('Editor Name (বার্তা সম্পাদক)'),

                        TextInput::make('site_email')
                            ->label('Email Address')
                            ->email(),

                        TextInput::make('site_phone')
                            ->label('Phone Number'),

                        TextInput::make('site_address')
                            ->label('Office Address'),
                    ]),

                Section::make('সোশ্যাল মিডিয়া')
                    ->columns(3)
                    ->components([
                        TextInput::make('facebook_url')
                            ->label('Facebook URL')
                            ->url()->placeholder('https://facebook.com/...'),

                        TextInput::make('twitter_url')
                            ->label('Twitter / X URL')
                            ->url()->placeholder('https://twitter.com/...'),

                        TextInput::make('youtube_url')
                            ->label('YouTube URL')
                            ->url()->placeholder('https://youtube.com/...'),
                    ]),

                Section::make('বিজ্ঞাপন ব্যানার')
                    ->description('Header and sidebar ad banners.')
                    ->columns(2)
                    ->components([
                        FileUpload::make('header_ad_image')
                            ->label('Header Ad Image (468×60)')
                            ->image()
                            ->disk('public')
                            ->directory('ads'),

                        TextInput::make('header_ad_url')
                            ->label('Header Ad Link')
                            ->url(),

                        FileUpload::make('sidebar_ad1_image')
                            ->label('Sidebar Ad 1 Image')
                            ->image()
                            ->disk('public')
                            ->directory('ads'),

                        TextInput::make('sidebar_ad1_url')
                            ->label('Sidebar Ad 1 Link')
                            ->url(),

                        FileUpload::make('sidebar_ad2_image')
                            ->label('Sidebar Ad 2 Image')
                            ->image()
                            ->disk('public')
                            ->directory('ads'),

                        TextInput::make('sidebar_ad2_url')
                            ->label('Sidebar Ad 2 Link')
                            ->url(),
                    ]),

                Section::make('ePaper Retention Policy')
                    ->components([
                        TextInput::make('archive_retention_days')
                            ->label('Archive Retention (Days)')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(365)
                            ->helperText('Old editions are deleted after this many days.'),
                    ]),

            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $settings = SystemSetting::instance();
        $settings->update($this->form->getState());

        Notification::make()
            ->title('Settings Saved')
            ->success()
            ->send();
    }
}