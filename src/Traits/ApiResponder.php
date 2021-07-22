<?php


namespace Miladimos\FileManager\Traits;

trait ApiResponder
{
    protected function responseSuccess($data, $statusCode = 200, $statusMessage = "Ok")
    {
        return response()->json([
            'success' => true,
            'status_code' => $statusCode,
            'status_message' => $statusMessage,
            'data' => $data
        ], $statusCode);
    }

    protected function responseError($data, $statusCode = 500, $statusMessage = "Error")
    {
        return response()->json([
            'success' => false,
            'status_code' => $statusCode,
            'status_message' => $statusMessage,
            'data' => $data
        ], $statusCode);
    }
}
