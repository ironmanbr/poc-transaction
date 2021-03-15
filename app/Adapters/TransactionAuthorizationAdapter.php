<?php

namespace App\Adapters;

use Exception;
use GuzzleHttp\Client;

class TransactionAuthorizationAdapter
{
   public function authorize($data): bool
    {
        try {
            $client = new Client([
                'base_uri' => 'https://run.mocky.io'
            ]);

            $response = $client->get(
                '/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6',
                [
                    'json' => [
                        'payer' => $data['payer'],
                        'payee' => $data['payee'],
                    ]
                ]
            );

            if ($response->getStatusCode() != 200) {
                throw new Exception('Fail authorization');
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}
