<?php
/**
 * Glovebox: Lightweight Dependency Injection Container
 *
 * @author    Michael Squires <sqmk@php.net>
 * @copyright Copyright (c) 2012-2013 Michael K. Squires
 * @license   http://github.com/sqmk/Glovebox/wiki/License
 */

namespace Glovebox\Test;

use Glovebox\Container;

/**
 * Container Tests
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Glovebox container
     *
     * @var Container
     */
    protected $container;

    /**
     * Sets up fixture
     */
    public function setUp()
    {
        $this->container = new Container;
    }

    /**
     * Test: Service list
     *
     * @covers \Glovebox\Container::getServices
     */
    public function testServiceList()
    {
        $this->assertEmpty(
            $this->container->getServices()
        );

        $this->container->dummy = function () {
            // Do nothing
        };

        $this->assertContains(
            'dummy',
            $this->container->getServices()
        );
    }

    /**
     * Test: Throw exception on invalid service
     *
     * @covers \Glovebox\Container::__set
     *
     * @expectedException PHPUnit_Framework_Error
     */
    public function testInvalidService()
    {
        $this->container->dummy = 'Invalid service';
    }

    /**
     * Test: Setting a service
     *
     * @covers \Glovebox\Container::__set
     */
    public function testSettingService()
    {
        $this->container->dummy = function () {
            return 'success';
        };

        $this->assertEquals(
            'success',
            $this->container->dummy
        );
    }

    /**
     * Test: Throw exception on unknown service
     *
     * @covers \Glovebox\Container::__get
     *
     * @expectedException        \DomainException
     * @expectedExceptionMessage Unknown service: dummy
     */
    public function testUnknownService()
    {
        $this->container->dummy;
    }

    /**
     * Test: Throw exception on unknown service options
     *
     * @covers \Glovebox\Container::__invoke
     * @covers \Glovebox\Container::__isset
     *
     * @expectedException        \DomainException
     * @expectedExceptionMessage Unknown service: dummy
     */
    public function testUnknownServiceOptions()
    {
        $container = $this->container;

        $container('dummy')->persist = true;
    }

    /**
     * Test: Deleting service
     *
     * @covers \Glovebox\Container::__unset
     */
    public function testDeletingService()
    {
        $this->container->dummy = function() {
            // Do nothing
        };

        $this->assertTrue(
            isset($this->container->dummy)
        );

        unset($this->container->dummy);

        $this->assertFalse(
            isset($this->container->dummy)
        );
    }

    /**
     * Test: Parameter list
     *
     * @covers \Glovebox\Container::getParameters
     */
    public function testNoInitialParameters()
    {
        $this->assertEmpty(
            $this->container->getParameters()
        );

        $this->container['param'] = true;

        $this->assertContains(
            'param',
            $this->container->getParameters()
        );

    }

    /**
     * Test: Throw exception on unknown parameter
     *
     * @covers \Glovebox\Container::offsetGet
     *
     * @expectedException        \DomainException
     * @expectedExceptionMessage Unknown parameter: param
     */
    public function testUnknownParameter()
    {
        $this->container['param'];
    }

    /**
     * Test: Setting a parameter
     *
     * @covers \Glovebox\Container::offsetSet
     */
    public function testSettingParameter()
    {
        $this->container['param'] = true;

        $this->assertTrue(
            $this->container['param']
        );
    }

    /**
     * Test: Delete a parameter
     *
     * @covers \Glovebox\Container::offsetUnset
     */
    public function testDeletingParameter()
    {
        $this->container['param'] = 'dummy';

        $this->assertTrue(
            isset($this->container['param'])
        );

        unset($this->container['param']);

        $this->assertFalse(
            isset($this->container['param'])
        );
    }

    /**
     * Test: Non-persisted Service
     *
     * @covers \Glovebox\Container::offsetGet
     */
    public function testServiceActions()
    {
        $container = $this->container;

        $container['prefix']  = null;
        $container['entropy'] = true;

        // Set service
        $container->dummy = function($c) {
            return uniqid($c['prefix'], $c['entropy']);
        };

        $this->assertNotEquals(
            $container->dummy,
            $container->dummy
        );
    }

    /**
     * Test: Setting, retrieving, removing persisted services and parameters
     *
     * @covers \Glovebox\Container::__invoke
     * @covers \Glovebox\Container::__get
     * @covers \Glovebox\Container::offsetExists
     * @covers \Glovebox\Container::offsetGet
     */
    public function testPersistedServiceActions()
    {
        $container = $this->container;

        $container['prefix']  = null;
        $container['entropy'] = true;

        // Set persistent service
        $container->dummy = function($c) {
            return uniqid($c['prefix'], $c['entropy']);
        };

        $container('dummy')->persist = true;

        // Requests should be equal
        $this->assertEquals(
            $container->dummy,
            $container->dummy
        );
    }
}
