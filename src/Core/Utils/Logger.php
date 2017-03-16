<?php
declare(strict_types=1);

namespace StackGuru\Core\Utils;


class Logger
{
	private static $database = null;

	private static setDatabase(\PDO $database) 
	{
		static::$database = $database;
	}


	public function callbackExceptionLog(?\Exception $e = null) 
	{
		if (null === $object) {
			return;
		}


	}


	public function log(int $level, string $message, $object = null) 
	{
		if (false === $this->appendToLogfile($message)) {
			echo "ERROR! Unable to log message: {$message}", PHP_EOL;
		}
	}

}