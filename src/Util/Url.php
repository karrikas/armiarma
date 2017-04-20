<?php

namespace AppBundle\Util;

class Url
{
    /**
     * Get the aboslute url path with domain name.
     * @param string $base path to find
     * @param string $path current page
     *
     * @return string absolute url
     */
    public static function urlize($base, $path)
    {
        // remove anchors
        $base = preg_replace('/#.*$/', '', $base);
        $path = preg_replace('/#.*$/', '', $path);
        $base = html_entity_decode($base);
        $path = html_entity_decode($path);

        // allowed protocols http, https
        $protocols = ['http', 'https'];
        if (preg_match('@([^:]+)://@', $base, $res)) {
            if (!in_array($res[1], $protocols)) {
                return '';
            }
        }

        // without protocol add http
        if (preg_match('@^//@', $base, $res)) {
            $base = 'http:'.$base;
        }

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

    /**
     * get domain name with protocol.
     * @param string $url
     *
     * @return string|false
     */
    public static function getDomain($url)
    {
        if (preg_match('/http(s)*\:\/\/[^\/]+/', $url, $res)) {
            $url = $res[0];
        } else {
            $url = false;
        }

        return strtolower($url);
    }
}
