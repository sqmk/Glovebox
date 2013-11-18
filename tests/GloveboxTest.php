<?php
/**
 * Glovebox: Lightweight Dependency Injection Container
 *
 * @author    Michael Squires <sqmk@php.net>
 * @copyright Copyright (c) 2012-2013 Michael K. Squires
 * @license   http://github.com/sqmk/Glovebox/wiki/License
 */

/**
 * Glovebox Tests
 */
class GloveboxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Glovebox container
     *
     * @var \Glovebox
     */
    protected $container;

    /**
     * Sets up fixture
     */
    public function setUp()
    {
        $this->container = new \Glovebox();
    }

    /**
     * Test: Service list
     *
     * @covers \Glovebox::getServices
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
     * @covers \Glovebox::__set
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
     * @covers \Glovebox::__set
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
     * @covers \Glovebox::__get
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
     * @covers \Glovebox::__invoke
     * @covers \Glovebox::__isset
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
     * @covers \Glovebox::__unset
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
     * @covers \Glovebox::getParameters
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
     * @covers \Glovebox::offsetGet
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
     * @covers \Glovebox::offsetSet
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
     * @covers \Glovebox::offsetUnset
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
     * @covers \Glovebox::offsetGet
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
     * @covers \Glovebox::__invoke
     * @covers \Glovebox::__get
     * @covers \Glovebox::offsetExists
     * @covers \Glovebox::offsetGet
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
