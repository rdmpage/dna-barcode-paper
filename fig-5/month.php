<?php

// get monthly counts of taxa added to NCBI taxonomy, and retrieve the tax_ids

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


$start_year = 1995;
$end_year = 2015;


for ($year = $start_year; $year <= $end_year; $year++)
{
	for ($month = 1; $month <= 12; $month++)
	{
		$last_day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		
		$data = array(
			'db' => 'taxonomy',
			'mindate' => $year . '/' . $month . '/01',
			'maxdate' => $year . '/' . $month . '/' . $last_day,
			'retmax' => 100000,
//			'datetype' => 'mdat'  // only mdat and edat work
			'datetype' => 'edat'  // only mdat and edat work
		);
		
		$url = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?' . http_build_query($data);
		
		//echo $url . "\n";
		
		$xml = get($url);
		
		//echo $xml;
		
		$dom= new DOMDocument;
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
        
        $ids = array();
        
        $nodeCollection = $xpath->query ('//eSearchResult/IdList/Id');     
        foreach($nodeCollection as $node)
        {
          	$ids[] = $node->firstChild->nodeValue;
        }
        
        //print_r($ids);
        foreach ($ids as $id)
        {
        	echo "$year-$month-01\t$year-$month-$last_day\t$id\n";
        }
  
		
		// pause between
		{
			$rand = 1000000;
			usleep($rand);
		}	
		
		
		
	}
}


?>