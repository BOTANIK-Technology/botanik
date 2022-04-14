<?php
namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;

class CustomizeFormatter
{
    /**
     * Customize the given logger instance.
     *
     * @param  \Illuminate\Log\Logger  $logger
     * @return void
     */
    public function __invoke($logger)
    {
        $processName = 'local';
        if (config('app.env') === 'production') {
            $processUser = posix_getpwuid(posix_geteuid());
            $processName= $processUser['name'];
        }
        $sapi = php_sapi_name();

        foreach ($logger->getHandlers() as $handler) {
            if ($handler instanceof RotatingFileHandler) {
                $handler->setFilenameFormat("{filename}-$sapi-$processName-{date}", 'Y-m-d');
            }
        }
    }
}
