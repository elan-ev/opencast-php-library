# 1.1.0
 - Provide progress upon event uploads [#6](https://github.com/elan-ev/opencast-php-library/issues/6)
 - Update guzzle to 7.4.4 [#8](https://github.com/elan-ev/opencast-php-library/issues/8)
 - Handle Exceptions [#5](https://github.com/elan-ev/opencast-php-library/issues/5)
 - Perform a call without any headers or request options [#4](https://github.com/elan-ev/opencast-php-library/issues/4)
 - Error Handling (Version control) [#3](https://github.com/elan-ev/opencast-php-library/issues/3)
 - Add dynamic role headers with "X-RUN-WITH-ROLES" to requests [#2](https://github.com/elan-ev/opencast-php-library/issues/2)
 - A single filter can occur multiple times [#1](https://github.com/elan-ev/opencast-php-library/issues/1)

# 1.1.1
- Sysinfo Endpoint [#9](https://github.com/elan-ev/opencast-php-library/issues/9)
- Optional serviceType in getServiceJSON [#10](https://github.com/elan-ev/opencast-php-library/issues/10)

# 1.2.0
- typo in method name "addSinlgeAcl" in OcEventsApi [#13](https://github.com/elan-ev/opencast-php-library/issues/13)
- Dynamic timeouts per each call to the methods [#12](https://github.com/elan-ev/opencast-php-library/issues/12)
- Rename the OpenCast class to Opencast [#14](https://github.com/elan-ev/opencast-php-library/issues/14)

# 1.3.0
- Introducing the Mock handling mechanism for testing
- A new API Events Endpoint to add track to an event, which also can be used to removed/overwrite the existing tracks of a flavor.
- Depricated Methods OcWorkflowsApi->getAll() since it is has been removed from Opencast 12.
- Depricated Methods OcWorkflow->getStatistics() since it is has been removed from Opencast 12.
- Depricated Methods OcWorkflow->getInstances() since it is has been removed from Opencast 12.
- Depricated Methods OcSeries->getTitles() or (/series/allSeriesIdTitle.json Endpoint) since it is has been removed from Opencast 12.
- Depricated Methods OcSeries->getAll() or (/series/series.json|xml Endpoints) since it is has been removed from Opencast 12.
- Add the series fulltext search query into Series API in: OcSeriesApi->getAllFullTextSearch()
- The ingest API now allows setting tags when ingesting attachments or catalogs via URL, therefore OcIngest methods including addCatalog, addCatalogUrl, addAttachment and addAttachmentUrl now accept an array parameter containing the tags.
- Dynamic ingest endpoint loading into Opencast class.
- Upgrade guzzlehttp/guzzle to 7.5.1

# 1.4.0
- Introducing runAsUser method to add X-RUN-AS-USER into the request headers.
- Introducing OcListProvidersApi REST API service endpoint.
- Add another array param into response result of the library called 'origin', which contains the information about oriniated request like its path, base url, params and method.

# 1.5.0
- PHP 8.2 compatibility: prevent dynamic property declarations

# 1.6.0
- Introducing OcPlaylistApi REST API service endpoint.
- Fix ingest tags issues

# 1.7.0
- Adopt Opencast 16 changes (lucene search endpoint removal by toggle it in the config -> features -> lucene [default false])
- runWithRoles does not apply the headers when api version is not defined
- Playlist endpoint minor changes
- Add workflow configuration params into ingest method.
- Make guzzle version require from (>=) v7.5.1 in composer.

# 1.8.0
- Fix for unauthorized access when extracting the Opencast API Version.

# 1.9.0
- Allow passing additional options to Guzzle [#30]
- Added `OcUtils` class, a utility class providing additional functionality to simplify the integration and consumption of this library.
  - Initially includes the `findValueByKey` function, which is meant to retrieve a specific value from the response body. [#33]
- WorkflowApi endpoint methods got updated
  - `withconfigurationpaneljson` parameter has been added to `/api/workflow-definitions` endpoints. [#34]
  - `@deprecated` removal of OcWorkflowsApi::geAll() method!
- Repair and enhance Event API `addTrack`under `/api/events/{eventId}/track` [#36]
  - Tags are now added, therefore it can be replaced by ingest method.
  - overwriteExisting flag has been fixed and works as expected now!
- Introduce `includeInternalPublication` in Events API `getAll`, `get` and `getPublications` methods [#37]
- Deprecated methods cleanup! [#39]
  - `OcWorkflow->getStatistics()`
  - `OcWorkflow->getInstances()`
  - `OcSeries->getTitles()`
  - `OcSeries->getAll()`

# 1.9.1
- Make sure ingest service is selected only when it is online and active [#47]
