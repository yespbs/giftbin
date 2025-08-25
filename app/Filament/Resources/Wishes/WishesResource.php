<?php

namespace App\Filament\Resources\Wishes;

use App\Filament\Resources\Wishes\Pages\CreateWishes;
use App\Filament\Resources\Wishes\Pages\EditWishes;
use App\Filament\Resources\Wishes\Pages\ListWishes;
use App\Filament\Resources\Wishes\Schemas\WishesForm;
use App\Filament\Resources\Wishes\Tables\WishesTable;
use App\Models\Wish;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WishesResource extends Resource
{
    protected static ?string $model = Wish::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    public static function form(Schema $schema): Schema
    {
        return WishesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WishesTable::configure($table);
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
            'index' => ListWishes::route('/'),
            'create' => CreateWishes::route('/create'),
            'edit' => EditWishes::route('/{record}/edit'),
        ];
    }
}
