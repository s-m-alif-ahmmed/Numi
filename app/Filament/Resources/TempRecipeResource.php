<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TempRecipeResource\Pages;
use App\Filament\Resources\TempRecipeResource\RelationManagers;
use App\Models\TempRecipe;
use App\Services\Spoonacular\SpoonacularApiService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class TempRecipeResource extends Resource
{
    protected static ?string $model = TempRecipe::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $navigationGroup = 'Imports';

    protected static ?string $modelLabel = 'Import Recipes';

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('M j, Y g:i A')),
                Tables\Columns\TextColumn::make('recipe_api_id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image_url')
                    ->getStateUsing(function ($record) {
                        return $record->image_url;
                    })
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->limit(40)
                    ->searchable()
                    ->weight('bold')
                    ->wrap(),
                Tables\Columns\TextColumn::make('category')
                    ->sortable(),
//                Tables\Columns\TextColumn::make('recipe.recipe_api_id')
//                    ->label('Import Status')
//                    ->formatStateUsing(function ($record) {
//                        return \App\Models\Recipe::where('recipe_api_id', $record->recipe_api_id)->exists()
//                            ? 'Imported'
//                            : 'Not Imported';
//                    })
//                    ->badge()
//                    ->default('Not Imported')
//                    ->color(function ($record) {
//                        return \App\Models\Recipe::where('recipe_api_id', $record->recipe_api_id)->exists()
//                            ? 'success'
//                            : 'danger';
//                    })
//                    ->icon(function ($record) {
//                        return \App\Models\Recipe::where('recipe_api_id', $record->recipe_api_id)->exists()
//                            ? 'heroicon-o-check-circle'
//                            : 'heroicon-o-x-circle';
//                    }),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->color('info')
                    ->slideOver(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger'),
                    Tables\Actions\BulkAction::make('import')
                        ->label('Import')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Import Selected Recipes')
                        ->modalDescription('Are you sure you want to import the selected products? Successful imports will be removed from the temporary products list.')
                        ->modalSubmitActionLabel('Yes, Import')
                        ->action(function (Collection $records, SpoonacularApiService $service) {
                            try {
                                $ids = $records->pluck('recipe_api_id')->toArray();
                                $result = $service->importProductData($ids);

                                if ($result['status']) {
                                    Notification::make()
                                        ->title('Import Successful')
                                        ->body($result['items'])
                                        ->success()
                                        ->send();

                                    return;
                                }

                                Notification::make()
                                    ->title('Import Failed')
                                    ->body($result['message'] ?? 'Unknown error occurred')
                                    ->danger()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Import Failed')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();

                                return [
                                    'status' => false,
                                    'message' => $e->getMessage(),
                                ];
                            } catch (GuzzleException $e) {
                                Notification::make()
                                    ->title('Import Failed')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();

                                return [
                                    'status' => false,
                                    'message' => $e->getMessage(),
                                ];
                            }
                        }),
                ])
                    ->button()
                    ->label('Bulk Actions')
                    ->color('primary'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Recipe')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ImageEntry::make('image_url')
                                    ->columnSpan(1)
                                    ->extraAttributes(['class' => 'object-cover h-48 w-full rounded-lg']),

                                TextEntry::make('title')
                                    ->columnSpan(2)
                                    ->weight('bold')
                                    ->size(TextEntry\TextEntrySize::Large),
                                TextEntry::make('recipe_api_id')
                                    ->weight('bold')
                                    ->size(TextEntry\TextEntrySize::Large),
                                TextEntry::make('description')
                                    ->columnSpan(3)
                                    ->markdown(),
                            ]),
                    ]),
                Section::make('Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('preparation_time'),
                                TextEntry::make('cooking_time'),
                                TextEntry::make('total_ready_time'),
                                TextEntry::make('servings'),
                                TextEntry::make('calories'),
                                TextEntry::make('protein'),
                                TextEntry::make('fat'),
                                TextEntry::make('carbs'),
                                TextEntry::make('instruction')
                                    ->columnSpan(3)
                                    ->markdown(),
                            ]),
                    ]),

                Section::make('Import Status')
                    ->schema([
                        TextEntry::make('recipe.recipe_api_id')
                            ->label('Import Status')
                            ->formatStateUsing(fn ($record) => $record->recipe_api_id ? 'Recipe Imported' : 'Recipe Not Imported')
                            ->badge()
                            ->color(fn ($record) => $record->recipe_api_id ? 'success' : 'danger')
                            ->icon(fn ($record) => $record->recipe_api_id ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                            ->size(TextEntry\TextEntrySize::Large)->default('Not Imported'),
                        TextEntry::make('created_at')
                            ->label('Import Date')
                            ->dateTime(),
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
            'index' => Pages\ListTempRecipes::route('/'),
        ];
    }
}
