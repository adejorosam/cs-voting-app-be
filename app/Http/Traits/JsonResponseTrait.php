<?php
namespace App\Http\Traits;


trait JsonResponseTrait
{
    public function jsonSuccessResponse($message,$data, $code){
        return response([
            'status' => 'Ok',
            'message' => $message,
            'data' => $data
        ], $code);
        
    }

    public function jsonErrorResponse($message, $code)
    {
        return response([
            'status' => 'Error',
            'message' => $message
        ], $code);
    }
}
