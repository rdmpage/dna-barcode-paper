<?php

$filename = 'ibol_records.txt';

$file_handle = fopen($filename, "r");

$row = 0;

$PROCESSID = 0;
$GB_ACCESSION = 0;

while (!feof($file_handle)) 
{
	$line = trim(fgets($file_handle));
	
	$parts = explode("\t", $line);
		
	if ($row > 0)
	{
		$PROCESSID++;
		if ($parts[2] != '')
		{
			$GB_ACCESSION++;
		}
	}
	
	
	$row++;
	
	//if ($row == 10) break;
	
	if ($row % 1000 == 0) echo $row . "\n";
}

echo "$row $PROCESSID $GB_ACCESSION\n";

?>

