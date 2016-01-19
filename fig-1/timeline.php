<?php

// get yearly counts of names for select groups using BioNames

$config=array();

// Fill these in
$config['username'] = '';
$config['password'] = '';


//--------------------------------------------------------------------------------------------------
/**
 * @brief Test whether HTTP code is valid
 *
 * HTTP codes 200 and 302 are OK.
 *
 * For JSTOR we also accept 403
 *
 * @param HTTP code
 *
 * @result True if HTTP code is valid
 */
function HttpCodeValid($http_code)
{
	if ( ($http_code == '200') || ($http_code == '302') || ($http_code == '403'))
	{
		return true;
	}
	else{
		return false;
	}
}


//--------------------------------------------------------------------------------------------------
/**
 * @brief GET a resource
 *
 * Make the HTTP GET call to retrieve the record pointed to by the URL. 
 *
 * @param url URL of resource
 *
 * @result Contents of resource
 */
function get($url, $userAgent = '', $timeout = 0)
{
	global $config;
	
	$data = '';
	
	$ch = curl_init(); 
	curl_setopt ($ch, CURLOPT_URL, $url); 
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($ch, CURLOPT_FOLLOWLOCATION,	1); 
	//curl_setopt ($ch, CURLOPT_HEADER,		  1);  
	
	if ($userAgent != '')
	{
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
	}	
	
	if ($timeout != 0)
	{
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	}
	
	if ($config['proxy_name'] != '')
	{
		curl_setopt ($ch, CURLOPT_PROXY, $config['proxy_name'] . ':' . $config['proxy_port']);
	}
	
			
	$curl_result = curl_exec ($ch); 
	
	//echo $curl_result;
	
	if (curl_errno ($ch) != 0 )
	{
		echo "CURL error: ", curl_errno ($ch), " ", curl_error($ch);
	}
	else
	{
		$info = curl_getinfo($ch);
		
		 //$header = substr($curl_result, 0, $info['header_size']);
		//echo $header;
		
		
		$http_code = $info['http_code'];
		
		//echo "<p><b>HTTP code=$http_code</b></p>";
		
		if (HttpCodeValid ($http_code))
		{
			$data = $curl_result;
		}
	}
	return $data;
}


$years = array();

$group_list = array();

$groups = array(
	array("Animalia"),
	array("Animalia","Mollusca","Gastropoda"), // Gastropods
	array("Animalia","Arthropoda","Arachnida","Araneae"), // spiders
	array("Animalia","Arthropoda","Insecta","Hymenoptera"), // Hymenoptera
	array("Animalia","Nematoda"),
	array("Animalia","Chordata","Vertebrata","Aves")// birds
	);
	
	
foreach ($groups as $group)
{
	global $config;
	
	$group_label = $group[count($group) - 1];
	
	$group_list[] = $group_label;
	
	$url = 'https://' . $config['username'] . ':' . $config['password'] . '@rdmpage.cloudant.com/bionames/_design/ion/_view/group';
	
	$parameters = array(
		'group_level' => 2,
		'start_key' => json_encode(array($group, 0)),
		'end_key' => json_encode(array($group, 2015))
		);
		
	$url .= '?' . http_build_query($parameters);
	
	$json = get($url);
	
	//echo $json;
	
	$obj = json_decode($json);
	
	foreach ($obj->rows as $row)
	{
		$year = $row->key[1];
		$count = $row->value;
		
		if (!isset($years[$year]))
		{
			$years[$year] = array();
		}
		$years[$year][$group_label] = $count;
	}
	
}

//print_r($years);

echo "Year\t" . join("\t", $group_list) . "\n";
for ($year = 1864; $year < 2016; $year++)
{
	echo $year;
	foreach ($group_list as $g)
	{
		echo "\t";
		if (isset($years[$year][$g]))
		{
			echo $years[$year][$g];
		}
	}
	echo "\n";
}
	



?>