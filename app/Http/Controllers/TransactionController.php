<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * @var TransactionService
     */
    private $transactionService;

    /**
     * Create a new controller instance.
     *
     * @param TransactionService $transactionService
     */
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        $resultTransfer = $this->transactionService->transfer($request->all());

        if (!$resultTransfer) {
            return response()->json([
                'success' => false
            ], 400);
        }

        return response()->json([
            'success' => true
        ]);
    }
}
