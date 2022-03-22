# opencast-php-library
This php composer package is meant to provide an unified easy-to-use Opencast RESTful API library. It has been designed to make most of commonly used REST Endpoints available to the developers of thirt-party applications mainly LMSes such as Stud.IP, Moodle and ILIAS.

# Requisitions
<b>PHP Version 7.2.5 or above</b> as well as <b>cURL</b> are required. Additionaly, the <a href="https://docs.guzzlephp.org/en/stable/overview.html#requirements" target="_blank">requirements</a> of <a href="https://packagist.org/packages/guzzlehttp/guzzle#7.0.0" target="_blank">guzzlehttp/guzzle</a> must be fullfiled.

# Installation
`composer require elan/opencast-api`

# Basic Usage
There 2 approaches to use the Endpoints from this library:

1. The first one is via the Generic `OpencastAPI\OpenCast` which contains all available opencast endpoints. The advantage of using this approach would be a better control over all endpoints. <b>(Recommended)</b>

<b>NOTE:</b> This approach also supports dual node Opencast configuration, you can pass the secondary node config (a.k.a "engage" mostly use for search endpoint) to have it accessible throughout the `OpencastAPI\OpenCast` class.
```php

```

2. The second approach is to instatiate each endpoint class when needed, but the down side of this would be that it needs a `OpencastAPI\OcRestClient` instance as its parameter. The advantage of this approach might be the methods' definitions in the IDE.

```php

```
# Response
The return result of each call is an `Array` containing the following information:
```php

```


# Available Opencast REST Service Endpoint

- `/api/*`: all known API endpoints of Opencast are available to be used in this library. <a href="" target="_blank">Class Definitions WiKi for API endpoints</a>
  
- `/ingest/*` : all known Ingest endpoints are available. <a href="" target="_blank">Class Definitions WiKi for Ingest Endpoints</a>

- `/services/services.json` : <a href="" target="_blank">Class Definitions WiKi for Services Endpoints</a>

- `/search/{episode | lucene | series}.json` : <a href="" target="_blank">Class Definitions WiKi for Search Endpoints</a>
