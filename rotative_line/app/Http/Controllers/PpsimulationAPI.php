<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class PpsimulationAPI extends Controller
{
    //
    public function index(Request $request){

        $response = Http::withHeaders([
            'Accept'=>'application/json',
            'Authorization'=>'Bearer william.uyasan',
            'Content-Type'=>'application/json'
        ])
        //->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36')
        //->timeout(120)
        ->post('http://192.168.1.15:8000/rotative_line/payment_plan_simulator/', [
            'tax_number' => '80932303',
            'credit_amount' => 100000000,
            'discount_rate' => 0.03,
            'credit_term' => 120
        ]);

        $res = $response->json();
        error_log($res['status']);

        dd($res);

        //return $res;
    }
}

/*

$response = Http::withHeaders([
            'Accept'=>'application/json',
            'Authorization'=>'Bearer william.uyasan',
            'Content-Type'=>'application/json'
        ])
        //->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36')
        //->timeout(120)
        ->post('http://192.168.1.15:8000/rotative_line/payment_plan_simulator/', [
            'tax_number' => '80932303',
            'credit_amount' => 100000000,
            'discount_rate' => 0.03,
            'credit_term' => 120
        ]);

        $res = $response->json();

$response = Http::withHeaders([
            //'Accept'=>'application/json',
            'Authorization'=>'Bearer william.uyasan',
            //'Content-Type'=>'application/json'
        ])
        ->timeout(120)
        ->get('http://192.168.1.15:8000/rotative_line/ping/');


$client = new Client(['base_uri' => 'http://192.168.1.15:8000']);
        
        $response = $client->request('POST','/rotative_line/payment_plan_simulator/', [
            
            'headers' => [
                'Accept'=>'application/json',
                'Content-Type'=>'application/json',
                'Authorization'=>'Bearer william.uyasan'
            ],
            'json' => [
                'tax_number' => '80932303',
                'credit_amount' => '100000000',
                'discount_rate' => '0.03',
                'credit_term' => '120'
            ]
        ]);

        $res = $response->getBody()->getContents();


$response = Http::withHeaders([
            'Accept'=>'application/json',
            'Authorization'=>'Bearer william.uyasan',
            'Content-Type'=>'application/json'
        ])
        ->withQueryParameters([
            'task_id' => 'abc'
        ])
        ->post('http://192.168.1.15:8000/rotative_line/payment_plan_simulator_callback/', []);

        $res = $response->json();

        dd($res);

*/