<?php

namespace Modules\Auth\Services;

class ResponseBuilder
{
    public static function success($data, string $message, ?string $token = null, int $status = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        if ($token !== null) {
            $response['token'] = $token;
        }

        return response()->json($response, $status);
    }

    public static function error(string $message, int $status = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
        ], $status);
    }
}
