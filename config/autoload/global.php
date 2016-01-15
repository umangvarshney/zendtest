<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

//Mysql Server Connection
/*return array(
    // ...
    'db' => array(
 'driver' => 'Pdo',
//'dsn' => 'pgsql:host=localhost;port=5432;dbname=zendtest',
 'dsn' => 'mysql:dbname=zendtest;host=localhost',
 'driver_options' => array(
 PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
 ),
 ),
 'service_manager' => array(
 'factories' => array(
 'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory', 
 ),
  ),

);*/
//Postgres Server Connection
return array(
    'db' => array(
        'driver'         => 'pdo',
        'dsn'            => 'pgsql:host=localhost;port=5432;dbname=zendtest',
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
);
