<?php

namespace App\Filament\Resources\Editions\Tables;

use App\Models\Edition;
use Filament\Actions\Action;                    // ✅ Filament\Actions, NOT Filament\Tables\Actions
use Filament\Actions\BulkActionGroup;           // ✅
use Filament\Actions\DeleteBulkAction;          // ✅
use Filament\Actions\EditAction;                // ✅
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Jobs\ProcessEditionPdf;

class EditionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('edition_date')
                    ->date('F j, Y')
                    ->sortable(),
                    
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'processing' => 'warning',
                        'published' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                    
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
                
                Action::make('process')
                    ->label('Process PDF')
                    ->icon(Heroicon::OutlinedCog)
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Process ePaper PDF')
                    ->modalDescription('This will run in the background to convert the PDF into individual page images. Are you sure you want to start?')
                    ->action(function (Edition $record) {
    
                        // Update status to prevent double-clicking
                        $record->update(['status' => 'processing']);
                        
                        // Dispatch the Background Job!
                        ProcessEditionPdf::dispatch($record);
                    
                        Notification::make()
                            ->title('Processing Started')
                            ->body('The PDF is being converted to images in the background. Check back in a few minutes.')
                            ->success()
                            ->send();
                    })
                    ->hidden(fn (Edition $record) => $record->status !== 'draft'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('edition_date', 'desc');
    }
}