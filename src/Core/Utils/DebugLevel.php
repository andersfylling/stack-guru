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
			case ALL: 		$title = "ALL"; 	break;
			case CONFIG: 	$title = "CONFIG"; 	break;
			case FINEST: 	$title = "FINEST"; 	break;
			case FINER: 	$title = "FINER"; 	break;
			case FINE: 		$title = "FINE"; 	break;
			case INFO: 		$title = "INFO"; 	break;
			case WARNING: 	$title = "WARNING"; break;
			case SEVERE: 	$title = "SEVERE"; 	break;
			
			default:		$title = "ALL";
		}

		return $title;
	}
}