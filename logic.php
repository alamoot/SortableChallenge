<?php

// For debugging purposes
function printArray($arr, $fieldName) {
	for ($i = 0; $i < count($arr); $i++) {
		echo $arr[$i][$fieldName] . "\n";
	}
}

function readInput($fileName) {
	$arr = array();
	$file = fopen($fileName, "r");
	if (!$file) 
		throw new Exception ("Couldn't open " . $fileName . "!!\n");
	
	while(!feof($file)) {
		$line = fgets($file);
		if($line)		// Don't handel an empty object (blank line at the end of the file)
			array_push($arr, json_decode($line, true));
	}
	if(!fclose($file))
		throw new Exception ("fclose failed for " . $fileName);
	return $arr;
}

try{
	// Clean the directory
	$outputFileName = "./output.txt";
	if(file_exists($outputFileName))
		if(!unlink($outputFileName))
			throw new Exception ("Couldn't delete output.txt");
	

	$prods = readInput("./products.txt");
	$lists = readInput("./listings.txt");
	//printArray($prods, "product_name");
	//echo "======================== \n";
	//printArray($lists, "title");

	$output = "";
	for ($i = 0; $i < count($prods); $i++) {	
		$listingMatches = array();
		$formattedProductName = strtoupper(trim($prods[$i]["product_name"]));
		$formattedProductName = str_replace("_", " ", $formattedProductName);
			
		for ($j = 0; $j < count($lists); $j++) {	
			$formattedListingTitle = strtoupper($lists[$j]["title"]);
			
			if(strpos($formattedListingTitle, $formattedProductName) !== false) {
				array_push($listingMatches, $lists[$j]);
			}
		}
		
		$insertObject = (object)[	//"count" => count($listingMatches),	//	for debugging
									"product_name" => $prods[$i]["product_name"], 
									"listings" => $listingMatches];
		$output .= json_encode($insertObject, true) . "\n";
	}

	$outputFile = fopen($outputFileName, "w");
	if(!$outputFile)
		throw new Exception ("Couldn't open " . $outputFileName . "!!\n");
		
	if(!fwrite($outputFile, $output))	
		throw new Exception ("Couldn't write to output.txt");	
	
	if(!fclose($outputFile))
		throw new Exception ("fclose failed for " . $outputFileName);
		
} catch(Exception $e) {
	echo "Caught Exception: " . $e->getMessage();
	echo "\nTerminating ...";
}

?>
