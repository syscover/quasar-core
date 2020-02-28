<?php namespace Quasar\Core\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Quasar\Core\Support\JsonResponse;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof \Illuminate\Validation\ValidationException && $request->wantsJson()) 
        {    
            return JsonResponse::errorResponse('Unprocessable Entity', Response::HTTP_UNPROCESSABLE_ENTITY, $exception->errors());
        }

        return parent::render($request, $exception);
    }
}
