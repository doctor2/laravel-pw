<?php


namespace App\Http\Controllers;


class BaseController extends Controller
{
    public function formedErrorResult($message, $code, $errors = [])
    {
        return response([
            'status' => 'error',
            'code' => $code,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    public function formedSuccessResult($data = [], $code = 200)
    {
        return response([
            'status' => 'success',
            'data' => $data,
            'code' => $code,
        ], $code);
    }
}
