<?php

foreach (scandir('Maildir') as $level1) {
	if (in_array($level1, array('.', '..'))) {
		continue;
	} elseif (!is_dir('Maildir/' . $level1)) {
		continue;
	} elseif (substr($level1, 0, 1) !== '.' && !in_array($level1, array('cur', 'new'))) {
		continue;
	} else {
		print $level1 . "\n";
		foreach (scandir('Maildir/' . $level1) as $level2) {
			if (in_array($level2, array('cur', 'new'))) {
				print "  $level2\n";
				foreach (scandir('Maildir/' . $level1 . '/' . $level2) as $level3) {
					if (substr($level3, 0, 1) != '.') {
						print "    $level3\n";
						fix_file("Maildir/$level1/$level2/$level3");
					}
				}
			}
		}
	}
}

function fix_file($fname) {
	$fhandle = fopen($fname, 'r');
	$rcvheader = FALSE;
	while (($line = fgets($fhandle, 4096)) !== FALSE) {
		if (substr($line, 0, 9) == 'Received:') {
			$rcvheader = TRUE;
		} elseif (trim(substr($line, 0, 1)) !== '') {
			$rcvheader = FALSE;
		}
		if ($rcvheader && preg_match('/[A-Za-z]{3},[\s]*([\d]{1,2}) ([A-Za-z]{3}) ([\d]{4}) ([\d]{2}:[\d]{2}:[\d]{2}) ([-+][\d]{4})/', $line, $matches)) {
			$date = strtotime($matches[0]);
			print "      Date string: " . $matches[0] . "\n";
			print "      from " . date('Ymd-His', filemtime($fname)) . " to " . date('Ymd-His', $date) . "\n";
			return;
		}
	}
}

?>
