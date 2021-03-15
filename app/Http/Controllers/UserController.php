<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController
{
    /**
     * @var User
     */
    private $userModel;

    public function __construct(
        User $userModel
    ) {
        $this->userModel = $userModel;
    }

    /**
     * @param Request $request
     */
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string'],
                'email' => ['required', 'string'],
                'document' => ['required', 'numeric'],
                'wallet' => ['numeric']
            ]);

            if ($validator->fails()) {
                throw new \Exception($validator->errors());
            }

            $this->userModel->create($request->all());

        } catch (Exception $e) {
            dd($e);
            return response()->json([
                'success' => false
            ], 400);
        }

        return response()->json([
            'success' => true
        ]);
    }
}
