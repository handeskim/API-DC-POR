<?php
$username = 'crondomain';
$password = 'EerhfPCH7#';
$soap_location = 'https://101.99.20.164:8080/remote/index.php';
$soap_uri = 'https://101.99.20.164:8080/remote/';
$client = new SoapClient(null, array('location' => $soap_location,
                                     'uri'      => $soap_uri,
									 'trace' => 1,
									 'exceptions' => 1));


?>
