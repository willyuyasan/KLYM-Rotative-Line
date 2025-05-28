<?php

namespace App\Filament\Resources\RotativelineResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class RlinterestsRelationManager extends RelationManager
{
    protected static string $relationship = 'rlinterests';
    protected static ?string $title = 'Detalle intereses';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('quote_adj')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('quote_adj')
            ->columns([
                TextColumn::make('quote_adj'),

                TextColumn::make('init_date')
                    ->label('Fecha inicio')
                    ->date('Y-m-d')
                    ->grow(false),

                TextColumn::make('end_date')
                    ->label('Fecha fin')
                    ->date('Y-m-d')
                    ->grow(false),

                TextColumn::make('credit_term_end_date')
                    ->label('Fecha corte')
                    ->date('Y-m-d')
                    ->grow(false),

                TextColumn::make('capital_debt')
                    ->label('Capital en deuda')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),

                TextColumn::make('discount_rate')
                    ->label('Tasa de interes (M.V.)')
                    ->grow(false)
                    ->numeric(decimalPlaces: 3),

                TextColumn::make('payment_to_capital')
                    ->label('Capital pagado')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),

                TextColumn::make('default_amount')
                    ->label('Capital en mora')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),

                TextColumn::make('default_rate')
                    ->label('Tasa de mora (Anual)')
                    ->grow(false)
                    ->numeric(decimalPlaces: 3),
                
                TextColumn::make('incurred_days')
                    ->label('Dias de cálculo')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),

                TextColumn::make('generated_interest')
                    ->label('Intereses corrientes')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),

                TextColumn::make('generated_default_interest')
                    ->label('Intereses mora')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),


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
