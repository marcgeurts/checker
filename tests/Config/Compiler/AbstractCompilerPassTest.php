<?php

namespace ClickNow\Checker\Config\Compiler;

use Mockery as m;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * @group config/compiler
 * @covers \ClickNow\Checker\Config\Compiler\AbstractCompilerPass
 */
class AbstractCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Config\Compiler\AbstractCompilerPass|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $abstractCompilerPass;

    protected function setUp()
    {
        $this->abstractCompilerPass = $this->getMockForAbstractClass(AbstractCompilerPass::class);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(CompilerPassInterface::class, $this->abstractCompilerPass);
    }
}
