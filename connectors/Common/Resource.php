<?php
require_once(__DIR__.'/../../helpers.php');

class Resource
{
    /** @param array $customQueryParameters*/
    private $customQueryParameters;

    /** @param array $includes*/
    private $includes;

    /** @param array $filters*/
    private $filters;

    private $connector;

    private $name;

    public function __construct(Connector $connector, $name)
    {
        $this->connector = $connector;
        $this->name = $name;
    }

    /**
     * Include related entities in the response.
     *
     * @param array|string $includes
     * @return $this
     */
    public function include($includes)
    {
        if (is_array($includes)) {
            $includes = implode(',', $includes);
        }

        $this->includes = $includes;

        return $this;
    }

    /**
     * Add filters to the request.
     *
     * @param array $filters
     * @return $this
     */
    public function filter(array $filters)
    {
        $this->filters = $filters;

        return $this;
    }

    public function addCustomQueryParameters(array $customQueryParameters)
    {
        $this->customQueryParameters = $customQueryParameters;
        return $this;
    }

    public function list($iteration = 0)
    {
        $skip = $iteration * 100;
        $pagination = "count=100&skip={$skip}";
        $baseUri = $this->setupQueryString($this->name, $pagination);

        try {
            $response = $this->connector->send(
                "GET",
                $baseUri
            );

        } catch (Throwable $th) {
            throw new Exception($th->getMessage());
        }

        return $response;
    }

    /**
     * Function that sets up filters, includes and custom query params
     *
     * @param string $resource API resource to query
     * @return string
     **/
    public function setupQueryString($resource, $pagination = '')
    {
        $queryString = $pagination;
        if (!empty($this->includes)) {
            $queryString .= "&include={$this->includes}";
        }

        if (!empty($this->filters)) {
            $filterString = '';
            foreach ($this->filters as $field => $value) {
                if (!empty($filterString)) {
                    $filterString .= '&';
                }
                $filterString .= "filter[{$field}]={$value}";
            }

            if (!empty($queryString)) {
                $queryString .= '&';
            }
            $queryString .= $filterString;
        }

        // Include the custom query parameters in the query string
        if (!empty($this->customQueryParameters)) {
            $customQueryString = http_build_query($this->customQueryParameters);
            if (!empty($queryString)) {
                $queryString .= '&';
            }
            $queryString .= $customQueryString;
        }
        // Append the query string to the URI
        $uri = $resource;
        if (!empty($queryString)) {
            $uri .= "?{$queryString}";
        }
        return $uri;
    }
}

