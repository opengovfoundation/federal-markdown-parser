<?php
	require_once('HouseImport.php');

	$filename = $argv[1];

	
	$xml = file_get_contents($filename);

	$houseImport = new HouseImport($xml);

	$md = $houseImport->convert();

	file_put_contents('out.md', $md);

	echo "Saved markdown to `out.md`\n";


