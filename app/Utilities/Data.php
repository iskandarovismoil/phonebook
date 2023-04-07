<?php

namespace App\Utilities;

use Illuminate\Http\JsonResponse;

class Data
{
    public static function makeResponseForm(bool $success = false, $data = null, int $status = 400, $errors = null) : JsonResponse
    {
        return response()->json([
            'success' => $success,
            'data' => $data,
            'errors' => $errors,
        ], $status);
    }
}
