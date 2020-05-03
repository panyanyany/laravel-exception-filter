<?php

namespace Panyanyany\LaravelExceptionFilter;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Psr\Log\LoggerInterface;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class,
        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
        \Symfony\Component\Routing\Exception\MethodNotAllowedException::class,
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
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable $exception
     * @return void
     */
    public function report(\Throwable $exception) {
        // parent::report($exception);
        if ($this->shouldntReport($exception)) {
            return;
        }

        try {
            $logger = $this->container->make(LoggerInterface::class);
        } catch (Exception $ex) {
            throw $exception; // throw the original exception
        }

        $msg = get_class($exception);
        $msg .= " ".substr($exception->getMessage(), 0, 1024);
        $msg = trim($msg);

        $logger->error(
            $msg,
            array_merge($this->context(), ['exception' => $this->getExceptionAsString($exception)]
            ));
    }

    public function getExceptionAsString(Exception $e) {
        $trace = $e->getTraceAsString();
        $lines = explode("\n", $trace);
        $lines = array_filter($lines, function ($e) {
            return strpos($e, "#1 ") !== false || (strpos($e, '/vendor/') === false && strpos($e, ' phar://') === false);
        });
        return "\n  " . implode("\n  ", $lines);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Throwable               $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, \Throwable $exception) {
        return parent::render($request, $exception);
    }
}

