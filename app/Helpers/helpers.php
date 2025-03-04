<?php

if (!function_exists('sendResponse')) {
    function sendResponse($status, $data = [], $message = '')
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $status == 'error' ? 500 : 200);
    }
}
