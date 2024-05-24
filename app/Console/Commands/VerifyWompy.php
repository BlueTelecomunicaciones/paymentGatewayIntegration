<?php

namespace App\Console\Commands;

use App\Http\Controllers\TrackerController;
use App\Models\Tracker;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Console\Command;
use Carbon\Carbon;

class VerifyWompy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wompi:verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $dateStart  = (new Carbon)->subDays(15);
        $dateEnd    = (new Carbon);
        $trackers = Tracker::where("active","0")->whereNull("status")->whereBetween("created_at",[ $dateStart ,$dateEnd])->get();
        foreach ( $trackers as $tracker ) {
            $client = new Client;

            try {

                $url = "https://production.wompi.co/v1/transactions?reference=$tracker->reference";

                $response = $client->request('GET', $url, [
                    'headers' => [
                        'Content-Type'      => 'application/json',
                        'Authorization'     => 'Bearer prv_prod_hon0P14SHdV2QJOpfRB3zXzKIsz6p0d1'
                    ]
                ]);


                $responseBody = json_decode($response->getBody());
                if ( is_array( $responseBody->data ) && count( $responseBody->data ) > 0 ) {

                    $transaction = (object)$responseBody->data[0];

                    $request = new Request;
                    $request->merge(['transaction' => $transaction]);

                    (new TrackerController)->response($request);

                    var_dump($transaction->customer_email , $transaction->status ,$transaction->id );

                }

            } catch (\GuzzleHttp\Exception\ConnectException $e) {
                // This is will catch all connection timeouts
                // Handle accordinly
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                // This will catch all 400 level errors.
                return $e->getResponse()->getStatusCode();
            }
        }
        return Command::SUCCESS;
    }
}
