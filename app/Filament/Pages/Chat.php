<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Chat extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static string $view = 'filament.pages.chat';
    protected static ?string $navigationLabel = 'Chats';
    protected static ?int $navigationSort = 5;
    public function getTitle(): string 
    {
        return '';
    }
}
