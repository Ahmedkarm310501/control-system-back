<?php

namespace App\Traits;

trait HttpResponses
{
    public function success($data, $code = 200 , $message = null)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public function error($message, $code)
    {
        return response()->json(['error' => $message], $code);
    }
}
