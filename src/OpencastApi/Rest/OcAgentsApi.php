<?php
namespace OpencastApi\Rest;

class OcAgentsApi extends OcRest
{
    const URI = '/api/agents';

    public function __construct($restClient)
    {
        parent::__construct($restClient);
        // The Agents API is available since API version 1.1.0.
        if (!$this->restClient->hasVersion('1.1.0')) {
            throw new Exception('The Agents API is available since API version 1.1.0.');
        }
    }

    /**
     * Returns a list of capture agents.
     * 
     * @param int $limit The maximum number of results to return for a single request
     * @param int $offset The index of the first result to return
     * 
     * @return array the response result
     * @return array the response result
     */
    public function getAll($limit = 0, $offset = 0)
    {
        $query = [];
        if (!empty($limit)) {
            $query['limit'] = intval($limit);
        }
        if (!empty($offset)) {
            $query['offset'] = intval($offset);
        }
        $options = $this->restClient->getQueryParams($query);
        return $this->restClient->performGet(self::URI, $options);
    }

    /**
     * Returns a single capture agent.
     * 
     * @param string $agentId The agent id
     * 
     * @return array the response result
     */
    public function get($agentId)
    {
        $uri = self::URI . "/$agentId";
        return $this->restClient->performGet($uri);
    }
}
?>