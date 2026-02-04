<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    
    protected static ?string $navigationLabel = 'Inbox Messages';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Sender Details')
                    ->schema([
                        // Row 1: Name, Email, Mobile
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->readonly()
                                    ->prefixIcon('heroicon-m-user'),
                                
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->readonly()
                                    ->prefixIcon('heroicon-m-envelope'),

                                Forms\Components\TextInput::make('mobile') // ✅ NEW
                                    ->readonly()
                                    ->prefixIcon('heroicon-m-phone'),
                            ]),

                        // Row 2: Location & Date
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('city') // ✅ NEW
                                    ->readonly()
                                    ->prefixIcon('heroicon-m-map-pin'),

                                Forms\Components\TextInput::make('state') // ✅ NEW
                                    ->readonly()
                                    ->prefixIcon('heroicon-m-map'),

                                Forms\Components\DateTimePicker::make('created_at')
                                    ->label('Received On')
                                    ->readonly(),
                            ]),

                        // Row 3: Message (Full Width)
                        Forms\Components\Textarea::make('message')
                            ->readonly()
                            ->columnSpanFull()
                            ->rows(5),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. NAME
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-m-user'),

                // 2. MOBILE (New)
                Tables\Columns\TextColumn::make('mobile') // ✅ NEW
                    ->icon('heroicon-m-phone')
                    ->searchable()
                    ->toggleable(), 

                // 3. CITY (New)
                Tables\Columns\TextColumn::make('city') // ✅ NEW
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                // 4. EMAIL
                Tables\Columns\TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->copyable()
                    ->copyMessage('Email copied')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true), // Default hide kiya taaki table crowded na ho

                // 5. MESSAGE
                Tables\Columns\TextColumn::make('message')
                    ->limit(40)
                    ->tooltip(fn (Model $record): string => $record->message)
                    ->wrap(),

                // 6. DATE
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Received')
                    ->since(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),   // View button
                Tables\Actions\DeleteAction::make(), // Delete button
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
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
        ];
    }
}