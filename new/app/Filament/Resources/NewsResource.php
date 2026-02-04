<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
// 👇 Email bhejne ke liye zaroori classes
use Illuminate\Support\Facades\Mail;
use App\Models\Newsletter;
use App\Mail\NewsBroadcast;
use Filament\Notifications\Notification;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                        $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                
                // 👇 Dynamic Category Select
                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, $state) => $set('slug', Str::slug($state))),
                        
                        Forms\Components\TextInput::make('slug')
                            ->required(),
                    ]),

                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->directory('news-images'),
                
                Forms\Components\RichEditor::make('content')->columnSpanFull(),
                
                Forms\Components\Toggle::make('is_breaking'),
                
                Forms\Components\Select::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published'])
                    ->default('draft'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable()
                    ->badge()
                    ->color('warning'),
                Tables\Columns\IconColumn::make('is_breaking')->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

                // 👇👇👇 YEH RAHA BROADCAST BUTTON (Naya Feature) 👇👇👇
                Tables\Actions\Action::make('broadcast')
                    ->label('Send to All')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success') // Green button
                    ->requiresConfirmation()
                    ->modalHeading('Send Newsletter')
                    ->modalDescription('Are you sure you want to email this news to ALL subscribers?')
                    ->modalSubmitActionLabel('Yes, Send it!')
                    ->action(function (News $record) {
                        // 1. Saare subscribers nikalo
                        $subscribers = Newsletter::pluck('email');

                        // 2. Sabko mail bhejo
                        foreach ($subscribers as $email) {
                            Mail::to($email)->send(new NewsBroadcast($record));
                        }

                        // 3. Success Notification
                        Notification::make()
                            ->title('Newsletter Sent Successfully!')
                            ->success()
                            ->send();
                    }),
                // 👆👆👆 BROADCAST BUTTON KHATAM 👆👆👆
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}