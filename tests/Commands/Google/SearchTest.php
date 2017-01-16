<?php

use PHPUnit\Framework\TestCase;


class SearchTest extends TestCase
{
    public function testResponse()
    {
        $google = new \StackGuru\Commands\Google\Search();

        $query = "some keywords";

        $base = "https://www.google.com/search?";

        $expectations = array(
            "" => $base . "q=Why+am+I+such+an+asshole%3F",
            "some keywords" => $base . "q=some+keywords",
        );

        foreach ($expectations as $keywords => $url) {
             $this->assertEquals($url, $google->process($keywords));
        }
    }
}
