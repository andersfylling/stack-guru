<?php
declare(strict_types=1);

namespace StackGuru\Core\Utils;

class Logger
{
	private static $_initiated = false;
	private static $database;
	private static $development;
	private static $testing;

	/**
	 *	Initialize static variables.
	 *  Update code when this is approved: https://wiki.php.net/rfc/static_class_constructor
	 */
	public static function init(): void 
	{
		if (self::$_initiated) {
			return;
		}

		self::$database     = null;
		self::$development 	= defined("DEVELOPMENT") && DEVELOPMENT;
		self::$testing 	    = defined("TESTING") && TESTING;
		self::$_initiated   = true;
	}

	private static function setDatabase(\PDO $database) 
	{
		static::$database = $database;
	}


	public static function callbackExceptionLog(?\Exception $e = null) 
	{
		if (null === $object) {
			return;
		}


	}


	public static function log(int $level, string $message, \Exception $e = null) 
	{
		// haven't implemented storing to database
		// haven't implemented handling exception objects yet

		// check if it should be logged to terminal
		if (self::$development) {
			$levelTitle = DebugLevel::getTitle($level);

			echo "DEBUG [{$levelTitle}]: {$message}", PHP_EOL;
		}
	}

}

Logger::init(); // in case the autoloader does not call init().