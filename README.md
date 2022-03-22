# opencast-php-library
This php composer package is meant to provide an unified easy-to-use Opencast RESTful API library. It has been designed to make most of commonly used REST Endpoints available to the developers of thirt-party applications mainly LMSes such as Stud.IP, Moodle and ILIAS.

# Requisitions
<b>PHP Version 7.2.5 or above</b> as well as <b>cURL</b> are required. Additionaly, the <a href="https://docs.guzzlephp.org/en/stable/overview.html#requirements" target="_blank">requirements</a> of <a href="https://packagist.org/packages/guzzlehttp/guzzle#7.0.0" target="_blank">guzzlehttp/guzzle</a> must be fullfiled.

# Installation
`composer require elan/opencast-api`

# Basic Usage
There 2 approaches to use the Endpoints from this library:

1. The first one is via the Generic `OpencastAPI\OpenCast` which contains all available opencast endpoints. The advantage of using this approach would be a better control over all endpoints. <b>(Recommended)</b>

<b>NOTE:</b> When your Opencast setup is configured as <b>dual node</b>, one responsible for main functionalities and the other one responsible for presentation <b>(a.k.a "engage node")</b>, you can pass another set of configuration as the second parameter when instantiating the `OpencastAPI\OpenCast`. As of verion 1.0, the engage node takes care of search endpoint only.
```php
$config = [
      'url' => 'https://develop.opencast.org/',       // The API url of the opencast instance (required)
      'username' => 'admin',                          // The API username. (required)
      'password' => 'opencast',                       // The API password. (required)
      'timeout' => 30000,                             // The API timeout. In miliseconds (Default 30000 miliseconds or 30 seconds). (optional)
      'version' => null                               // The API Version. (Default null). (optional)
];

$engageConfig = [
      'url' => 'https://develop.opencast.org/',       // The API url of the opencast instance (required)
      'username' => 'admin',                          // The API username. (required)
      'password' => 'opencast',                       // The API password. (required)
      'timeout' => 30000,                             // The API timeout. In miliseconds (Default 30000 miliseconds or 30 seconds). (optional)
      'version' => null                               // The API Version. (Default null). (optional)
];

use OpencastAPI\OpenCast;

// In case of dual oc setup
$opencastDualApi = new OpenCast($config, $engageConfig);
// Or simply 
$opencastApi = new OpenCast($config);

// Accessing Event Endpoints to get all events
$events = [];
$eventsResponse = $opencastApi->eventsApi->getAll();
if ($eventsResponse['body'] == 200) {
      $events = $eventsResponse['body'];
}

// Accessing Series Endpoints to get all series
$series = [];
$seriesResponse = $opencastApi->seriesApi->getAll();
if ($seriesResponse['body'] == 200) {
      $series = $seriesResponse['body'];
}

// ...
```

2. The second approach is to instatiate each endpoint class when needed, but the down side of this would be that it needs a `OpencastAPI\OcRestClient` instance as its parameter. The advantage of this approach might be the methods' definitions in the IDE.

```php
$config = [
      'url' => 'https://develop.opencast.org/',       // The API url of the opencast instance (required)
      'username' => 'admin',                          // The API username. (required)
      'password' => 'opencast',                       // The API password. (required)
      'timeout' => 30000,                             // The API timeout. In miliseconds (Default 30000 miliseconds or 30 seconds). (optional)
      'version' => null                               // The API Version. (Default null). (optional)
];


use OpencastAPI\OcRestClient;
use OpencastAPI\OcEventsApi;
use OpencastAPI\OcSeriesApi;

// Get a client object.
$opencastClient = OcRestClient($config);

// To get events.
$opencastEventsApi = OcEventsApi($opencastClient);
$events = [];
$eventsResponse = $opencastEventsApi->getAll();
if ($eventsResponse['body'] == 200) {
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
      'timeout' => 30000,                             // The API timeout. In miliseconds (Default 30000 miliseconds or 30 seconds). (optional)
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

- `/api/*`: all known API endpoints of Opencast are available to be used in this library. <a href="" target="_blank">Class Definitions WiKi for API endpoints</a>
  
- `/ingest/*` : all known Ingest endpoints are available. <a href="" target="_blank">Class Definitions WiKi for Ingest Endpoints</a>

- `/services/services.json` : <a href="" target="_blank">Class Definitions WiKi for Services Endpoints</a>

- `/search/{episode | lucene | series}.json` : <a href="" target="_blank">Class Definitions WiKi for Search Endpoints</a>


# Naming convention
## Classes: 
Apart from 'OpenCast' class, all other classes under OpencastAPI namespace start with `Oc` followed by the name and the endpoint category. For example:
- OcEventApi contains 3 parts including Oc + Endpoint Name (Events) + Endpoint Category (Api)
- OcServices contains 2 parts including Oc + Endpoint Name/Category (Services)

## OpenCast class properties:
The naming convetion to access the endpoint subclasses from `OpencastAPI\OpenCast` as its properties, includes the name of the class without `Oc` in camelCase format. For example:
```php
use OpencastAPI\OpenCast;
$config = [/*the config*/];
$opencast = new OpenCast($config);

// Accessing OcEventsApi would be like: (without Oc and in camelCase format)
$ocEventsApi = $opencast->eventApi; 
```
# References
- <a href="https://develop.opencast.org/rest_docs.html" target="_blank">Main Opencast REST Service Documentation</a>
- <a href="https://docs.opencast.org/develop/developer/#api/#_top" target="_blank">Detailed Opencast REST API Endpoints Documentation</a>
