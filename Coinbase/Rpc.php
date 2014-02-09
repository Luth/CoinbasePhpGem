<?php

class Coinbase_Rpc
{
	private $_requestor;
	private $_apiKey;
	private $_apiSecret;

	public function __construct($requestor, $apiKey = null, $apiSecret = null)
	{
		$this->_requestor = $requestor;
		$this->_apiKey    = $apiKey;
		$this->_apiSecret = $apiSecret;
	}

	public function request($method, $url, $params)
	{
		if ($this->_apiKey === null) {
			throw new Coinbase_ApiException("Invalid API key", 500, "An invalid API key was provided.");
		}

		$url   = Coinbase::API_BASE . $url;
		$nonce = (int)(microtime(true) * 100);

		// Create query string
		$queryString = http_build_query($params);

		// Initialize CURL
		$curl     = curl_init();
		$curlOpts = array();

		// HTTP method
		$method = strtolower($method);
		if ($method == 'get') {
			$curlOpts[CURLOPT_HTTPGET] = 1;
			$url .= "?" . $queryString;
		} else if ($method == 'post') {
			$curlOpts[CURLOPT_POST]       = 1;
			$curlOpts[CURLOPT_POSTFIELDS] = $queryString;
		} else if ($method == 'delete') {
			$curlOpts[CURLOPT_CUSTOMREQUEST] = "DELETE";
			$url .= "?" . $queryString;
		} else if ($method == 'put') {
			$curlOpts[CURLOPT_CUSTOMREQUEST] = "PUT";
			$curlOpts[CURLOPT_POSTFIELDS]    = $queryString;
		}

		// Headers
		$headers = array(
			'ACCESS_KEY: ' . $this->_apiKey,
			'ACCESS_NONCE: ' . $nonce,
			'ACCESS_SIGNATURE: ' . hash_hmac("sha256", $nonce . $url . $queryString, $this->_apiSecret)
		);

		// CURL options
		$curlOpts[CURLOPT_URL]            = $url;
		$curlOpts[CURLOPT_HTTPHEADER]     = $headers;
		$curlOpts[CURLOPT_CAINFO]         = dirname(__FILE__) . '/ca-coinbase.crt';
		$curlOpts[CURLOPT_RETURNTRANSFER] = true;

		// Do request
		curl_setopt_array($curl, $curlOpts);
		$response = $this->_requestor->doCurlRequest($curl);

		// Decode response
		try {
			$json = json_decode($response['body']);
		} catch (Exception $e) {
			throw new Coinbase_ConnectionException("Invalid response body", $response['statusCode'], $response['body']);
		}
		if ($json === null) {
			throw new Coinbase_ApiException("Invalid response body", $response['statusCode'], $response['body']);
		}
		if (isset($json->error)) {
			throw new Coinbase_ApiException($json->error, $response['statusCode'], $response['body']);
		} else if (isset($json->errors)) {
			throw new Coinbase_ApiException(implode($json->errors, ', '), $response['statusCode'], $response['body']);
		}

		return $json;
	}
}
