<?php

namespace StackGuru\Commands\Service;

class Status extends \StackGuru\Command implements \StackGuru\CommandInterface
{
    const COMMAND_NAME = "status";
    const DESCRIPTION = "Shows information about the bot running, memory usage.";


    private function time_elapsed () : string
    {
    	$secs = time() - STARTUP_TIME;

    	// Can be greatly optimized!
	    $bit = [
	        " year"     => $secs / 31556926 % 12,
	        " week"     => $secs / 604800 % 52,
	        " day"      => $secs / 86400 % 7,
	        " hour"     => $secs / 3600 % 24,
	        " minute"   => $secs / 60 % 60,
	        " second"	=> $secs % 60
	    ];

	    foreach ($bit as $k => $v) {
	        if($v > 1) {
	        	$ret[] = $v . $k . 's';
	        }

	        if($v == 1) {
	        	$ret[] = $v . $k;
	        }
        }

	    array_splice($ret, count($ret)-1, 0, 'and');

	    if ("and" === $ret[0]) {
	    	unset($ret[0]);
	    }

	    return join(' ', $ret);
    }


    public function process (string $query, \StackGuru\CommandContext $ctx = null) : string
    {
    	$memoryLimitKiB	= round(memory_get_peak_usage(true) / 1024);
    	$memoryLimitMiB	= number_format($memoryLimitKiB / 1024, 1, ',', ' ');
    	$memoryKiB 		= round(memory_get_usage(false) / 1024);
        $memoryMiB 		= number_format($memoryKiB / 1024, 1, ',', ' ');
        $elapsedTime 	= trim($this->time_elapsed());


        $response = "```markdown\n" .
        			"# Memory\n" .
        			sprintf("* Used:      %20s", "{$memoryKiB}KiB ({$memoryMiB}MiB)") . "\n" .
        			sprintf("* Allocated: %20s", "{$memoryLimitKiB}KiB ({$memoryLimitMiB}MiB)") . "\n" .
        			"\n" .
        			"# History\n" .
        			sprintf("* Runtime:   %20s", $elapsedTime) . "\n" .
        			"\n" .
        			"```";

        return $response;
    }
}
