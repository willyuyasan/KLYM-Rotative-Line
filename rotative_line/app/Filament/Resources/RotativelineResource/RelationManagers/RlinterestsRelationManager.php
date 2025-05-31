<?php

namespace App\Filament\Resources\RotativelineResource\RelationManagers;

use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Query\Builder;
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
                    ->numeric(decimalPlaces: 0)
                    ->summarize(Summarizer::make()
                        ->label('')
                        ->using(fn (Builder $query): string => $query->latest('id')->pluck('capital_debt')[0])
                        ->numeric(decimalPlaces: 0)
                    ),

                TextColumn::make('discount_rate')
                    ->label('Tasa de interes (M.V.)')
                    ->grow(false)
                    ->state(fn ($record): string => number_format($record->discount_rate*100,2).'%'),

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
                    ->state(fn ($record): string => number_format($record->default_rate*100,2).'%'),
                
                TextColumn::make('incurred_days')
                    ->label('Dias de cálculo')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),

                TextColumn::make('generated_interest')
                    ->label('Intereses generados')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0)
                    ->summarize(Sum::make()
                        ->label('') 
                        ->numeric(decimalPlaces: 0)
                    ),

                TextColumn::make('incomming_interest_debt')
                    ->label('Intereses acumulados')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),

                TextColumn::make('interest_paid')
                    ->label('Intereses pagados')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0)
                    ->summarize(Sum::make()
                        ->label('') 
                        ->numeric(decimalPlaces: 0)
                    ),

                TextColumn::make('interest_debt')
                    ->label('Intereses en deuda')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0)
                    ->summarize(Summarizer::make()
                        ->label('')
                        ->using(fn (Builder $query): string => $query->latest('id')->pluck('interest_debt')[0])
                        ->numeric(decimalPlaces: 0)
                    ),

                TextColumn::make('generated_default_interest')
                    ->label('Intereses generados (mora)')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0)
                    ->summarize(Sum::make()
                        ->label('') 
                        ->numeric(decimalPlaces: 0)
                    ),

                TextColumn::make('incomming_default_debt')
                    ->label('Intereses acumulados (mora)')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),

                TextColumn::make('default_charge_paid')
                    ->label('Intereses pagados (mora)')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0)
                    ->summarize(Sum::make()
                        ->label('') 
                        ->numeric(decimalPlaces: 0)
                    ),

                TextColumn::make('default_charge_debt')
                    ->label('Intereses en deuda (mora)')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0)
                    ->summarize(Summarizer::make()
                        ->label('')
                        ->using(fn (Builder $query): string => $query->latest('id')->pluck('default_charge_debt')[0])
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

/*
->summarize(
    Average::make()
    ->label('') 
    ->query(fn (Builder $query) => $query->where('quote_adj','max("quote_adj")')),
), 

->summarize(Summarizer::make()            
                        ->using(function (Table $table) {
                            return $table->getRecords()->last()->pluck('interest_debt');                
                        })),

->summarize(Summarizer::make()
                        ->label('')
                        ->using(fn (Builder $query): string => $query->latest('id')->min('interest_debt'))
                    ),
*/