<?php

use App\Adapters\TransactionAuthorizationAdapter;
use App\Adapters\TransactionMessageAdapter;
use Laravel\Lumen\Testing\DatabaseTransactions;

use App\Models\User;
use Mockery\MockInterface;

class CashTransferTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @param $amount
     * @return void
     * @dataProvider amountDataProvider
     * @throws Exception
     */
    public function testValidTransactions($amount)
    {
        $this->app->instance(
            TransactionAuthorizationAdapter::class,
            Mockery::mock(TransactionAuthorizationAdapter::class, function (MockInterface $mock) {
                $mock->shouldReceive('authorize')
                    ->andReturn(true);
            })
        );

        $this->app->instance(
            TransactionMessageAdapter::class,
            Mockery::mock(TransactionMessageAdapter::class, function (MockInterface $mock) {
                $mock->shouldReceive('transferReceived')
                    ->andReturn(true);
            })
        );

        $payerWallet = $amount * random_int(1, 9);
        $payer = User::factory()->create([
            'wallet' => $payerWallet
        ]);

        $payee = User::factory()->create();

        $this->post(
            '/transaction',
                [
                    'value' => $amount,
                    'payer' => $payer->id,
                    'payee' => $payee->id
                ]
            )
            ->seeJsonContains([
               'success' => true
            ])
            ->assertResponseOk();

        $this->seeInDatabase('transactions', [
                'value' => $amount,
                'payer_id' => $payer->id,
                'payee_id' => $payee->id,
            ])
            ->seeInDatabase('users', [
                'id' => $payer->id,
                'wallet' => ($payerWallet - $amount)
            ])
            ->seeInDatabase('users', [
                'id' => $payee->id,
                'wallet' => $amount
            ])
        ;
    }

    /**
     * @param $amount
     * @return void
     * @dataProvider amountDataProvider
     */
    public function testInvalidTransactions($amount)
    {
        $dataTransaction = [
            'value' => $amount,
        ];

        $this->post('/transaction', $dataTransaction)
            ->seeJsonContains([
                'success' => false
            ])
            ->assertResponseStatus(400)
        ;
    }

    /**
     * @param $amount
     * @return void
     * @dataProvider amountDataProvider
     */
    public function testHasNoBalanceTransactions($amount)
    {
        $payerWallet = $amount - 1;

        $payer = User::factory()->create([
            'wallet' => $payerWallet
        ]);

        $payee = User::factory()->create();

        $dataTransaction = [
            'value' => $amount,
            'payer' => $payer->id,
            'payee' => $payee->id
        ];

        $this->post('/transaction', $dataTransaction)
            ->seeJsonContains([
                'success' => false
            ])
            ->assertResponseStatus(400)
        ;
    }

    /**
     * @param $amount
     * @dataProvider amountDataProvider
     * @throws Exception
     */
    public function testNotAuthorizedWithRollbackTransactions($amount)
    {
        $this->app->instance(
            TransactionAuthorizationAdapter::class,
            Mockery::mock(TransactionAuthorizationAdapter::class, function (MockInterface $mock) {
                $mock->shouldReceive('authorize')
                    ->andReturn(false);
            })
        );

        $payerWallet = $amount * random_int(1, 9);
        $payer = User::factory()->create([
            'wallet' => $payerWallet
        ]);

        $payee = User::factory()->create();

        $this->post(
            '/transaction',
            [
                'value' => $amount,
                'payer' => $payer->id,
                'payee' => $payee->id
            ]
        )
            ->seeJsonContains([
                'success' => false
            ])
            ->assertResponseStatus(400);

        $this->notSeeInDatabase('transactions', [
                'payer_id' => $payer->id,
                'payee_id' => $payee->id,
            ])
            ->seeInDatabase('users', [
                'id' => $payer->id,
                'wallet' => $payerWallet
            ])
            ->seeInDatabase('users', [
                'id' => $payee->id,
                'wallet' => null
            ])
        ;
    }

    /**
     * Generate data:
     *  value, payer, payee
     * @return float[][]
     */
    public function amountDataProvider()
    {
        return [
            [100.50],
            [150.29],
            [200.72],
            [249.99],
            [300.10],
        ];
    }
}
