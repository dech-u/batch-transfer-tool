<?php

namespace dacoto\LaravelWizardInstaller\Exceptions;

use Exception;
use Throwable;

class CantGenerateKeyException extends Exception
{
    public function __construct(string $message = "The application keys could not be generated.", int $code = 0, ?Throwable $previous = null) { parent::__construct($message, $code, $previous); }
}
