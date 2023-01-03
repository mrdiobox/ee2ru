<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Http;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            $data = [
                'desc' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
            Http::post('https://api.telegram.org/bot'.env('TELEGRAM_TOKEN').'/sendMessage', [
                'chat_id' => env('TELEGRAM_ADMIN_ID'),
                'text'=> (string)view('report', $data),
                'parse_mode' => 'html'
            ]);
        });
    }
}
