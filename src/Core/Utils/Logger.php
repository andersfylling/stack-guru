<?php
declare(strict_types=1);

namespace StackGuru\Core\Utils;


class Logger extends StackGuru\Core\Database
{

	private $classname = "";

	protected static $path = __DIR__ . "/logger_logs"; // DEFAULT



	public function __construct(string $name) 
	{
		$this->classname = $name; // should also store namespace somehow. :/
	}


	public static function setPath(string $fullPath) 
	{
		self::$path = $fullPath;
	}

	/**
	 * Add a log entry to a file.
	 * 
	 * @param  string $file  [full path, not relative as it might cause weirdo issues.]
	 * @param  string $entry [Log entry to be written to log file.]
	 * @return [boolean]     [true if successfully written to file. Based on return value of file_put_contents.]
	 */
	public function appendToLogfile(string $entry) : bool
	{
		// ----- FILE BASED VERSION -----
		// Add new line to end of the entry.
		//$entry .= PHP_EOL;
		
		// Write the contents to the file, 
		// using the FILE_APPEND flag to append the content to the end of the file
		// and the LOCK_EX flag to prevent anyone else writing to the file at the same time.
		//$result = file_put_contents(self::$path . '/' . $this->classname . ".log", $entry, FILE_APPEND | LOCK_EX);

		// file_put_contents() returns the number of bytes that were written to the file, 
		// or FALSE on failure.
		//return false === $result ? false : true;
		// ----- END ------
		
		$ 
		
	}


	public function log(int $level, string $message, $object = null) 
	{
		if (false === $this->appendToLogfile($message)) {
			echo "ERROR! Unable to log message: {$message}", PHP_EOL;
		}
	}

}