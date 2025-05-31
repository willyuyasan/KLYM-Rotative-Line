<?php

namespace App\Filament\Widgets;

use App\Models\Rotativeline;
use App\Models\RotativelineClient;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getColumns(): int {
        return 3;
    }

    protected function getStats(): array
    {
        $products_info = $this->products_info();

        return [
            //

            Stat::make("Total productos activos (RL/KTQ):", $products_info['active_products'])
                ->description("Clientes activos: {$products_info['active_clients']}")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total deuda capital:', $products_info['capital_debt'])
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('danger'),

            Stat::make('Fecha de Actualización:', $products_info['updated_at']),
        ];
    }

    public function products_info()
    {
        $active_clients = RotativelineClient::query()->where('active_client',true)->count();
        $active_products = RotativelineClient::query()->where('active_client',true)->sum('active_rls');

        $capital_debt = RotativelineClient::query()->where('active_client',true)->sum('capital_debt');
        $capital_debt = number_format($capital_debt,0);

        $updated_at = Rotativeline::query()->where('rl_active_id',true)->max('updated_at');

        return [
            'active_clients'=>$active_clients,
            'active_products'=>$active_products,
            'capital_debt'=>$capital_debt,
            'updated_at'=>$updated_at,
        ];
    }
}
