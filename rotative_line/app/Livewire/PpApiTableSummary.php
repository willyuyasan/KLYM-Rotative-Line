<?php

namespace App\Livewire;

use Filament\Tables\Columns\TextColumn;
use App\Models\PpsimulationApiModel;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PpApiTableSummary extends BaseWidget
{

    protected $listeners = ['refreshPpsimulationApiModel' => '$refresh'];

    protected int | string | array $columnSpan = 'full';

    public function getTaskId(): string
    {
        $task_id = session()->get('task_id');
        if (empty($task_id)) {
            $task_id = 'abc';
        }

        //(new PpsimulationApiModel)->getRows();
        //$this->dispatch('refreshPpsimulationApiModel');
        //$temp = PpsimulationApiModel::query()->where('task_id',$task_id);
        $this->dispatch('refreshPpsimulationApiModel');

        return $task_id;
    }

    public function table(Table $table): Table
    {
        $task_id = $this->getTaskId();

        return $table
        //->query(PpsimulationApiModel::query())
        ->query(PpsimulationApiModel::taskId($task_id))
        ->columns([

            TextColumn::make('quote'),

            TextColumn::make('credit_quote_init_date')
                ->label('Fecha de inicio de cuota')
                ->grow(false),
            
            TextColumn::make('credit_quote_end_date')
                ->label('Fecha de fin de cuota')
                ->grow(false),

            TextColumn::make('quote_days')
                ->label('Dias de la cuota')
                ->limit(20)
                ->grow(false),

            TextColumn::make('quote_amount')
                ->label('Valor cuota')
                ->grow(false)
                ->numeric(decimalPlaces: 0),

            TextColumn::make('capital_quote_amount')
                ->label('Valor cuota a capital')
                ->grow(false)
                ->numeric(decimalPlaces: 0),

            TextColumn::make('proyected_interest')
                ->label('Valor cuota a interes')
                ->grow(false)
                ->numeric(decimalPlaces: 0),
            
            TextColumn::make('capital_balance')
                ->label('Balance dueda capital')
                ->grow(false)
                ->numeric(decimalPlaces: 0),

        ])
        //->poll(5)
        ->paginated(false);
    }
}
