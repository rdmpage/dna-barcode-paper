<?php


$config=array();

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

	if (1)
	{
		curl_setopt ($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
	}
	else
	{
		// Set cookie manually if we need to
		curl_setopt($ch, CURLOPT_COOKIE, 'ispCPSESSID=lcn5n45m86qg3sul45tvuvb8m7');
	}
	
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


$dates = array(
'1990/01/01',
'2009/12/31',
'2010/03/31',
'2010/06/30',
'2010/09/30',
'2010/12/31',
'2011/03/31',
'2011/06/30',
'2011/09/30',
'2011/12/31',
'2012/03/31',
'2012/06/30',
'2012/09/30',
'2012/12/31',
'2013/03/31',
'2013/06/30',
'2013/09/13',
'2013/12/31',
'2014/03/31',
'2014/06/30',
'2014/09/30',
'2014/12/31',
'2015/03/31',
'2015/06/30',

// today
'2016/01/20'
);

$n = count($dates);

$total = 0;

for ($i = 1; $i < $n; $i++)
{
	// Get iBOL sequences
	$data = array(
		'db' 		=> 'nucleotide',
		'mindate' 	=> $dates[$i-1],
		'maxdate' 	=> $dates[$i],
		'datetype' 	=> 'pdat' ,
		'term' 		=> '37833[BioProject]'
	);
	
	/*
	// Get barcode sequences barcode[keyword] 
	$data = array(
		'db' 		=> 'nucleotide',
		'mindate' 	=> $dates[$i-1],
		'maxdate' 	=> $dates[$i],
		'datetype' 	=> 'pdat' ,
		'term' 		=> 'barcode[keyword] '
	);
	*/
	
	
	$url = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?' . http_build_query($data);
	
	//echo $url . "\n";
	
	$xml = get($url);
	
	//echo $xml;
	
	$dom= new DOMDocument;
	$dom->loadXML($xml);
	$xpath = new DOMXPath($dom);
	
	$nodeCollection = $xpath->query ('//eSearchResult/Count');     
	foreach($nodeCollection as $node)
	{
		$count = $node->firstChild->nodeValue;
		
		$total += $count;
	}

	echo $dates[$i] . "\t" . $count . "\n";		
}

echo "\t\t$total\n";


?>