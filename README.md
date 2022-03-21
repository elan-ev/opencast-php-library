# opencast-php-library
This php composer package is meant to provide an unified easy-to-use Opencast RESTful API library. It has been designed to make most of commonly used REST Endpoints available to the developers of thirt-party applications including LMSes such as Stud.IP, Moodle and ILIAS.

# Requisitions
<b>PHP Version 7.2.5 or above</b> as well as <b>cURL</b> is required by this library.

# Installation
`composer require elan/opencast-api`

# Basic Usage
There 2 approaches to use the Endpoints from this library:

1. The first one is via the Generic `OpencastAPI\OpenCast` which contains all available opencast endpoints. The advantage of using this approach would be a better control over all endpoints. (Recommended)

NOTE: This approach also supports dual node Opencast configuration, you can pass the secondary node config (a.k.a "engage" mostly use for search endpoint) to have it accessible throughout the `OpencastAPI\OpenCast` class. 
.It depends on the way your Opencast Instance is configured
```php

```

2. The second approach is to instatiate each endpoint when needed, but the down side of this would be the needs a `OpencastAPI\OcRestClient` when creating it. The advantage of this approach might be the methods' definitions in the IDE.

```php

```

# Available Nodes

- `/api/*`: all known API endpoints of Opencast are available to be used in this library. <a href="" target="_blank">Class Definitions WiKi for API endpoints</a>
  
- `/ingest/*` : all known Ingest endpoints are available. <a href="" target="_blank">Class Definitions WiKi for Ingest Endpoints</a>

- `/services/services.json` : <a href="" target="_blank">Class Definitions WiKi for Services Endpoints</a>

- `/search/{episode | lucene | series}.json` : <a href="" target="_blank">Class Definitions WiKi for Search Endpoints</a>
