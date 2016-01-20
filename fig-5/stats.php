<?php


//--------------------------------------------------------------------------------------------------
// MySQL
require_once(dirname(__FILE__).'/adodb5/adodb.inc.php');

$db = NewADOConnection('mysql');
$db->Connect(
	"localhost", 	# machine hosting the database, e.g. localhost
	'root', 		# MySQL user name
	'', 			# password
	'ncbi'		# database
	);

// Ensure fields are (only) indexed by column name
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

$species = true;
$propername = true; // true if we want to count well-formed Latin names
$propername = false; // false if we want to count all species
$bold = true;

echo "Year\tBacteria\tInvertebrates\tMammals\tPhages\tPlants\tPrimates\tRodents\tSynthetic\tUnassigned\tViruses\tVertebrates\tEnvironmental samples\n";

for ($year = 1995; $year < 2016; $year++)
{

//	$sql = "SELECT COUNT(tax_id) AS c, ncbi_nodes.division_id FROM tax_id_by_mdat
	$sql = "SELECT COUNT(tax_id) AS c, ncbi_nodes.division_id FROM tax_id_by_edat
	INNER JOIN ncbi_names USING(tax_id)
	INNER JOIN ncbi_nodes USING(tax_id)
	WHERE month_start LIKE '" . $year . "-%'
	AND (ncbi_names.name_class = 'scientific name')";
	
	if ($species)
	{
		$sql .= " AND (ncbi_nodes.rank = 'species')";
	}
	
	if ($propername)
	{
		// name isn't a species name
		$sql .= " AND NOT ncbi_names.name_txt REGEXP '^[A-Z][a-z]+( [[.left-parenthesis.]][A-Z][a-z]+[[.right-parenthesis.]])? [a-z][a-z]+$'";
		// ignore hybrids
		$sql .= " AND NOT ncbi_names.name_txt REGEXP ' x '";
	}

	if ($bold)
	{
		$sql .= " AND ncbi_names.name_txt REGEXP 'BOLD'";
	}


	$sql .= " GROUP BY ncbi_nodes.division_id";
		
	$divisions = array();
	for ($i = 0; $i < 12; $i++)
	{
		$divisions[$i] = 0;
	}
	
	$result = $db->Execute($sql);
	if ($result == false) die("failed [" . __LINE__ . "]: " . $sql);
	while (!$result->EOF) 
	{
		$divisions[$result->fields['division_id']] = $result->fields['c'];
		
		$result->MoveNext();	
	
	}
	
	//print_r($divisions);
	
	echo $year . "\t" . join("\t", $divisions) . "\n";
}

?>