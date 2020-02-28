<?php namespace Quasar\Core\Traits;

use Illuminate\Http\Response;

trait JsonResponse
{
    /**
     * @param   $data
     * @param   int $code
     * @return  \Illuminate\Http\JsonResponse
     */
    public static function successResponse($data, $code = Response::HTTP_OK)
    {
        return response()->json([
            'status'        => $code,
            'statusText'    => 'success',
            'data'          => $data
        ], $code);
    }

    /**
     * @param   $message
     * @param   $code
     * @return  \Illuminate\Http\JsonResponse
     */
    public static function errorResponse($message, $code, $errors = null)
    {
        $response = [
            'status'        => $code,
            'statusText'    => $message  
        ];

        if ($errors) $response['errors'] = $errors;

        return response()->json($response, $code);
    }
}
