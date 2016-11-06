<?php


use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    public function testResponse ()
    {
        $google = new \StackGuru\Commands\Google\Search();

        $query = [
            "test",
            "something",
            "some",
            "lol",
            "idkwhy",
            "WORD"
        ];

        $base = "https://www.google.com/search?";
        $expected1 = $base . "q=test+something+some+lol+idkwhy+WORD";
        $expected2 = $base . "q=Why+am+I+such+an+asshole%3F";


        $this->assertEquals($expected1, $google->response($query));
        $this->assertEquals($expected2, $google->response());
    }
}