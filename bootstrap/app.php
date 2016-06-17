<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\LogEntriesHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

$app = new Illuminate\Foundation\Application(
    realpath(__DIR__.'/../')
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/* Configure default file logger to follow level conventions in
 * environment configuration.
 *
 * Adds the following to .env
 * LOGGING_LEVEL – Can be set to DEBUG, INFO, NOTICE, WARNING, ERROR, CRITICAL, or ALERT
 * LOG_MAX_FILES – Maximum files to use in the daily logging format
 */
$app->configureMonologUsing(function ($monolog) {
    $logLevels = Logger::getLevels();
    $logLevel = env('LOGGING_LEVEL', 'DEBUG');
    $logMaxFiles = (int)env('LOG_MAX_FILES', 5);
    $storagePath = realpath(__DIR__ . '/../') . '/storage';
    $monolog->pushHandler(
        $handler = new RotatingFileHandler(
            $storagePath . '/logs/laravel.log',
            $logMaxFiles,
            $logLevels[$logLevel])
    );
    $handler->setFormatter(new LineFormatter(null, null, true, true));
    // Configure logentries to follow level conventions in environment configuration.
    $logEntriesHandler = new LogEntriesHandler(env('LOGENTRIES_TOKEN'));
    $monolog->pushHandler($logEntriesHandler, $logLevels[$logLevel]);
});

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
