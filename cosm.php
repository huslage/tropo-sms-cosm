<?php 

define(COSM_HEADER, "X-ApiKey: ");
define(COSM_API_KEY, "aBUpZ_sy_SeTdiYGic-7m4fkGMOSAKxjd00xR1BFc3lkST0g");
define(COSM_BASE_URL, "http://api.cosm.com/v2/feeds/");

function parseReport($inreport) {
	list($feedid,$outreport) = preg_split("/\r\n/", $inreport);
	return array($feedid,$outreport);
}

function genCosmJSON($params) {
	$encodedCosm = '{"version":"1.0.0","datastreams":[';
	foreach ( $params as $stream ) {
		list($streamid,$curval) = preg_split("/,/", $stream);
      		$encodedCosm .= '{"id":"'.$streamid.'", "current_value":"'.$curval.'"},';
	}
  	$encodedCosm .=']}';
	return $encodedCosm;
}

function sendToCosm($feedid,$data,$method="POST") {
	_log("*** Sending Feed ID: ".$feedid." to Cosm with values: ".$data);

	$url = COSM_BASE_URL.$feedid."/";

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	if($method == "POST") {

		$data = "";
		foreach($params as $key => $value) {
			$data .= "$key=$value&";
		}

		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: multipart/form-data', COSM_HEADER.COSM_API_KEY, 'Content-length: '.strlen($data)));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}

	// Execute.
	$output = curl_exec($ch);
	$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	// Return results.
	if($code != '200') {
		throw new $exceptionType($exceptionMessage);
	}
	else {
		$result = $metod == "POST" ? true : $output;
		return $result;
	}
}

try {
	_log("**** Hi there ****");
	// Get the address submited by the user.
	$report = ask("", array("choices" => "[ANY]"));
       _log("*** Got: ".$report->value."***");

	list($feedid,$params) = parseReport($report->value);
	$encoded=genCosmJSON($params);
	sendToCosm($feedid,$encoded);
}

catch (Exception $ex) {
	_log("*** ". $ex->getMessage() . " ***");
	say("Sorry, could not submit your report.");
	hangup();
}
?>
