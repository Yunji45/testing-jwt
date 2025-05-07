<?php

return [
    'broker' => env('KAFKA_BROKER', 'localhost:9092'),
    'user' => env('KAFKA_USERNAME', ''),
    'password' => env('KAFKA_PASSWORD', ''),

    'group' => env('KAFKA_GROUP', 'default_group'),
    'instance' => env('KAFKA_INSTANCE', 'default_instance'),
    'topic' => env('KAFKA_TOPIC', 'logins'),
    'zookeeper_port' => env('ZOOKEEPER_PORT', 2181),
];
