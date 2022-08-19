<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscountResource\Pages;
use App\Filament\Resources\DiscountResource\RelationManagers;
use App\Models\Discount;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class DiscountResource extends Resource
{
    protected static ?string $model = Discount::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options(['fixed' => 'Fixed amount off', 'percent' => 'Percentage off'])
                    ->required(),
                Forms\Components\TextInput::make('value')
                    ->required(),
                Forms\Components\Repeater::make('conditions')
                    ->relationship('conditions')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required(),
                        Forms\Components\Select::make('type')
                            ->options(['user_id' => 'User ID', 'customer_id' => 'Customer ID'])
                            ->reactive()
                            ->required(),
                        Forms\Components\Fieldset::make('configuration')
                            ->schema(function (\Closure $get) {
                                $fields = [];

                                if ($get('type') === 'customer_id') {
                                    $fields[] = Select::make('customer_id')
                                        ->label('Customer')
                                        ->searchable()
                                        ->getSearchResultsUsing(fn(string $search) => Customer::where('title', 'like', "%{$search}%")->limit(50)->pluck('title', 'id'))
                                        ->getOptionLabelUsing(fn($value): ?string => Customer::find($value)?->title)
                                        ->required();
                                }

                                if ($get('type') === 'user_id') {
                                    $fields[] = Select::make('user_id')
                                        ->label('User')
                                        ->searchable()
                                        ->getSearchResultsUsing(fn(string $search) => User::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id'))
                                        ->getOptionLabelUsing(fn($value): ?string => User::find($value)?->name)
                                        ->required();
                                }

                                $fields[] = Toggle::make('negate');
                                return $fields;
                            }),
                    ])
                    ->columnSpan(2)
                    ->createItemButtonLabel('Add condition'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListDiscounts::route('/'),
            'create' => Pages\CreateDiscount::route('/create'),
            'edit' => Pages\EditDiscount::route('/{record}/edit'),
        ];
    }
}
