<?php
/**
 * Glovebox: Lightweight Dependency Injection Container
 *
 * @author    Michael Squires <sqmk@php.net>
 * @copyright Copyright (c) 2012 Michael K. Squires
 * @license   http://github.com/sqmk/LICENSE.txt
 * @package   Glovebox
 */

/**
 * Glovebox Tests
 *
 * @package    Glovebox
 * @subpackage UnitTests
 * @group      Glovebox
 */
class GloveboxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Glovebox container
     *
     * @var \Glovebox Container
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
    public function testNoInitialServices()
    {
        $this->assertEmpty(
            $this->container->getServices()
        );
    }

    /**
     * Test: Throw exception on invalid exception
     *
     * @covers \Glovebox::__set
     * @covers \Glovebox::__isset
     *
     * @expectedException PHPUnit_Framework_Error
     */
    public function testInvalidService()
    {
        $this->container->dummy = 'Invalid service';
    }

    /**
     * Test: Throw exception on missing service
     *
     * @covers \Glovebox::__set
     * @covers \Glovebox::__isset
     *
     * @expectedException \DomainException
     */
    public function testMissingService()
    {
        $this->container->dummy;
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
    }

    /**
     * Test: Throw exception on missing parameter
     *
     * @covers \Glovebox::offsetExists
     * @covers \Glovebox::offsetSet
     *
     * @expectedException \DomainException
     */
    public function testMissingParameter()
    {
        $this->container['param'];
    }

    /**
     * Test: Setting, retrieving, removing services and parameters
     *
     * @covers \Glovebox
     */
    public function testServiceActions()
    {
        $container = $this->container;

        // Set common parameters
        $container['prefix']  = null;
        $container['entropy'] = true;

        // Handle non persistent service
        $container->dummy = function($container) {
            return uniqid(
                $container['prefix'],
                $container['entropy']
            );
        };

        $this->assertNotEquals(
            $container->dummy,
            $container->dummy
        );

        // Handle persistent service
        $container->dummy2 = function($container) {
            return uniqid(
                $container['prefix'],
                $container['entropy']
            );
        };

        $container('dummy2')->persist = true;

        $this->assertEquals(
            $container->dummy2,
            $container->dummy2
        );
    }
}
