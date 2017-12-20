<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\TableGateway\DirectusActivityTableGateway;
use Directus\Util\ArrayUtils;

class Activities extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->get('', [$this, 'all']);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function all(Request $request, Response $response)
    {
        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');
        $params = $request->getQueryParams();

        $activityTableGateway = new DirectusActivityTableGateway($dbConnection, $acl);

        // a way to get records last updated from activity
        if (ArrayUtils::get($params, 'last_updated')) {
            $table = key($params['last_updated']);
            $ids = ArrayUtils::get($params, 'last_updated.' . $table);
            $arrayOfIds = $ids ? explode(',', $ids) : [];
            $responseData = $activityTableGateway->getLastUpdated($table, $arrayOfIds);
        } else {
            $responseData = $activityTableGateway->fetchFeed($params);
        }

        return $this->responseWithData($request, $response, $responseData);
    }
}
