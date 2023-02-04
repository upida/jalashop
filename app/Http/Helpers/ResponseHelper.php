<?php

namespace App\Http\Helpers;

trait ResponseHelper
{

    protected function onSuccess($data, string $message = '', int $code = 200)
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function onError(int $code, string $message = '', $errors = [])
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
}