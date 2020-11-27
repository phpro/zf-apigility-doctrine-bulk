> ## Repository abandoned 2020-11-27
>
> This repository has been archived since we are not using it anymore internally.
> Feel free to use it AS-IS, we won't be providing any support anymore.

# Apigility Doctrine Bulk Module
This module provides an extendable and fast way of adding bulk actions to the doctrine apigility module.
It is based on the bulk API of [elasticsearch](http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/docs-bulk.html).

## Installation
```
curl -s https://getcomposer.org/installer | php
php composer.phar install
```

## Module Installation

### Add to composer.json
```
"phpro/zf-apigility-doctrine-bulk": "dev-master"
```

### Add module to application.config.php
```php
return array(
    'modules' => array(
        'Phpro\Apigility\Doctrine\Bulk',
        // other libs...
    ),
    // Other config
);
```

### Add your custom bulk endpoints to the configuration
```php
return [
    // A normal RPC route:
    'router' => [
        'routes' => [
            'api.rpc.bulk' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/bulk',
                    'defaults' => array(
                        'controller' => 'Api\V1\Rpc\Bulk\BulkController',
                        'action' => 'bulk',
                    ],
                ],
            ],
        ]
    ],

    /*
     * A new bulk controller:
     * - entity_class: the classname of the doctrine entity
     * - object_manager: the key of the desired object manager in the service manager
     * - hydrator: the key of the desired hydrator in the hydrator manager
     * - listeners: custom bulk action listeners that are being loaded from the service manager
     */
    'zf-apigility' => [
        'doctrine-bulk-handlers' => [
            'Api\V1\Rpc\Bulk\BulkController' => [
                'entity_class' => 'Application\Entity',
                'object_manager' => 'doctrine.object-manager.default',
                'hydrator' => 'Application\Hydrator\Entity',
                'listeners' => [
                    // Custom ListenerAggregates
                ],
            ],
        ],
    ],

    // Flag the new controller as a RPC controller:
    'zf-rpc' => [
        'Api\V1\Rpc\Bulk\BulkController' => array(
            'http_methods' => array(
                0 => 'POST',
            ),
            'route_name' => 'api.rpc.bulk',
            'service_name' => 'Bulk',
        ),
    ],

    // Enable versioning
    'zf-versioning' => [
        'uri' => [
            0 => 'api.rpc.bulk',
        ],
    ],

    // Add JSON to content negotiation
    'zf-content-negotiation' => [
        'controllers' => [
            'Api\V1\Rpc\Bulk\BulkController' => 'Json',
        ],
        'accept_whitelist' => [
            'Api\V1\Rpc\Bulk\BulkController' => array(
                0 => 'application/json',
                1 => 'application/*+json',
            ),
        ],
        'content_type_whitelist' => [
            'Api\V1\Rpc\Bulk\BulkController' => [
                0 => 'application/json',
            ],
        ],
    ],
];
```

### Create a RPC Controller class
```php
namespace Api\V1\Rpc\Bulk;
use Phpro\Apigility\Doctrine\Bulk\Controller\BulkController as BaseController;

class BulkController extends BaseController
{

}
```

# Run a bulk call
```
POST /api/bulk
```

## Request body:
```javascript
[
    {'create': {'name': 'VeeWee'}},
    {'update': {'id': 1, 'name': 'Updated'}},
    {'delete': {'id': 2}},
    {'changeEmail': {'id': 1, 'email': 'new@email.com'}},
]
```

## Response body:
```javascript
[
    {'command': 'create', 'id': 100, 'params': [], 'isSuccess': true, 'isError': false, 'error': ''},
    {'command': 'update', 'id': 1, 'params': [], 'isSuccess': true, 'isError': false, 'error': ''},
    {'command': 'delete', 'id': 2, 'params': [], 'isSuccess': true, 'isError': false, 'error': ''},
    {'command': 'changeEmail', 'id': 1, 'params': {'old-email': 'old@email.com'}, 'isSuccess': true, 'isError': false, 'error': ''},
]
```

# Custom Commands
It is very easy to add a custom action like `changeEmail` to the bulk service.
This method will call the changeEmail($email) method on the entity with ID 1 and save the entity to the database.
When the result of this method is an array, then these results are added as key-value pairs to the response.

# Custom listeners
It is possible to add your own custom listeners for specific tasks.
You should create a class that implements ListenerAggregateInterface.
This listener should listen to a BulkEvent with a specified command name.
When you add this listener to the service manager, it is posible to add the key to the `listeners` property of the `doctrine-bulk-handlers` part in the configuration.

# TODO
- Interaction with admin
