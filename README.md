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

### Add your custom mail settings
```php
return array(

);
```