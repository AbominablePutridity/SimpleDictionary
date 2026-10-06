<?php

namespace controller;

class Request
{
    private $method;
    private $requestUri;
    private $rawBody;
    private $queryParams;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->rawBody = file_get_contents('php://input'); // read the body of request (for example - json)
        $this->queryParams = $_GET; // gate the query params from __GET
    }

    public function decodeBody(): array
    {
        return json_decode($this->rawBody, true) ?? []; // decode the json
    }

    /**
     * @return mixed
     */
    public function getMethod(): mixed
    {
        return $this->method;
    }

    /**
     * @return array|false|int|string|null
     */
    public function getRequestUri(): false|array|int|string|null
    {
        return $this->requestUri;
    }

    /**
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * @return false|string
     */
    public function getRawBody(): false|string
    {
        return $this->rawBody;
    }
}