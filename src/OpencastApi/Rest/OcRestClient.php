<?php
namespace OpencastApi\Rest;

use GuzzleHttp\Client;

class OcRestClient extends Client
{
    private $baseUri;
    private $username;
    private $password;
    private $timeout = 0;
    private $connectTimeout = 0;
    private $version;
    private $headerExceptions = [];
    private $additionalHeaders = [];
    private $noHeader = false;
    /* 
        $config = [
            'url' => 'https://develop.opencast.org/',       // The API url of the opencast instance (required)
            'username' => 'admin',                          // The API username. (required)
            'password' => 'opencast',                       // The API password. (required)
            'timeout' => 0,                                 // The API timeout. In seconds (default 0 to wait indefinitely). (optional)
            'connect_timeout' => 0,                         // The API connection timeout. In seconds (default 0 to wait indefinitely) (optional)
            'version' => null                               // The API Version. (Default null). (optional)
        ]
    */
    public function __construct($config)
    {
        $this->baseUri = $config['url'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        if (isset($config['timeout'])) {
            $this->timeout = $config['timeout'];
        }
        if (isset($config['connect_timeout'])) {
            $this->connectTimeout = $config['connect_timeout'];
        }

        if (isset($config['version'])) {
            $this->setVersion($config['version']);
        }

        parent::__construct([
            'base_uri' => $this->baseUri
        ]);
    }

    public function registerHeaderException($header, $path) {
        $path = ltrim($path, '/');
        if (!isset($this->headerExceptions[$header]) || !in_array($path, $this->headerExceptions[$header])) {
            $this->headerExceptions[$header][] = $path;
        }
    }

    public function registerAdditionalHeader($header, $value)
    {
        $this->additionalHeaders[$header] = $value;
    }

    public function enableNoHeader()
    {
        $this->noHeader = true;
    }

    private function addRequestOptions($uri, $options)
    {

        // Perform a temp no header request.
        if ($this->noHeader) {
            $this->noHeader = false;
            return array_merge($options , ['headers' => null]);
        }

        $generalOptions = [];
        // Auth
        if (!empty($this->username) && !empty($this->password)) {
            $generalOptions['auth'] = [$this->username, $this->password];
        }

        // Timeout
        if (isset($this->timeout)) {
            $generalOptions['timeout'] = $this->timeout;
        }

        // Connect Timeout
        if (isset($this->connectTimeout)) {
            $generalOptions['connect_timeout'] = $this->connectTimeout;
        }

        // Opencast API Version.
        if (!empty($this->version)) {
            $this->registerAdditionalHeader('Accept', "application/v{$this->version}+json");
        }

        if (!empty($this->additionalHeaders)) {
            $generalOptions['headers'] = $this->additionalHeaders;
            $this->additionalHeaders = [];
            foreach ($generalOptions['headers'] as $header => $value) {
                $path = explode('/', ltrim($uri, '/'))[0];
                if (isset($this->headerExceptions[$header]) && in_array($path, $this->headerExceptions[$header]) ) {
                    unset($generalOptions['headers'][$header]);
                }
            }
        }

        $requestOptions = array_merge($generalOptions, $options);
        return $requestOptions;
    }

    public function hasVersion($version)
    {
        if (empty($this->version)) {
            try {
                $defaultVersion = $this->performGet('/api/version/default');
                if (!empty($defaultVersion['body']) && isset($defaultVersion['body']->default)) {
                    $this->setVersion(str_replace(['application/', 'v', '+json'], ['', '', ''], $defaultVersion['body']->default));
                } else {
                    return false;
                }
            } catch (\Throwable $th) {
                return false;
            }
        }
        return version_compare($this->version, $version, '>=');

    }

    private function setVersion($version)
    {
        $version = str_replace(['application/', 'v', '+json'], ['', '', ''], $version);
        $this->version = $version;
    }

    public function getVersion() {
        return $this->version;
    }

    private function resolveResponseBody(string $body)
    {
        $result = json_decode($body);
        if ($result !== null) {
            return $result;
        }
        // TODO: Here we can add more return type if needed...

        if (!empty($body)) {
            return $body;
        }

        return null;
    }

    private function returnResult($response)
    {
        $result = [];
        $result['code'] = $response->getStatusCode();
        $result['reasone'] = $response->getReasonPhrase();
        $body = '';
        if ($result['code'] < 400 && !empty((string) $response->getBody())) {
            $body = $this->resolveResponseBody((string) $response->getBody());
        }
        $result['body'] = $body;

        $location = '';
        if ($response->hasHeader('Location')) {
            $location = $response->getHeader('Location');
        }
        $result['location'] = $location;
        return $result;
    }

    public function performGet($uri, $options = [])
    {
        $response = $this->get($uri, $this->addRequestOptions($uri, $options));
        return $this->returnResult($response);
    }

    public function performPost($uri, $options = [])
    {
        $response = $this->post($uri, $this->addRequestOptions($uri, $options));
        return $this->returnResult($response);
    }


    public function performPut($uri, $options = [])
    {
        $response = $this->put($uri, $this->addRequestOptions($uri, $options));
        return $this->returnResult($response);
    }

    public function performDelete($uri, $options = [])
    {
        $response = $this->delete($uri, $this->addRequestOptions($uri, $options));
        return $this->returnResult($response);
    }

    public function getFormParams($params)
    {
        $options = [];
        $formParams = [];
        foreach ($params as $field_name => $field_value) {
            $formParams[$field_name] = (!is_string($field_value)) ? json_encode($field_value) : $field_value;
        }
        if (!empty($formParams)) {
            $options['form_params'] = $formParams;
        }
        return $options;
    }

    public function getMultiPartFormParams($params)
    {
        $options = [];
        $multiParams = [];
        foreach ($params as $field_name => $field_value) {
            $multiParams[] = [
                'name' => $field_name,
                'contents' => $field_value
            ];
        }
        if (!empty($multiParams)) {
            $options['multipart'] = $multiParams;
        }
        return $options;
    }

    public function getQueryParams($params)
    {
        $options = [];
        $queryParams = [];
        foreach ($params as $field_name => $field_value) {
            $value = is_bool($field_value) ? json_encode($field_value) : $field_value;
            $queryParams[$field_name] = $value;
        }
        if (!empty($queryParams)) {
            $options['query'] = $queryParams;
        }
        return $options;
    }
}
?>