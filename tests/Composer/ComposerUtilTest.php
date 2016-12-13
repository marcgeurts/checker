<?php

namespace ClickNow\Checker\Composer;

use Composer\Package\PackageInterface;
use Exception;
use Mockery as m;

/**
 * @group composer
 * @covers \ClickNow\Checker\Composer\ComposerUtil
 */
class ComposerUtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Composer\Package\Loader\JsonLoader|\Mockery\MockInterface
     */
    protected $jsonLoader;

    protected function setUp()
    {
        $this->jsonLoader = m::mock('overload:Composer\Package\Loader\JsonLoader');
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testLoadPackage()
    {
        $this->jsonLoader->shouldReceive('load')->withAnyArgs()->once()->andReturn(m::mock(PackageInterface::class));

        $this->assertInstanceOf(PackageInterface::class, ComposerUtil::loadPackage());
    }

    public function testInvalidLoadPackage()
    {
        $this->jsonLoader->shouldReceive('load')->withAnyArgs()->once()->andThrow(Exception::class);

        $this->assertNull(ComposerUtil::loadPackage());
    }
}