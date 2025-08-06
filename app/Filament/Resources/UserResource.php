<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->unique(
                        table: User::class,
                        column: 'email',
                        ignoreRecord: true
                    )
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('email_verified_at'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->autocomplete('new-password')
                    ->minLength(8)
                    ->nullable()
                    ->revealable()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn ($record) => $record === null),
                Forms\Components\FileUpload::make('avatar')
                    ->label('Profile Avatar')
                    ->image()
                    ->disk('public')
                    ->directory('uploads/avatar')
                    ->visibility('public')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('url')
                    ->label('Profile URL')
                    ->prefix('@')
                    ->afterStateUpdated(fn ($state, callable $set) =>
                    $set('url', str_replace(' ', '_', strtolower($state)))
                    )
                    ->unique(
                        table: User::class,
                        column: 'url',
                        ignoreRecord: true
                    )
                    ->maxLength(255),
                Forms\Components\Select::make('role')
                    ->required()
                    ->options([
                        'Admin' => 'Admin',
                        'User' => 'User',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->getStateUsing(function ($record) {
                        return $record->avatar
                            ?: 'https://ui-avatars.com/api/?name=' . urlencode(substr($record->name ?? 'U', 0, 1)) . '&background=000000&color=fff';
                    })
                    ->width(35)
                    ->height(35)
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->formatStateUsing(fn($state) => $state ?: 'N/A')
                    ->limit(15)
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\SelectColumn::make('role')
                    ->width(100)
                    ->options([
                        'Admin' => 'Admin',
                        'User' => 'User',
                    ])
                    ->default(fn ($record) => $record?->status ?? 'Active')
                    ->inline()
                    ->sortable()
                    ->searchable()
                    ->afterStateUpdated(function ($record, $state) {
                        Notification::make()
                            ->body("Role {$record->role} changed to {$state}.")
                            ->success()
                            ->send();
                    }),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Filter by Role')
                    ->options([
                        'Admin' => 'Admin',
                        'User' => 'User',
                    ])
                    ->attribute('role'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
