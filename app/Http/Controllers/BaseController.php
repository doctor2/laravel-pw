<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class BaseController extends Controller
{
    /**
     * @param string $message - error message
     * @param int $code - response code
     *
     * @return ResponseFactory|Response
     */
    public function formErrorResult(string $message, int $code)
    {
        return response([
            'status' => 'error',
            'code' => $code,
            'message' => $message,
        ], $code);
    }

    /**
     * @param $data
     * @param int $code - response code
     *
     * @return ResponseFactory|Response
     */
    public function formSuccessResult($data = null, $code = 200)
    {
        return response([
            'status' => 'ok',
            'data' => $data,
            'code' => $code,
        ], $code);
    }
}
