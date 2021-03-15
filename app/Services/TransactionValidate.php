<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Validator;

class TransactionValidate
{
    static public function transferValidate($data): bool
    {
        $validator = Validator::make(
            $data, [
                'value' => 'required',
                'payer' => 'required',
                'payee' => 'required'
            ]
        );

        if ($validator->fails()) {
            return false;
        }

        if (!($payee = User::find($data['payee']))) {
            return false;
        }

        if (!($payer = User::find($data['payer']))) {
            return false;
        }

        return !$payer->isStore() && $payer->hasBalance($data['value']);
    }
}
