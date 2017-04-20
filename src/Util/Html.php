<?php

namespace AppBundle\Util;

class Html
{
    /**
     * Find urls
     * @param string $html
     *
     * @return array
     */
    public function findUrls($html)
    {
        $html = preg_replace("/href[ ]*=[ ]*''/", '', $html);
        $html = preg_replace('/href[ ]*=[ ]*""/', '', $html);

        if (!preg_match_all('/<a[^>]*href[ ]*=[ ]*[\'"]*([^\'"]*)/si', $html, $res)) {
            return [];
        }

        $return = [];
        foreach ($res[1] as $key => $value) {
            if (empty(trim($value))) {
                continue;
            }

            $return[] = $value;
        }

        return $return;
    }
}
