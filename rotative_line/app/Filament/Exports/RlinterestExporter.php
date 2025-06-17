<?php

namespace App\Filament\Exports;

use App\Models\Rlinterest;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class RlinterestExporter extends Exporter
{
    protected static ?string $model = Rlinterest::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('factoring_operation_id'),
            ExportColumn::make('quote_adj')->label('Cuota'),
            ExportColumn::make('init_date')->label('Fecha inicio'),
            ExportColumn::make('end_date')->label('Fecha fin'),
            ExportColumn::make('interest_event_description')->label('Evento'),
            ExportColumn::make('incurred_days')->label('Dias de calculo'),
            ExportColumn::make('payment_amount')->label('Pago del cliente'),
            ExportColumn::make('payment_to_capital')->label('Abono a capital'),
            ExportColumn::make('capital_debt')->label('Capital en deuda'),
            
            ExportColumn::make('monto_base')
                ->label('Monto base calculo de interes')
                ->state(fn ($record): string => number_format($record->payment_to_capital + $record->capital_debt,2)),

            ExportColumn::make('discount_rate')->label('Tasa de interes (M.V.)'),
            ExportColumn::make('generated_interest')->label('Intereses generados'),
            ExportColumn::make('incomming_interest_debt')->label('Intereses acumulados'),
            ExportColumn::make('interest_paid')->label('Intereses pagados'),
            ExportColumn::make('interest_debt')->label('Intereses en deuda'),

            ExportColumn::make('default_amount')->label('Monto base calculo mora'),
            ExportColumn::make('default_rate')->label('Tasa de mora (Anual)'),
            ExportColumn::make('generated_default_interest')->label('Intereses generados (mora)'),
            ExportColumn::make('incomming_default_debt')->label('Intereses acumulados (mora)'),
            ExportColumn::make('default_charge_paid')->label('Intereses pagados (mora)'),
            ExportColumn::make('default_charge_debt')->label('Intereses en deuda (mora)'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your rlinterest export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
