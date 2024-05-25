<?php

namespace App\Http\Controllers;

use App\Models\Tracker;
use DateTime;
use GuzzleHttp\Client;
use http\Env\Response;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Utils;

class TrackerController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $tracker = new Tracker;
            $tracker->fill( $request->all() );
            $tracker->amount = json_encode( $request->amount );
            $tracker->invoiceId = json_encode( $request->invoiceId );
            $tracker->active = 0;
            if ( $tracker->save() ) {
                return response()->json(["message" => "Datos registrado exitosamente." , "status" => true , "tracker" => $tracker ] , 200);
            }
        }
        catch ( \Exception $exception ) {
            return response()->json(["message" => $exception->getMessage()  , "status" => false  ] , 400);
        }

    }

    public function update ( Request $request , Tracker $tracker , $id ) {
        try {
            $tracker = $tracker::where("id", $id)->first();
            $tracker->transactionId = $request->transactionId;

            if ( $tracker->update() ) {
                return response()->json(["message" => "Datos actualizados exitosamente." , "status" => true , "tracker" => $tracker ] , 200);
            }
        }
        catch ( \Exception $exception ) {
            return response()->json(["message" => $exception->getMessage()  , "status" => false  ] , 400);
        }
    }

    function response_pay (Request $request) {

        $client = new Client;

        try {

            $env = $request->get('env');
            $id = $request->get('id');
            $path = ( $env == null || $env != "test" ) ? "production" : "sandbox";
            $url = "https://$path.wompi.co/v1/transactions/$id";

            $response = $client->request('GET', $url );

            $object = json_decode($response->getBody());

            if ( $response->getStatusCode() == 200 ) {

                return view('response' , [ "response" => (object)[
                    "message" => "El pago se efectuo exitosamente r.",
                    "status" => true
                ] , "data" => $object->data ]);

            }else {
                return view('response', [
                    "response" => (object)[
                        "message" => "Hubo un problema al procesar la solicitud.",
                        "status" => false
                    ],
                    "data" => null
                ]);
            }

        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            // This is will catch all connection timeouts
            return $e->getResponse()->getStatusCode();
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // This will catch all 400 level errors.
            return $e->getResponse()->getStatusCode();
        }

    }

    /**
     * Active response
     * @param Request $request
     */
    public function response (Request $request)
    {
        $transaction = (object)$request->get("transaction");
        $tracker = Tracker::where("reference",$transaction->reference)->first();

        if ( $tracker->active == 1 ) {
            return response()->json(["message" => "La factura ya tiene registrado el pago." , "status" => true , ]);
        }
        else if ( $transaction->status == "APPROVED" ) {

            $amounts = json_decode( $tracker->amount );
            $invoicesId = json_decode( $tracker->invoiceId );
            $tracker->active = 1;
            $tracker->status = $transaction->status;
            $tracker->transactionId = $transaction->id;

            if ( is_array($invoicesId) && count($invoicesId) == 1 ) {

                foreach ( $invoicesId as $key => $invoiceId ) {

                    $this->approveInvoice( $invoiceId  , (int)$transaction->amount_in_cents / 100 , (isset($transaction->paymentMethod)) ? (object)$transaction->paymentMethod : $transaction->payment_method , $transaction->reference );

                }

            }
            elseif ( is_array($amounts) && count($amounts) > 1 ) {

                foreach ( $amounts as $key => $amount ) {

                    $this->approveInvoice( (int)$invoicesId[$key] , (int)$amount , (isset($transaction->paymentMethod)) ? (object)$transaction->paymentMethod : $transaction->payment_method  , $transaction->reference."-".$key  );

                }

            }
            else {

                $this->approveInvoice( $transaction->invoiceId , (int)$transaction->amount_in_cents / 100 , (object)$transaction->paymentMethod , $transaction->reference );

            }

            $tracker->update();
            return response()->json(["message" => "El pago se efectuo exitosamente." , "status" => true , ]);
        }
        else if ( $transaction->status == "ERROR" ) {
            return response()->json(["message" => "Se presentó un problema al intentar realizar el pago." , "status" => false , ]);
            // NewAlert( 2 , `Se presentó un problema al intentar realizar el pago.` , `Error al intentar pagar con: $ ${transaction.paymentMethodType}` )
        }
        else if ( $transaction->status == "DECLINED" ) {
            return response()->json(["message" => "La transacción a sido declinada  por la entidad a la cual se intento realizar la transacción" , "status" => false , ]);
            // NewAlert( 2 , `La transacción a sido declinada  por la entidad a la cual se intento realizar la transacción` , `Transación declinada por el medio de pago: ${transaction.paymentMethodType}` )
        }

    }

    function approveInvoice ( $idInvoice ,$price , $paymentMethod , $reference ) {

        $client = new Client;
        try {

            $DateNow = new DateTime(); // Crea un objeto DateTime con la fecha y hora actual
            $formatISO = $DateNow->format('Y-m-d\TH:i:s\Z');

            $response = $client->request('POST', "https://crm2.bluetelecomunicaciones.com/api/v1/PaidInvoice" , [
               'headers' => [
                   'Content-Type'     => 'application/json'
               ],
               "json" => [
                    "token" => "YjQ4cmZEZHQyNnBNQ2Z5d0R4R1NnUT09",
                    "idfactura" => $idInvoice,
                    "pasarela" => "PASARELA WOMPI",
                    "cantidad" => $price,
                    "comision" => 0,
                    "idtransaccion" => $reference,
                    "fecha" => $formatISO
               ]
           ]);

            $approved = json_decode($response->getBody());

            if ( $response->getStatusCode() == 200 && $approved->estado != "error" ) {
                return `El pago se efectuo exitosamente. Factura $idInvoice por un precio de $price con el metodo de pago. $paymentMethod->type`;
            }
            else {
                return $approved;
            }
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            // This is will catch all connection timeouts
            // Handle accordinly

//            return $e->getCode();
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // This will catch all 400 level errors.
//            return $e->getResponse()->getStatusCode();
        }

    }

    function verify ( Request $request ) {
        $ids = [
            1598, 1592,/* 1584, 1578, 1576, 1575, 1574, 1556, 1450, 1444, 1396, 1333, 1331, 1328, 1288, 1287, 1286, 1285,
            1284, 1283, 1282, 1281, 1280, 1279, 1278, 1277, 1275, 1273, 1272, 1271, 1270, 1269, 1268, 1266, 1264, 1263,
            1262, 1261, 1260, 1259, 1258, 1257, 1256, 1255, 1254, 1253, 1252, 1251, 1250, 1249, 1248, 1247, 1246, 1245,
            1244, 1243, 1242, 1241, 1240, 1239, 1238, 1237, 1236, 1235, 1233, 1232, 1231, 1230, 1229, 1228, 1227, 1226,
            1225, 1224, 1223, 1222, 1221, 1220, 1219, 1218, 1217, 1216, 1215, 1214, 1213, 1212, 1211, 1210, 1209, 1208,
            1207, 1206, 1205, 1204, 1203, 1202, 1201, 1200, 1199, 1198, 1197, 1196, 1195, 1194, 1192, 1191, 1190, 1189,
            1188, 1187, 1186, 562*/
        ];

        $client = new Client;
        try {

            foreach ( $ids as $id ) {

                $response = $client->request('POST', "https://crm2.bluetelecomunicaciones.com/api/v1/DeleteTransaccion", [
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ],
                    "json" => [
                        "token" => "YjQ4cmZEZHQyNnBNQ2Z5d0R4R1NnUT09",
                        "idfactura" => $id
                    ]
                ]);

                $approved = json_decode($response->getBody());

                var_dump($approved);

            }

        }
        catch (\GuzzleHttp\Exception\ConnectException $e) {
            // This is will catch all connection timeouts
            // Handle accordinly
        }
        catch (\GuzzleHttp\Exception\ClientException $e) {
            // This will catch all 400 level errors.
        }

    }

}
