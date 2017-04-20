<?php

use PHPUnit\Framework\TestCase;
use AppBundle\Util\Html;

class HtmlTest extends TestCase
{
    public function testHtml()
    {
        $x = Html::findUrls("<a href=''>xxx</div>");
        $this->assertEquals(0, count($x));

        $x = Html::findUrls('<a href=" ">xxx</div>');
        $this->assertEquals(0, count($x));

        $x = Html::findUrls('<a href="">xxx</div>');
        $this->assertEquals(0, count($x));

        $x = Html::findUrls('<div></div><span><div href="http://www.google.es">xxx</div></span>');
        $this->assertEquals(0, count($x));

        $x = Html::findUrls('<div></div><span><a href="http://www.google.es">xxx</a></span>');
        $this->assertEquals("http://www.google.es", $x[0]);

        $x = Html::findUrls('<a class="XXXX" href     ="http://www.google.es">xxx</a>');
        $this->assertEquals(1, count($x));
        $this->assertEquals("http://www.google.es", $x[0]);

        $x = Html::findUrls("<a href = \"http://www.google.es\"
        >xxx</a> <a href='http://www.google.eus'>xxx</a>");
        $this->assertEquals(2, count($x));
        $this->assertEquals("http://www.google.es", $x[0]);
        $this->assertEquals("http://www.google.eus", $x[1]);

        $x = Html::findUrls('<a href="http://www.google.es">xxx</a> <a href="http://www.google.eus">xxx</a>');
        $this->assertEquals(2, count($x));
        $this->assertEquals("http://www.google.es", $x[0]);
        $this->assertEquals("http://www.google.eus", $x[1]);

        $x = Html::findUrls('<a href = "http://www.google.es"  >xxx</a>');
        $this->assertEquals("http://www.google.es", $x[0]);

        $x = Html::findUrls('<a href = \'http://www.google.es\'  >xxx</a>');
        $this->assertEquals("http://www.google.es", $x[0]);

        $x = Html::findUrls('<a href="http://www.google.es">xxx</a>');
        $this->assertEquals(1, count($x));
        $this->assertEquals("http://www.google.es", $x[0]);

        $x = Html::findUrls('xxx123123123');
        $this->assertEquals(0, count($x));
    }
}
