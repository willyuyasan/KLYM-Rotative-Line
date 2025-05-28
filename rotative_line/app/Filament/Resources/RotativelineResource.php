<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use App\Models\Rotativeline;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Fieldset;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput\Mask;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RotativelineResource\Pages;
use App\Filament\Resources\RotativelineResource\RelationManagers;

class RotativelineResource extends Resource
{
    protected static ?string $model = Rotativeline::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Lineas Rotativas';
    protected static ?string $modelLabel = 'Lineas Rotativas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Fieldset::make('Producto/Cliente')
                ->schema([
                    // ...
                    TextInput::make('product')
                        ->label('Producto')
                        ->required()
                        ->grow(false),

                    TextInput::make('number_line')
                        ->label('Numero producto')
                        ->required()
                        ->maxLength(7),

                    TextInput::make('issuer_tax_number')
                        ->label('Identificación cliente')
                        ->required()
                        ->maxLength(20)
                        ->grow(false),
        
                    TextInput::make('issuer_name')
                        ->label('Nombre cliente')
                        ->required()
                        ->maxLength(100)
                        ->grow(false),
                    ]),

                Fieldset::make('Detalles Producto')
                ->schema([

                    TextInput::make('value_to_issuer')
                        ->label('Valor del crédito')
                        ->required()
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->numeric()
                        ->suffix('$')
                        ->grow(false),

                    TextInput::make('discount_rate')
                        ->label('Tasa de interés')
                        ->required()
                        ->formatStateUsing(fn (string $state): string => number_format($state*100,2))
                        ->suffix('%(M.V.)')
                        ->numeric()
                        ->grow(false),
                    
                    TextInput::make('credit_term')
                        ->label('Termino del crédito')
                        ->required()
                        ->formatStateUsing(fn (string $state): string => number_format($state,0))
                        ->suffix('Dias')
                        ->integer()
                        ->grow(false),

                    TextInput::make('credit_periods')
                        ->label('Coutas generadas')
                        ->required()
                        ->formatStateUsing(fn (string $state): string => number_format($state,0))
                        ->integer()
                        ->grow(false),

                    DatePicker::make('disbursement_date')
                        ->label('Fecha desembolso')
                        ->required()
                        //->mask('99/99/9999')
                        //->placeholder('MM/DD/YYYY')
                        ->grow(false),

                    DatePicker::make('payment_date_with_extension')
                        ->label('Fecha vencimiento')
                        ->required()
                        //->mask('99/99/9999')
                        //->placeholder('MM/DD/YYYY')
                        ->grow(false),

                ])
                ->columns(3),

                Fieldset::make('Estado actual')
                ->schema([

                    TextInput::make('rotative_line_status')
                        ->label('Estado del producto')
                        ->required()
                        ->maxLength(100)
                        ->grow(false),

                    TextInput::make('quote')
                        ->label('Couta actual')
                        ->grow(false),

                    TextInput::make('today_debt')
                        ->label('Total deuda (HOY)')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->numeric()
                        ->suffix('$')
                        ->grow(false),
                    
                    TextInput::make('new_expected_payment')
                        ->label('Pago minimo')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->numeric()
                        ->suffix('$')
                        ->grow(false),

                    DatePicker::make('credit_term_end_date')
                        ->label('Próxima fecha de pago')
                        ->grow(false),
                ])
                ->columns(5),

                Fieldset::make('Balance actual')
                ->schema([

                    TextInput::make('capital_debt')
                        ->label('Capital en deuda')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->numeric()
                        ->suffix('$')
                        ->grow(false),

                    TextInput::make('interest_debt')
                        ->label('Interes en deuda')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->numeric()
                        ->suffix('$')
                        ->grow(false),

                    TextInput::make('default_charge_debt')
                        ->label('Mora en deuda')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->numeric()
                        ->suffix('$')
                        ->grow(false),

                ])
                ->columns(3),

                Fieldset::make('Reportes PDF')
                ->schema([

                    TextInput::make('pdf_download_url')
                        ->label('Estado del credito')
                        ->suffixAction(
                            Action::make('verEstadoDelCredito')
                                ->icon('heroicon-m-globe-alt')
                                ->requiresConfirmation()
                                ->url(function ($state) {
                                    return $state;
                                }, shouldOpenInNewTab: true)),
                                //->action(function ($state, $livewire) {
                                    //return redirect()->away($state);
                                    //return $livewire->js("window.open(\'{$state}\', \'_blank\');");
                                //})),

                    TextInput::make('pdf_download_url_pp')
                        ->label('Plan de pagos')
                        ->suffixAction(
                            Action::make('verEstadoDelCredito')
                                ->icon('heroicon-m-globe-alt')
                                ->requiresConfirmation()
                                ->url(function ($state) {
                                    return $state;
                                }, shouldOpenInNewTab: true)),

                    ])
                ->columns(2),

                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('product')
                    ->label('Producto')
                    //->description(fn ($record): string => new HtmlString($record->number_line.'<br>'.$record->factoring_operation_id))
                    ->description(fn ($record) => new HtmlString("<span class=\"text-gray\"> {$record->number_line}<br>{$record->factoring_operation_id}</span>"), 'below')
                    ->listWithLineBreaks()
                    ->limit(20)
                    ->grow(false)
                    ->size('sm')
                    ->copyable()
                    ->searchable(['product','number_line']),

