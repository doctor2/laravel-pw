<?php


namespace App\Http\Controllers;


class BaseController extends Controller
{
    public function formErrorResult($message, $code, $errors = [])
    {
        return response([
            'status' => 'error',
            'code' => $code,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    public function formSuccessResult($data = null, $code = 200)
    {
        return response([
            'status' => 'success',
            'data' => $data,
            'code' => $code,
        ], $code);
    }
}
