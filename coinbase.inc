<?php

if(!function_exists('curl_init')) {
    throw new Exception('The Coinbase client library requires the CURL PHP extension.');
}

require(dirname(__FILE__) . '/Coinbase/Exception.php');
require(dirname(__FILE__) . '/Coinbase/Coinbase.php');
require(dirname(__FILE__) . '/Coinbase/Requestor.php');
require(dirname(__FILE__) . '/Coinbase/Rpc.php');
