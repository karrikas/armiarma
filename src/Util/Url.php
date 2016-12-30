<?php

namespace AppBundle\Util;

class Url {
    public static function urlize($base, $path)
    {
        // remove anchors
        $base = preg_replace('/#.*$/', '', $base);
        $path = preg_replace('/#.*$/', '', $path);

        // find de url root "http://domain.com"/xxx/bbb
        if (preg_match('/(^[^\:]+\:\/\/[^\/]+)/', $path, $res)) {
            $pathBase = $res[1];
        }

        // if is a new url
        if (preg_match('/^http(s)*\:\/\//', $base)) {

            return $base;
        }

        // if it is not a url 
        if (!preg_match('/^http(s)*\:\/\//', $path)) {

            return '';
        }

        // ignore mailto: tel: fax: 
        if (preg_match('/^[a-z]+\:/i', $base)) {

            return '';
        }

        if (preg_match('/^\//', $base)) { 

            return $pathBase.$base;
        } else {
            if (!preg_match('/\/$/', $path)) {
                $path .= '/';
            }

            return $path.$base;
        }

        return '';
    }

    public static function getDomain($url)
    {
        if (preg_match('/http(s)*\:\/\/[^\/]+/', $url, $res)) {
            $url = $res[0];
        } else {
            $url = false;
        }

        return $url;
    }
}