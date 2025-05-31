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

class RlquotesRelationManager extends RelationManager
{
    protected static string $relationship = 'rlquotes';
    protected static ?string $title = 'Estado cuotas';


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

                TextColumn::make('quote')
                    ->label('Couta')
                    ->state(function ($record):string {
                        if($record->quote === 9999) {
                            return 'Total';
                        } else {
                            return $record->quote;
                        }}
                ),

                TextColumn::make('credit_term_dates')
                    ->label('Fecha inicio')
                    ->date('Y-m-d')
                    ->grow(false),

                TextColumn::make('credit_term_end_date')
                    ->label('Fecha fin')
                    ->date('Y-m-d')
                    ->grow(false),

                TextColumn::make('capital_quote_amount')
                    ->label('Capital esperado')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),

                TextColumn::make('capital_paid')
                    ->label('Capital pagado')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),

                    TextColumn::make('payments')
                    ->label('Total pagos')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),

                TextColumn::make('quote_status')
                    ->label('Estado de la cuota')
                    ->grow(false),

                TextColumn::make('quote_default_days')
                    ->label('Dias en mora')
                    ->grow(false),

                TextColumn::make('due_capital_today')
                    ->label('Reporte Capital')
                    ->state(fn ($record): array => [
                        'Esperado: '.number_format($record->capital_quote_amount,0),
                        'Pagado: '.number_format($record->capital_paid,0),
                        'En Deuda: '.number_format($record->capital_debt,0),
                    ])
                    ->listWithLineBreaks()
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),

                TextColumn::make('real_interest_cum')
                    ->label('Reporte Intereses')
                    ->state(fn ($record): array => [
                        'Generado: '.number_format($record->generated_interest,0),
                        'Pagado: '.number_format($record->interest_paid,0),
                        'En Deuda: '.number_format($record->interest_debt,0),
                    ])
                    ->listWithLineBreaks()
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),
                
                TextColumn::make('real_default_charge_cum')
                    ->label('Reporte Mora')
                    ->state(fn ($record): array => [
                        'Generado: '.number_format($record->generated_default_interest,0),
                        'Pagado: '.number_format($record->default_paid,0),
                        'En Deuda: '.number_format($record->default_interest_debt,0),
                    ])
                    ->listWithLineBreaks()
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
                //Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated(false);
    }
}
