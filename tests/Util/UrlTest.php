<?php

use PHPUnit\Framework\TestCase;
use AppBundle\Util\Url;

class UrlTest extends TestCase
{
    public function testUrlize()
    {
        $a = 'android-app://org.wikipedia/http/eu.m.wikipedia.org/wiki/HTML';
        $b = 'http://org.wikipedia';
        $x = Url::urlize($a, $b);
        $this->assertEquals('', $x);

        $a = '/w/index.php?title=HTML&amp;action=edit';
        $b = 'http://org.wikipedia/w/';
        $x = Url::urlize($a, $b);
        $this->assertEquals('http://org.wikipedia/w/index.php?title=HTML&action=edit', $x);


        $a = '/static/apple-touch/wikipedia.png';
        $b = 'http://org.wikipedia/w/';
        $x = Url::urlize($a, $b);
        $this->assertEquals('http://org.wikipedia/static/apple-touch/wikipedia.png', $x);

        $a = '//eu.wikipedia.org/w/api.php?action=rsd';
        $b = 'http://org.wikipedia/w/';
        $x = Url::urlize($a, $b);
        $this->assertEquals('http://eu.wikipedia.org/w/api.php?action=rsd', $x);

        $a = 'https://eu.wikipedia.org/wiki/HTML';
        $b = 'http://org.wikipedia/w/';
        $x = Url::urlize($a, $b);
        $this->assertEquals('https://eu.wikipedia.org/wiki/HTML', $x);

        $x = Url::urlize('http://www.woothemes.com', 'http://lupulu.com');
        $this->assertEquals('http://www.woothemes.com', $x);
        
        $x = Url::urlize('a', 'b');
        $this->assertEquals('', $x);
        
        $x = Url::urlize('/', 'http://web/');
        $this->assertEquals('http://web/', $x);
        
        $x = Url::urlize('/bba', 'http://web');
        $this->assertEquals('http://web/bba', $x);

        $x = Url::urlize('/bba', 'http://web/uuu/iii');
        $this->assertEquals('http://web/bba', $x);

        $x = Url::urlize('bba', 'http://web/uuu/iii');
        $this->assertEquals('http://web/uuu/iii/bba', $x);

        $x = Url::urlize('bba', 'http://web/uuu/iii/');
        $this->assertEquals('http://web/uuu/iii/bba', $x);

        $x = Url::urlize('#bba', 'http://web/uuu/iii/');
        $this->assertEquals('http://web/uuu/iii/', $x);

        $x = Url::urlize('tel:123123123', 'http://web/uuu/iii/');
        $this->assertEquals('', $x);

        $x = Url::urlize('xxx:123123123', 'http://web/uuu/iii/');
        $this->assertEquals('', $x);
    }

    public function testgetDomain()
    {
        $x = Url::getDomain('http://PRUeba.com');
        $this->assertEquals('http://prueba.com', $x);

        $x = Url::getDomain('http://prueba.com');
        $this->assertEquals('http://prueba.com', $x);

        $x = Url::getDomain('http://prueba.com/');
        $this->assertEquals('http://prueba.com', $x);

        $x = Url::getDomain('http://prueba.com/bat/bi');
        $this->assertEquals('http://prueba.com', $x);

        $x = Url::getDomain('http://www.prueba.com');
        $this->assertEquals('http://www.prueba.com', $x);

        $x = Url::getDomain('xxx123123123');
        $this->assertTrue(!$x);
    }
}
