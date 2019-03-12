<?php

namespace App\Ddd\Application;

use League\Fractal\Manager;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Serializer\JsonApiSerializer;

class FractalService extends Manager
{
    /**
     * @var string
     */
    private $apiUrl;

    public function __construct($apiUrl='')
    {
        $this->apiUrl = $apiUrl;
        parent::__construct();
    }

    /**
     * @param $resource
     * @param string $prefix
     * @return array
     */
    public function transform($resource, $prefix = ''): array
    {
        if ($resource instanceof ResourceInterface){
            $this->setSerializer(new JsonApiSerializer($this->apiUrl . $prefix));
            $resource = $this->createData($resource);
            $response = $resource->toArray();
        }else{
            $response = [ 'message' => $resource ];
        }

        return $response;

    }
}
