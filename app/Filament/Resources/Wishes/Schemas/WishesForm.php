<?php

namespace App\Filament\Resources\Wishes\Schemas;

use Filament\Components\Select;
use Filament\Components\TextInput;
use Filament\Components\Textarea;
use Filament\Schemas\Schema;

class WishesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('event_id')
                    ->label('Event')
                    ->relationship('event', 'title')
                    ->required(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->numeric()
                    ->prefix('$'),
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('url')
                    ->url()
                    ->maxLength(255),
                TextInput::make('image_url')
                    ->url()
                    ->maxLength(255),
                Select::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ])
                    ->default('medium'),
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'fulfilled' => 'Fulfilled',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('active')
                    ->required(),
            ]);
    }
}
