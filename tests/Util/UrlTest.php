<?php

use PHPUnit\Framework\TestCase;
use AppBundle\Util\Url;

class UrlTest extends TestCase
{
    public function testUrlize()
    {
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
