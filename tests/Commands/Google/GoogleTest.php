<?php

use PHPUnit\Framework\TestCase;


class GoogleTest extends TestCase
{
    public function testQueryBuilder()
    {
        $google = new \StackGuru\Commands\Google\Google();

        $query = [
            "q" => "test",
            "asl" => "images"
        ];

        $expected = "q=test&asl=images";

        $this->assertEquals($expected, $google->queryBuilder($query));
    }
}
