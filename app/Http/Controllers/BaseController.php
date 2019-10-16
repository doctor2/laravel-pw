<?php


namespace App\Http\Controllers;


class BaseController extends Controller
{
    /**
     * @param string $message - error message
     * @param int $code - response code
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function formedErrorResult(string $message, int $code)
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
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function formedSuccessResult($data, $code = 200)
    {
        return response([
            'status' => 'ok',
            'data' => $data,
            'code' => $code,
        ], $code);
    }
}
