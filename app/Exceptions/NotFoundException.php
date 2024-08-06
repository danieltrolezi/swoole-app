<?php

namespace App\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    public function __construct(string $path, int $code = 0, Throwable $previous = null)
	{
		parent::__construct("Path \"$path\" was not found", $code, $previous);
	}
}