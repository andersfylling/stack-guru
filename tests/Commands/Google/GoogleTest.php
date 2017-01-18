<?php

use PHPUnit\Framework\TestCase;


class GoogleTest extends TestCase 
{

    public function testUrlHelper()
    {
        //$google = new \StackGuru\Commands\Google\UrlHelper();
        $search = new StackGuru\Commands\Google\Search();

        $query = [
            "q" => "test",
            "asl" => "images"
        ];

        $expected = "q=test&asl=images"; 

        $this->assertEquals($expected, $search->buildQuery($query));
    }

    public function testSearch()
    {
        //$google = new \StackGuru\Commands\Google\UrlHelper();
        $search = new StackGuru\Commands\Google\Search();

        $query = "test images";

        $expected = "https://www.google.com/search?q=test+images"; 

        $this->assertEquals($expected, $search->process($query, null));
    }

    public function testImage()
    {
        //$google = new \StackGuru\Commands\Google\UrlHelper();
        $image = new StackGuru\Commands\Google\Image();

        $query = "test images";

        $expected = "https://www.google.com/search?q=test+images&tbm=isch"; 

        $this->assertEquals($expected, $image->process($query, null));
    }
}
