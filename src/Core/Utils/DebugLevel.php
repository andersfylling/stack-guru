<?php
declare(strict_types=1);

namespace StackGuru\Core\Utils;


class DebugLevel
{
	// Warning levels
	const ALL 		= 00;
	const CONFIG 	= 05;
	const FINEST 	= 10;
	const FINER 	= 20;
	const FINE 		= 30;
	const INFO 		= 40;
	const WARNING 	= 50;
	const SEVERE 	= 60;

	final public static function getTitle(int $level): string 
	{
		$title = "";

		switch($level) {
			case self::ALL: 	$title = "ALL"; 	break;
			case self::CONFIG: 	$title = "CONFIG"; 	break;
			case self::FINEST: 	$title = "FINEST"; 	break;
			case self::FINER: 	$title = "FINER"; 	break;
			case self::FINE: 	$title = "FINE"; 	break;
			case self::INFO: 	$title = "INFO"; 	break;
			case self::WARNING:	$title = "WARNING"; break;
			case self::SEVERE: 	$title = "SEVERE"; 	break;

			default:			$title = "UNDEFINED LEVEL";
			
		}

		return $title;
	}
}