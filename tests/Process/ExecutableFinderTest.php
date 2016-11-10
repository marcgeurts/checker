<?php

namespace ClickNow\Checker\Process;

use ClickNow\Checker\Exception\ExecutableNotFoundException;
use Mockery as m;
use Symfony\Component\Process\ExecutableFinder as SymfonyExecutableFinder;

/**
 * @group process
 * @covers \ClickNow\Checker\Process\ExecutableFinder
 */
class ExecutableFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Process\ExecutableFinder|\Mockery\MockInterface
     */
    protected $finder;

    /**
     * @var \ClickNow\Checker\Process\ExecutableFinder
     */
    protected $executableFinder;

    protected function setUp()
    {
        $this->finder = m::mock(SymfonyExecutableFinder::class);
        $this->executableFinder = new ExecutableFinder('bin', $this->finder);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testFindExecutable()
    {
        $this->finder->shouldReceive('find')->with('foo', null, ['bin'])->twice()->andReturn('bin/foo');

        $this->assertSame('bin/foo', $this->executableFinder->find('foo'));
        $this->assertSame('bin/foo', $this->executableFinder->find('foo', true));
    }

    public function testExecutableNotFound()
    {
        $this->setExpectedException(ExecutableNotFoundException::class, 'Executable `foo` was not found.');

        $this->finder->shouldReceive('find')->with('foo', null, ['bin'])->once()->andReturn(false);

        $this->assertSame('bin/foo', $this->executableFinder->find('foo'));
    }
}
