# opencast-php-library
This php composer package is meant to provide an unified easy-to-use Opencast RESTful API library. It has been designed to make most of commonly used REST Endpoints available to the developers of thirt-party applications mainly LMSes such as Stud.IP, Moodle and ILIAS.

# Requisitions
<b>PHP Version 7.2.5 or above</b> as well as <b>cURL</b> are required. Additionaly, the [requirements](https://docs.guzzlephp.org/en/stable/overview.html#requirements) of [guzzlehttp/guzzle](https://packagist.org/packages/guzzlehttp/guzzle#7.0.0) must be fullfiled.

# Installation
`composer require elan-ev/opencast-api`

# Basic Usage
There are 2 approaches to use the Opencast REST Endpoints from this library:

1. The first one is via the Generic `OpencastApi\OpenCast` which contains all available opencast endpoints (which are capable with the API version defined in the config). The advantage of using this approach would be a better control over all available endpoints. <b>(Recommended)</b>

<b>NOTE:</b> When your Opencast setup is configured as <b>dual node</b>, one responsible for main functionalities and the other one responsible for presentation <b>(a.k.a "engage node")</b>, you can pass another set of configuration as the second parameter when instantiating the `OpencastApi\OpenCast`. Initially, the engage node only takes care of search endpoint.
```php
$config = [
      'url' => 'https://develop.opencast.org/',       // The API url of the opencast instance (required)
      'username' => 'admin',                          // The API username. (required)
      'password' => 'opencast',                       // The API password. (required)
      'timeout' => 0,                                 // The API timeout. In seconds (default 0 to wait indefinitely). (optional)
      'connect_timeout' => 0                          // The API connection timeout. In seconds (default 0 to wait indefinitely) (optional)
      'version' => null                               // The API Version. (Default null). (optional)
];

$engageConfig = [
      'url' => 'https://develop.opencast.org/',       // The API url of the opencast instance (required)
      'username' => 'admin',                          // The API username. (required)
      'password' => 'opencast',                       // The API password. (required)
      'timeout' => 0,                                 // The API timeout. In seconds (default 0 to wait indefinitely). (optional)
      'connect_timeout' => 0                          // The API connection timeout. In seconds (default 0 to wait indefinitely) (optional)
      'version' => null                               // The API Version. (Default null). (optional)
];

use OpencastApi\OpenCast;

// In case of dual oc setup
$opencastDualApi = new OpenCast($config, $engageConfig);
// Or simply 
$opencastApi = new OpenCast($config);

// Accessing Event Endpoints to get all events
$events = [];
$eventsResponse = $opencastApi->eventsApi->getAll();
if ($eventsResponse['code'] == 200) {
      $events = $eventsResponse['body'];
}

// Accessing Series Endpoints to get all series
$series = [];
$seriesResponse = $opencastApi->seriesApi->getAll();
if ($seriesResponse['code'] == 200) {
      $series = $seriesResponse['body'];
}

// ...
```

2. The second approach is to instatiate each REST endpoint class, which are located under `OpencastApi\Rest\` namespace, when needed, but the down side of this would be that it needs a `OpencastApi\Rest\OcRestClient` instance as its parameter. The advantage of this approach might be the methods' definitions in the IDE.

```php
$config = [
      'url' => 'https://develop.opencast.org/',       // The API url of the opencast instance (required)
      'username' => 'admin',                          // The API username. (required)
      'password' => 'opencast',                       // The API password. (required)
      'timeout' => 0,                                 // The API timeout. In seconds (default 0 to wait indefinitely). (optional)
      'connect_timeout' => 0                          // The API connection timeout. In seconds (default 0 to wait indefinitely) (optional)
      'version' => null                               // The API Version. (Default null). (optional)
];


use OpencastApi\Rest\OcRestClient;
use OpencastApi\Rest\OcEventsApi;
use OpencastApi\Rest\OcSeriesApi;

// Get a client object.
$opencastClient = OcRestClient($config);

// To get events.
$opencastEventsApi = OcEventsApi($opencastClient);
$events = [];
$eventsResponse = $opencastEventsApi->getAll();
if ($eventsResponse['code'] == 200) {
      $events = $eventsResponse['body'];
}

// To get series.
$opencastSeriesApi = OcSeriesApi($opencastClient);
$series = [];
$seriesResponse = $opencastSeriesApi->getAll();
if ($seriesResponse['body'] == 200) {
      $series = $seriesResponse['body'];
}

// ...
```
# Configuration
The configuration is type of `Array` and has to be defined as follows:
```php
$config = [
      'url' => 'https://develop.opencast.org/',       // The API url of the opencast instance (required)
      'username' => 'admin',                          // The API username. (required)
      'password' => 'opencast',                       // The API password. (required)
      'timeout' => 0,                                 // The API timeout. In seconds (default 0 to wait indefinitely). (optional)
      'connect_timeout' => 0                          // The API connection timeout. In seconds (default 0 to wait indefinitely) (optional)
      'version' => null                               // The API Version. (Default null). (optional)
];
```
NOTE: the configuration for `engage` node responsible for search has to follow the same definition as normal config. But in case any parameter is missing, the value will be taken from the main config param. 

# Response
The return result of each call is an `Array` containing the following information:
```php
[
      'code' => 200,                // The status code of the response
      'body' => '',                 // The result of the response. It can be type of string, array or object ('' || [] || {})
      'reason' => 'OK',             // The reason/message of response
      'location' => '',             // The location header of the response when available
]
```
# Filters and Sorts
Filters and Sorts must be define as associative `Array`, as follows:
```php
// for example:

$filters = [
      'title' => 'The title name',
      'creator' => 'opencast admin'
];

$sorts = [
      'title' => 'DESC',
      'startDate' => 'ASC'
];
```
# Available Opencast REST Service Endpoint

- `/api/*`: all known API endpoints of Opencast are available to be used in this library. [API Endpoints definitions WiKi](https://github.com/elan-ev/opencast-php-library/wiki/API-Endpoints)
  
- `/ingest/*`: all known Ingest endpoints are available. [Ingest Endpoint definitions WiKi](https://github.com/elan-ev/opencast-php-library/wiki/OcIngest)

- `/services/services.json`: only services.json is available. [Services Endpoint definitions WiKi](https://github.com/elan-ev/opencast-php-library/wiki/OcServices)

- `/search/{episode | lucene | series}.{json | xml}`: only episode, lucene and series in JSON or XML format are available. [Search Endpoint definitions WiKi](https://github.com/elan-ev/opencast-php-library/wiki/OcSearch)

- `/capture-admin/*`: all known Capture Admin endpoints are available. [Capture Admin Endpoint definitions WiKi](https://github.com/elan-ev/opencast-php-library/wiki/OcCaptureAdmin)

- `/admin-ng/event/delete`: only delete endpoint is available. [Admin Ng Endpoint definitions WiKi](https://github.com/elan-ev/opencast-php-library/wiki/OcEventAdminNg)

- `/recordings/*`: all known Recording endpoints are available. [Recordings Endpoint definitions WiKi](https://github.com/elan-ev/opencast-php-library/wiki/OcRecordings)

- `/series/*`: all known Series endpoints are available. [Series Endpoint definitions WiKi](https://github.com/elan-ev/opencast-php-library/wiki/OcSeries)

- `/workflow/*`: all known Workflow endpoints are available. [Workflow Endpoint definitions WiKi](https://github.com/elan-ev/opencast-php-library/wiki/OcWorkflow)


# Naming convention
## Classes: 
Apart from 'OpenCast' class, all other classes under `OpencastApi\Rest\` namespace start with `Oc` followed by the name and the endpoint category. For example:
- `OcEventsApi` contains 3 parts including Oc + Endpoint Name (Events) + Endpoint Category (Api)
- `OcServices` contains 2 parts including Oc + Endpoint Name/Category (Services)

## OpenCast class properties:
The naming convetion to access the endpoint subclasses from `OpencastApi\OpenCast` as its properties, includes the name of the class without `Oc` in camelCase format. For example:
```php
use OpencastApi\OpenCast;
$config = [/*the config*/];
$opencast = new OpenCast($config);

// Accessing OcEventsApi would be like: (without Oc and in camelCase format)
$ocEventsApi = $opencast->eventsApi; 
```
# References
- <a href="https://develop.opencast.org/rest_docs.html" target="_blank">Main Opencast REST Service Documentation</a>
- <a href="https://docs.opencast.org/develop/developer/#api/#_top" target="_blank">Detailed Opencast REST API Endpoints Documentation</a>
