<?php

namespace App\Models;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class PpsimulationApiModel extends Model
{
    //
    use Sushi;

    protected $listeners = ['refreshPpsimulationApiModel' => '$update'];

    protected static string $task_id = 'abc';

    public static function taskId(string $task_id): Builder
    {
        static::$task_id = $task_id;

        return static::query();
    }

    public function getTaskId(): string
    {
        $task_id = session()->get('task_id');
        if (empty($task_id)) {
            $task_id = self::$task_id;
        }

        error_log($task_id);

        return $task_id;
    }

    public function getApiData($task_id) {

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

    public function getRows()
    {
        $task_id = $this->getTaskId();

        if ($task_id != 'abc'){

            $res = $this->getApiData($task_id);

            //filtering some attributes
            $res2 = Arr::map($res['json_payload']['credit_quotes_info'], function ($item) {
                return Arr::only($item,
                    [
                        'quote',
                        'credit_quote_init_date',
                        'credit_quote_end_date',
                        'quote_days',
                        'capital_quote_amount',
                        'proyected_interest',
                        'quote_amount',
                        'capital_balance'
                    ]
                );
            });

            return $res2;
        }
        else{
            return [];
        }
    }

    protected function sushiShouldCache(): bool
    {
        return false;
    }

    public static function bustSushiCache()
    {
        $model = new static;
        $cachePath = storage_path('sushi/' . $model->sushiCachePath());

        if (File::exists($cachePath)) {
            File::delete($cachePath);
        }

        File::cleanDirectory(storage_path('sushi'));
        
    }

    public static function flush()
    {
        $instance = (new static);

        $cachePath = $instance->sushiCachePath();
        $dataPath = $instance->sushiCacheReferencePath();

        $instance->cacheFileNotFoundOrStale($cachePath, $dataPath, $instance);
    }
}

/*
return Cache::remember(
                'paymentplan', 
                now()->addHour(), 
                function () {

                    $res = $this->getApiData();
                    //filtering some attributes
                    $res2 = Arr::map($res['json_payload']['credit_quotes_info'], function ($item) {
                        return Arr::only($item,
                            [
                                'quote',
                                'credit_quote_init_date',
                                'credit_quote_end_date',
                                'quote_days',
                                'capital_quote_amount',
                                'proyected_interest',
                                'quote_amount',
                                'capital_balance'
                            ]
                        );
                    });

                    return $res2;
                });



$res = $this->getApiData();

        //filtering some attributes
        $res2 = Arr::map($res['json_payload']['credit_quotes_info'], function ($item) {
            return Arr::only($item,
                [
                    'quote',
                    'credit_quote_init_date',
                    'credit_quote_end_date',
                    'quote_days',
                    'capital_quote_amount',
                    'proyected_interest',
                    'quote_amount',
                    'capital_balance'
                ]
            );
        });

        return $res2;
*/