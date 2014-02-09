<?php

class Coinbase_Exception extends Exception
{
    public function __construct($message, $http_code=null, $response=null)
    {
        parent::__construct($message);
        $this->http_code = $http_code;
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getHttpCode()
    {
        return $this->http_code;
    }
}


class Coinbase_TokensExpiredException extends Coinbase_Exception
{
}

class Coinbase_ConnectionException extends Coinbase_Exception
{
}

class Coinbase_ApiException extends Coinbase_Exception
{
}
