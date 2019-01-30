<?php

namespace Kunstmaan\AdminListBundle\Tests\Helper;

use Kunstmaan\AdminBundle\Helper\DomainConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-03-19 at 09:56:53.
 */
class DomainConfigurationTest extends TestCase
{
    /**
     * @var DomainConfiguration
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        $map = array(
            array('multilanguage', true),
            array('defaultlocale', 'nl'),
            array('requiredlocales', 'nl|fr|en'),
        );

        $this->object = new DomainConfiguration($this->getContainer($map));
    }

    public function testGetSet()
    {
        $data = $this->object->getLocalesExtraData();
        $this->assertCount(0, $data);
        $data = $this->object->getFullHostConfig();
        $this->assertCount(0, $data);
        $this->assertNull($this->object->getFullHost());
        $this->assertNull($this->object->getFullHostById(123));
        $this->assertNull($this->object->getHostSwitched());
        $this->assertNull($this->object->getHostBaseUrl());
    }

    private function getContainer($map)
    {
        $this->container = $this->createMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->container
            ->method('getParameter')
            ->will($this->returnValueMap($map));
        $this->container
            ->expects($this->any())
            ->method('get')
            ->with($this->equalTo('request_stack'))
            ->willReturn($this->getRequestStack());

        return $this->container;
    }

    private function getRequestStack()
    {
        $requestStack = $this->createMock('Symfony\Component\HttpFoundation\RequestStack');
        $requestStack->expects($this->any())->method('getMasterRequest')->willReturn($this->getRequest());

        return $requestStack;
    }

    private function getRequest()
    {
        $request = Request::create('http://domain.tld/');

        return $request;
    }
}
