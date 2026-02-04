<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterResource\Pages;
use App\Models\Newsletter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
// 👇 Email Classes Import
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomEmail;
use Filament\Notifications\Notification;

class NewsletterResource extends Resource
{
    protected static ?string $model = Newsletter::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-m-envelope'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Joined On')
                    ->sortable(),
            ])
            
            // 👇 1. BUTTON: "Write Email to ALL" (Table ke upar)
            ->headerActions([
                Tables\Actions\Action::make('send_to_all')
                    ->label('Write Email to Everyone')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->form([
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->label('Email Subject')
                            ->placeholder('e.g. Happy Republic Day!'),
                        
                        Forms\Components\RichEditor::make('content')
                            ->required()
                            ->label('Message')
                            ->placeholder('Write your message here...')
                    ])
                    ->action(function (array $data) {
                        // Saare Emails nikalo
                        $emails = Newsletter::pluck('email');

                        if ($emails->isEmpty()) {
                            Notification::make()->title('No subscribers yet!')->warning()->send();
                            return;
                        }

                        // Loop chala kar sabko bhejo
                        foreach ($emails as $email) {
                            Mail::to($email)->send(new CustomEmail($data['subject'], $data['content']));
                        }

                        Notification::make()->title('Email sent to ' . $emails->count() . ' people!')->success()->send();
                    }),
            ])

            ->actions([
                // 👇 2. BUTTON: "Send to This User" (Har row ke aage)
                Tables\Actions\Action::make('send_email')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->label('')
                    ->tooltip('Send Email to this user')
                    ->form([
                        Forms\Components\TextInput::make('subject')->required(),
                        Forms\Components\RichEditor::make('content')->required()
                    ])
                    ->action(function (Newsletter $record, array $data) {
                        Mail::to($record->email)->send(new CustomEmail($data['subject'], $data['content']));
                        Notification::make()->title('Email sent successfully!')->success()->send();
                    }),

                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListNewsletters::route('/'),
            'create' => Pages\CreateNewsletter::route('/create'),
            'edit' => Pages\EditNewsletter::route('/{record}/edit'),
        ];
    }
}