<?php


namespace App\Adapters;


use GuzzleHttp\Client;

class TransactionMessageAdapter
{
    public function transferReceived($data): bool
    {
        try {
            $client = new Client([
                'base_uri' => 'https://run.mocky.io'
            ]);

            $response = $client->get(
                '/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04',
                [
                    'json' => [
                        'value' => $data['value'],
                        'payer' => $data['payer'],
                        'payee' => $data['payee'],
                        'message' => 'Transfer received'
                    ]
                ]
            );

            if ($response->getStatusCode() != 200) {
                throw new Exception('Fail message sending');
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}
