<?php

namespace App\Filament\Resources\RotativelineResource\RelationManagers;

use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RlpaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'rlpayments';
    protected static ?string $title = 'Detalle pagos';

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('quote')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('quote')
            ->columns([
                TextColumn::make('abono')
                    ->label('Abono')
                    ->rowIndex()
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),

                TextColumn::make('quote')
                    ->label('Cuota'),

                TextColumn::make('credit_dates')
                    ->label('Fecha de pago')
                    ->date('Y-m-d')
                    ->grow(false),

                TextColumn::make('payment_amount')
                    ->label('Valor total del pago')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0)
                    ->summarize(Sum::make() 
                        ->label('') 
                        ->numeric(decimalPlaces: 0)
                    ),

                TextColumn::make('payment_to_capital')
                    ->label('Pago a capital')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0)
                    ->summarize(Sum::make()
                        ->label('') 
                        ->numeric(decimalPlaces: 0)
                    ),

                TextColumn::make('payment_to_interest')
                    ->label('Pago a intereses')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0)
                    ->summarize(Sum::make()
                        ->label('') 
                        ->numeric(decimalPlaces: 0)
                    ),

                TextColumn::make('payment_to_default')
                    ->label('Pago a mora')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0)
                    ->summarize(Sum::make()
                        ->label('') 
                        ->numeric(decimalPlaces: 0)
                    ),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated(false);
    }
}
