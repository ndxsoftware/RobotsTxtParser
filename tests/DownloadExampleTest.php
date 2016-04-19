<?php
namespace vipnytt\RobotsTxtParser\Tests;

use vipnytt\RobotsTxtParser\Parser;

/**
 * Class DownloadExampleTest
 *
 * @package vipnytt\RobotsTxtParser\Tests
 */
class DownloadExampleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $base
     */
    public function testDownloadExample($base)
    {
        $parser = new Parser($base);
        $this->assertInstanceOf('vipnytt\RobotsTxtParser\Parser', $parser);

        $this->assertTrue($parser->userAgent()->isAllowed("/"));
        $this->assertFalse($parser->userAgent()->isDisallowed("/"));

        $this->assertTrue($parser->userAgent('*')->isAllowed("/"));
        $this->assertFalse($parser->userAgent('*')->isDisallowed("/"));

        $this->assertEquals([], $parser->getSitemaps());

        $this->assertNull($parser->getHost());

        $this->assertNull($parser->getCleanParam());
    }

    /**
     * Generate test data
     *
     * @return array
     */
    public function generateDataForTest()
    {
        return [
            [
                'http://example.com'
            ],
            [
                'http://www.example.com'
            ],
            [
                'https://example.com'
            ],
            [
                'https://www.example.com'
            ]
        ];
    }
}
