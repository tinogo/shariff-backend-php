<?php

namespace Heise\Tests\Shariff;

use GuzzleHttp\ClientInterface;
use Heise\Shariff\Backend\Facebook;

/**
 * Class FacebookTest
 */
class FacebookTest extends \PHPUnit_Framework_TestCase
{
    public function testConfig()
    {
        /** @var ClientInterface|\PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->createMock(ClientInterface::class);

        $facebook = new Facebook($client);
        $facebook->setConfig(array('app_id' => 'foo', 'secret' => 'bar'));
        $facebook->getRequest('http://www.heise.de');
    }

    public function testUsesGraphApi()
    {
        /** @var \GuzzleHttp\Client|\PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->createMock(ClientInterface::class);

        $facebook = new Facebook($client);
        $facebook->setConfig(array('app_id' => 'foo', 'secret' => 'bar'));
        $request = $facebook->getRequest('http://www.heise.de');

        $this->assertEquals('graph.facebook.com', $request->getUri()->getHost());
        $this->assertEquals('/v2.12/', $request->getUri()->getPath());
        $this->assertEquals(
            'id='.urlencode('http://www.heise.de').'&fields=engagement&access_token=foo%7Cbar',
            $request->getUri()->getQuery()
        );
    }
}
