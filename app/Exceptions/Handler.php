<?php

namespace App\Exceptions;

use Throwable;
use App\Services\Traits\Responsable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    use Responsable;
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {

        $this->renderable(function (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        });

        $this->reportable(function (\Illuminate\Validation\ValidationException $e) {
            return $this->validationError($e->errors());
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
