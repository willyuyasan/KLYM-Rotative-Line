<?php

namespace App\Filament\Widgets;

use App\Models\Disbursementmonth;
use Filament\Widgets\ChartWidget;

class PaymentsChart extends ChartWidget
{
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Desembolsos vs Pagos (X 1000MM)';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '300px';


    protected function getData(): array
    {
        return [
            //
            'datasets' => [
                [
                    'label' => 'Desembolsos',
                    'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
                ],

                [
                    'label' => 'Pagos',
                    'data' => [90, 20, 25, 82, 51, 12, 35, 14, 45, 45, 77, 56],
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function getChartData():array {

        $chart1=Disbursementmonth::query()
            ->get()
            ->toArray();

        $lchart1 = count($chart1);

        return [1];
    }
}

/*
php artisan tinker
*/