                TextColumn::make('issuer_name')
                    ->label('Nombre cliente')
                    ->description(fn ($record): string => $record->issuer_tax_number)
                    ->limit(20)
                    ->grow(false)
                    ->copyable()
                    ->searchable(['issuer_name','issuer_tax_number']),
                
                TextColumn::make('disbursement_date')
                    ->label('Fecha de desembolso')
                    ->grow(false),

                TextColumn::make('value_to_issuer')
                    ->label('Valor del crédito')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),
                
                TextColumn::make('discount_rate')
                    ->label('Tasa de interés')
                    ->state(fn ($record): string => number_format($record->discount_rate*100,2).'%(M.V.)')
                    ->grow(false),

                TextColumn::make('credit_term')
                    ->label('Termino del credito (días)')
                    ->description(fn ($record): string => 'Coutas: '.$record->credit_periods)
                    ->limit(20)
                    ->grow(false),

                TextColumn::make('daily_step')
                    ->label('Tiempo de avance')
                    //->description(fn ($record): string => '(Cuota Actual: '.$record->quote.")\n (Dias Mora: ".$record->default_days.")")
                    ->state(fn ($record): array => [
                        'Dias Trascurridos: '.$record->daily_step,
                        'Cuota Actual: '.$record->quote,
                        'Dias Mora: '.$record->default_days,
                    ])
                    ->listWithLineBreaks()
                    //->formatStateUsing(fn ($state): HtmlString => new HtmlString($state))
                    ->limit(30)
                    ->grow(false),

                TextColumn::make('capital_debt')
                    ->label('Capital en deuda')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),

                TextColumn::make('rotative_line_status')
                    ->label('Estado actual')
                    ->grow(false),
                
                TextColumn::make('new_expected_payment')
                    ->label('Minimo pago esperado')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),

                TextColumn::make('credit_term_end_date')
                    ->label('Próxima fecha de pago')
                    ->grow(false),

                TextColumn::make('due_capital_today')
                    ->label('Reporte Capital')
                    ->state(fn ($record): array => [
                        'Esperado: '.number_format($record->value_to_issuer,0),
                        'Pagado: '.number_format($record->capital_paid,0),
                        'En Deuda: '.number_format($record->capital_debt,0),
                    ])
                    ->listWithLineBreaks()
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),

                TextColumn::make('real_interest_cum')
                    ->label('Reporte Intereses')
                    ->state(fn ($record): array => [
                        'Generado: '.number_format($record->real_interest_cum,0),
                        'Pagado: '.number_format($record->interest_paid,0),
                        'En Deuda: '.number_format($record->interest_debt,0),
                    ])
                    ->listWithLineBreaks()
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),

                TextColumn::make('real_default_charge_cum')
                    ->label('Reporte Mora')
                    ->state(fn ($record): array => [
                        'Generado: '.number_format($record->real_default_charge_cum,0),
                        'Pagado: '.number_format($record->default_charge_paid,0),
                        'En Deuda: '.number_format($record->default_charge_debt,0),
                    ])
                    ->listWithLineBreaks()
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),

                TextColumn::make('today_debt')
                    ->label('Pago para cancelación')
                    ->grow(false)
                    ->numeric(decimalPlaces: 0),

            ])

            ->defaultSort(function (Builder $query): Builder {
                return $query
                ->orderBy('rl_active_id','desc')
                ->orderBy('capital_debt','desc');
                })

            ->filters([
                //
                SelectFilter::make('rl_active_id')
                ->label('Esta vigente?')
                ->options(fn (): array => Rotativeline::query()->pluck('rl_active_id','rl_active_id')->all())
                ->default(true),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                //Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            RelationManagers\RlquotesRelationManager::class,
            RelationManagers\RlinterestsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRotativelines::route('/'),
            'create' => Pages\CreateRotativeline::route('/create'),
            'view' => Pages\ViewRotativeline::route('/{record}'),
            'edit' => Pages\EditRotativeline::route('/{record}/edit'),
        ];
    }
}
