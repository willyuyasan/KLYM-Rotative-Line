<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\File;
use App\Livewire\PpApiTableSummary;
use Illuminate\Contracts\View\View;
use Filament\Forms\Get;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Exception;
use App\Models\PpsimulationApiModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Facades\Http;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Contracts\HasForms;
use App\Models\Rlquote;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;

class Ppsimulation extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Calculo Plan de Pagos';
    protected static ?string $modelLabel = 'Calculo Plan de Pagos';

    protected static string $view = 'filament.pages.ppsimulation';

    protected $listeners = ['refreshPpsimulationApiModel' => '$refresh'];

    public ?array $data = [];
    public ?array $response = [];

    public function mount(): void
    {
        $this->form->fill();
        $temp = new PpsimulationApiModel;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Datos del plan de pagos:')
                ->schema([

                    TextInput::make('issuer_tax_number')
                        ->label('Identificación cliente')
                        //->required()
                        ->maxLength(20)
                        ->grow(false),
                
                    TextInput::make('issuer_name')
                        ->label('Nombre cliente')
                        //->required()
                        ->maxLength(100)
                        ->grow(false),
                
                    TextInput::make('value_to_issuer')
                        ->label('Valor del crédito')
                        //->required()
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->numeric()
                        ->suffix('$')
                        ->grow(false),

                    TextInput::make('discount_rate')
                        ->label('Tasa de interés')
                        //->required()
                        //->formatStateUsing(fn (string $state): string => number_format($state*100,2))
                        ->suffix('%(M.V.)')
                        ->numeric()
                        ->grow(false),

                    TextInput::make('credit_term')
                        ->label('Termino del crédito')
                        ->required()
                        //->formatStateUsing(fn (string $state): string => number_format($state,0))
                        ->suffix('Dias')
                        ->integer()
                        ->grow(false),

                    DatePicker::make('disbursement_date')
                        ->label('Fecha desembolso')
                        //->required()
                        //->mask('99/99/9999')
                        //->placeholder('MM/DD/YYYY')
                        ->grow(false),
                
                    ])
                    ->columns(2),
                
                Fieldset::make('')
                ->schema([
                    Actions::make([
                        Action::make('getPpsimulation')
                            ->color('info')
                            ->label('Generate')
                            ->action('getPpsimulation')
                    ])
                ]),
        
            ])
            ->statePath('data');
    }

    public function getTaskId(): string
    {
        $task_id = session()->get('task_id');
        if (empty($task_id)) {
            $task_id = 'abc';
        }

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
        ->paginated(false);
    }

    //protected function getFooterWidgets(): array {
    //    return [
    //        PpApiTableSummary::class,
    //    ];
    //}

    //public static function getWidgets(): array {
    //    return [
    //        PpApiTableSummary::class,
    //    ];
    //}

    private function requestPpsimulationApi(){
        
        $response = Http::withHeaders([
            'Accept'=>'application/json',
            'Authorization'=>'Bearer william.uyasan',
            'Content-Type'=>'application/json'
        ])
        ->post('http://192.168.1.15:8000/rotative_line/payment_plan_simulator/', [
            'tax_number' => '80932303',
            'credit_amount' => 100000000,
            'discount_rate' => 0.03,
            'credit_term' => 120
        ]);

        $res = $response->json();

        return $res;
    }

    private function requestPpsimulationApi2(){

        $dat = $this->form->getState();

        if (empty($dat)) {
            $credit_term = 60;
        }
        else{
            $tax_number = $dat['issuer_tax_number'] ?: '80900300';
            $client_name = $dat['issuer_name'] ?: null;
            $credit_amount = $dat['value_to_issuer'] ?: 100000000;
            $discount_rate = $dat['discount_rate']/100 ?: null;
            $credit_term = $dat['credit_term'] ?: 60;
            $disbursement_date = $dat['disbursement_date'] ?: null;
        }

        error_log($credit_term);
        
        $response = Http::withHeaders([
            'Accept'=>'application/json',
            'Authorization'=>'Bearer william.uyasan',
            'Content-Type'=>'application/json'
        ])
        ->post('http://192.168.1.15:8000/rotative_line/payment_plan_simulator/', [
            'tax_number' => $tax_number,
            'client_name' => $client_name,
            'credit_amount' => $credit_amount,
            'discount_rate' => $discount_rate,
            'credit_term' => $credit_term,
            'disbursement_date' => $disbursement_date
        ]);

        $res = $response->json();

        return $res;
    }

    private function retrievePpsimulationApi($task_id){
        
        $response = Http::withHeaders([
            'Accept'=>'application/json',
            'Authorization'=>'Bearer william.uyasan',
            'Content-Type'=>'application/json'
        ])
        ->withQueryParameters([
            'task_id' => $task_id
        ])
        ->post('http://192.168.1.15:8000/rotative_line/payment_plan_simulator_callback/', []);

        $res = $response->json();

        return $res;
    }

    public function getPpsimulation2() {

        $res2 = $this->retrievePpsimulationApi('abc');

        $response = $res2['json_payload']['credit_quotes_info'];

        $this->response = $response;

        //dd($response[0]);

    }

    public function getPpsimulation() {

        $res = $this->requestPpsimulationApi2();

        $task_id = $res['task_id'];
        $task_status = 'Results not found!';
        $time = 0;
        error_log($task_id);

        do {
            
            $ssleep = 10;
            sleep($ssleep);
            $res2 = $this->retrievePpsimulationApi($task_id);
            $task_status = $res2['status'];
            $time = $time + $ssleep;
            error_log($task_status);

        } while ($task_status == 'Results not found!' and  $time < 150 );

        if ($task_status == 'Results not found!'){
            $task_id = 'abc';
        }

        session()->put('task_id', $task_id);
        $task_id = session()->get('task_id', $task_id);
        error_log($task_id);


        // IMPORTAT!
        // Clear the Sushi Cache to  compute the new data
        PpsimulationApiModel::flush();

        sleep(1);
        $this->dispatch('refreshPpsimulationApiModel');
        error_log('REFRESH');
        
        $file_contents = 'SGVsbG8gd29ybGQh';
        $file_contents = $res2['json_payload']['pdfbinary'];
        //$file_contents = base64_decode($file_contents);
        //$file_contents = $res['pdfbinary'];

        $this->response = $res2['json_payload']['credit_quotes_info'];

        return response()->stream(function () use ($file_contents) {
            echo base64_decode($file_contents); 
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="payment_plan.pdf"'
            //'Content-Type' => 'text/plain',
            //'Content-Disposition' => 'inline; filename="test.txt"'
        ]);

        
    }

    public function submmit()
    {
        $response = $this->getPpsimulation();
        dd($response);
    }
}


/*

return response()->make($file_contents, 200, [
            //'Content-type: application/pdf',
            'Content-type: text/plain',
            'Content-Disposition: attachment; filename=payment_plan.txt'
        ]);

        return response($file_contents)
            ->header('Cache-Control', 'no-cache private')
            ->header('Content-Description', 'File Transfer')
            ->header('Content-Type', 'text/plain')
            ->header('Content-length', strlen($file_contents))
            ->header('Content-Disposition', 'attachment; filename=payment_plan.txt')
            ->header('Content-Transfer-Encoding', 'binary')
            ->stream($file_contents);

$file_contents = 'SGVsbG8gd29ybGQh';
        //$file_contents = base64_decode($file_contents);
        //$file_contents = $res['pdfbinary'];

        return response()->stream(function () use ($file_contents) {
            echo base64_decode($file_contents); 
        }, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'inline; filename="test.txt"'
        ]);

Actions::make([
    Action::make('getPpsimulation')
        ->color('info')
        ->label('Generate')
        ->action('getPpsimulation')
    ])

*/
