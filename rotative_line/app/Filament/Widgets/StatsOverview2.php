<?php

namespace App\Filament\Widgets;

use App\Models\Rotativeline;
use App\Models\RotativelineClient;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview2 extends BaseWidget
{
    protected static ?int $sort = 2;
    protected ?string $heading = 'Reporte de productos RL y KTQ';

    protected function getColumns(): int {
        return 3;
    }

    protected function getStats(): array
    {
        $products_info = $this->products_info2();

        return [
            //
            Stat::make("Total productos vigentes:", $products_info['rls_vigentes'])
                ->description("deuda: {$products_info['uptodate_debt']}")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make("Total productos mora:", $products_info['rls_mora'])
                ->description("deuda: {$products_info['default_debt']}")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('danger'),
            
            Stat::make("Indice de cartera vencida:", "{$products_info['icv']} %")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('danger'),
        ];
    }

    public function products_info2()
    {

        $total_debt = Rotativeline::query()->where('rl_active_id',true)->sum('capital_debt');

        $rls_vigentes = Rotativeline::query()->whereRaw("rotative_line_status in ('ok', 'near to cut date') and rl_active_id = True")->count();

        $uptodate_debt = Rotativeline::query()->whereRaw("rotative_line_status in ('ok', 'near to cut date') and rl_active_id = True")->sum('capital_debt');

        $rls_mora = Rotativeline::query()->whereRaw("rotative_line_status not in ('ok', 'near to cut date') and rl_active_id = True")->count();

        $default_debt = Rotativeline::query()->whereRaw("rotative_line_status not in ('ok', 'near to cut date') and rl_active_id = True")->sum('capital_debt');
        

        $icv = number_format(($default_debt/$total_debt)*100,2);
        
        $total_debt = number_format($total_debt,0);
        $uptodate_debt = number_format($uptodate_debt,0);
        $default_debt = number_format($default_debt,0);

        return [
            'rls_vigentes'=>$rls_vigentes,
            'uptodate_debt'=>$uptodate_debt,
            'rls_mora'=>$rls_mora,
            'default_debt'=>$default_debt,
            'icv'=>$icv,
        ];
    }
}
