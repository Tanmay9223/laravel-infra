<?php

namespace App\Classes;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class ApiResponseClass
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function rollback($e, $message = "Something went wrong! Process not completed"){
        DB::rollBack();
        self::throw($e, $message);
    }

    public static function throw($e, $message = "Something went wrong! Process not completed"){
        Log::info($e);
        throw new HttpResponseException(response()->json([
            'status' => false,
            "message" => $message
        ], 500));
    }

    public static function sendResponseCode($message, $code = 200, $result = []){
        $response = [
            'success' => true,
            'message' => $message
        ];

        if(!empty($result)){
            $response['data'] = $result;
        }

        return response()->json($response, $code);
    }

    public static function sendErrorCode($errorMessage, $code = 400, $error = [])
    {
    	$response = [
            'success' => false,
            'message' => $errorMessage,
        ];

        if(!empty($error)){
            $response['data'] = $error;
        }

        return response()->json($response, $code);
    }

    public static function created($data = [], $message = 'Resource created successfully')
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], 201);
    }

    public static function updated($data = [], $message = 'Resource updated successfully')
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], 200);
    }

    public static function list($data, $totalData, $message = 'Success')
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'list' => $data,
            'totalRecords' => $totalData,
        ], 200);
    }


}
