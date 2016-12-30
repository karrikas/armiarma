<?php

namespace AppBundle\Util;

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;


class DomainFinder {

    function find($url) {
        $client = new Client();
        $guzzleClient = new GuzzleClient(array(
            'timeout' => 60,
        ));
        $client->setClient($guzzleClient);


        file_put_contents(ROOTDIR.'/var/cache/'.md5($url), file_get_contents($url));
        $crawler = $client->request('GET', $url);

        return $crawler->filter('a')->each(function ($node) {
            return $node->attr("href");
        });
    }
}
