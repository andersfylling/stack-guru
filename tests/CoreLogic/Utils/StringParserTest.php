<?php


use PHPUnit\Framework\TestCase;

use \StackGuru\CoreLogic\Utils;


class StringParserTest extends TestCase
{
    /**
     * @dataProvider getFirstWordsDataProvider
     */
    public function testGetFirstWords($sentence, $words, $expected)
    {
        $result = Utils\StringParser::getFirstWords($sentence, $words);
        $this->assertEquals($expected, $result);
    }

    // Provider for test cases for testGetFirstWords
    public function getFirstWordsDataProvider()
    {
        // List of test cases with arguments and expected result
        // [
        //   [$sentence, $words, $expected],
        //   ...
        // ]
        return [
            [ "a",      1,  ["a"] ],     // most simple case
            [ "a b c",  2,  ["a", "b"] ],   // regular extraction
            [ "a b c",  5,  ["a", "b", "c"] ], // more words than present in sentence
            [ "a b c",  0,  [] ],      // no words
        ];
    }
}
