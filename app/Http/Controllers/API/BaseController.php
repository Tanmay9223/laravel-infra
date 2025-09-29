<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\API\CommonHelper;

class BaseController extends Controller
{

    public $successStatus        =  CommonHelper::SUCCESS_STATUS;
    public $createSuccessStatus  =  CommonHelper::CREATE_SUCCESS_STATUS;
    public $noContentStatus      =  CommonHelper::NO_CONTENT_STATUS;
    public $badRequestStatus     =  CommonHelper::BAD_REQUEST;
    public $forbiddenStatus      =  CommonHelper::FORBIDDEN;
    public $notFoundStatus      =  CommonHelper::NOT_FOUND;
    public $validationErrorStatus  =  CommonHelper::VALIDATION_ERROR_STATUS;

    public function sendResponse($message, $result = [])
    {
    	$response = [
            'success' => true,
            'message' => $message,
        ];

        if(!empty($result)){
            $response['data'] = $result;
        }

        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($errorMessage, $error = [], $code = 400)
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
}
