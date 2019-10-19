<?php


namespace App\Http\Controllers;


class BaseController extends Controller
{
    public function formedErrorResult($message, $code)
    {
        return response([
            'status' => 'error',
            'code' => $code,
            'message' => $message,
        ], $code);
    }

    public function formedSuccessResult($data, $code = 200)
    {
        return response([
            'status' => 'success',
            'data' => $data,
            'code' => $code,
        ], $code);
    }
}
