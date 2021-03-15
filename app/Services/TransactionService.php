<?php

namespace App\Services;

use App\Adapters\TransactionAuthorizationAdapter;
use App\Adapters\TransactionMessageAdapter;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    /**
     * @var TransactionAuthorizationAdapter
     */
    private $transactionAuthorizationAdapter;

    /**
     * @var TransactionMessageAdapter
     */
    private $transactionMessageAdapter;

    public function __construct(
        TransactionAuthorizationAdapter $transactionAuthorizationAdapter,
        TransactionMessageAdapter $transactionMessageAdapter
    ) {
        $this->transactionAuthorizationAdapter = $transactionAuthorizationAdapter;
        $this->transactionMessageAdapter = $transactionMessageAdapter;
    }

    public function transfer($data): bool
    {
        DB::beginTransaction();
        try {
            if (!TransactionValidate::transferValidate($data)) {
                throw new \Exception('Invalid data');
            }

            Transaction::create([
                'value' => $data['value'],
                'payer_id' => $data['payer'],
                'payee_id' => $data['payee'],
            ]);

            $payer = User::find($data['payer']);
            $payer->wallet -= $data['value'];
            $payer->save();

            $payee = User::find($data['payee']);
            $payee->wallet += $data['value'];
            $payee->save();

            if (!$this->transactionAuthorizationAdapter->authorize($data)) {
                throw new \Exception('Transaction not authorized');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }

        if (!$this->transactionMessageAdapter->transferReceived($data)) {
            // TODO add in queue retry
        }

        return true;
    }
}
