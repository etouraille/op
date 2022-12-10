<?php

namespace App\Service;

use App\Entity\Thing;
use Elasticsearch\ClientBuilder;

class ElasticService
{

    public static function getClient() {

        $hosts = [
            'https://elastic:b1otope@es01:9200',
        ];

        $certs = '/certs/ca/ca.crt';

        return ClientBuilder::create()->setHosts($hosts)->setSSLVerification($certs)->build();
    }

    public static function getObject(Thing $thing) {
        return /** @var $thing Thing */
            $object = [
                'id' => $thing->getId(),
                'name' => strtolower($thing->getName()),
                'picture' => count($thing->getPictures()) === 0 ? '': $thing->getPictures()->toArray()[0]->getPicture(),
                'type' => $thing->getType() ? strtolower($thing->getType()->getName()): 'undefined',
            ];
    }

    public static function upsert(Thing $thing) {

        $client = self::getClient();
        $params = [
            'index' => 'thing',
            'id' => $thing->getId(),
        ];
        if($client->get($params)) {
            $client->update([
                'index' => 'thing',
                'id' => $thing->getId(),
                'body' => [
                    'doc' => self::getObject($thing)
                ]
            ]);
        } else {
            $client->index([
                'index' => 'thing',
                'id' => $thing->getId(),
                'body' => self::getObject($thing)
            ]);
        }
    }

    public static function search( $string ) {
        $string = strtolower($string);
        $params = [
            'index' => 'thing',
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => [
                            ['wildcard' => ['name' => '*'.$string. '*']],
                            ['wildcard' => ['type' => '*'.$string. '*']]
                        ]
                    ]
                ]
            ]
        ];
        $results = self::getClient()->search($params);
        return array_map(function($elem) {
            return $elem['_source'];
        }, $results['hits']['hits']);
    }
}