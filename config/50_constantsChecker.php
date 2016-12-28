<?php

const EXIT_MESSAGE = "\n---\nBot will now terminate itself.\n";

{
	/*
	 * All required constants, where example syntax can help the developer..
	 */
	$required = [
		"DEVELOPMENT" 			=> "how to use",
		"ST_DISCORD_SETTINGS" 	=> "Discord bot token. eg: MjQwNjE3NjQwMT1M4sDFSMDIx.C0RVjw.Pl8rrUDFsej_8Pn9q09vQk3O1ys",
		"ST_DATABASE_SETTINGS" 	=> "MySQL connection settings array [host=>'localhost',port=>3306,user=>'root',pass=>'',schema=>'stackGuru',file=>'schemafile.sql']"
	];

	/*
	 * Check that eah constant exists.
	 * If one has not been definedm print how its syntax should be and exit the script.
	 */
	foreach ($required as $constant => $explanation) {
		if (!defined($constant)) {
			echo "ERROR, Missing Constant: $explanation", '\n';

			exit(EXIT_MESSAGE);
		}
	}
}

