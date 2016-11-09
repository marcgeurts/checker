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
    protected function tearDown()
    {
        m::close();
    }

    public function testFindExecutable()
    {
        $symfonyExecutableFinder = m::mock(SymfonyExecutableFinder::class);
        $symfonyExecutableFinder->shouldReceive('find')->with('foo', null, ['bin'])->twice()->andReturn('bin/foo');

        $executableFinder = new ExecutableFinder('bin', $symfonyExecutableFinder);

        $this->assertSame('bin/foo', $executableFinder->find('foo'));
        $this->assertSame('bin/foo', $executableFinder->find('foo', true));
    }

    public function testExecutableNotFound()
    {
        $this->setExpectedException(ExecutableNotFoundException::class, 'Executable `foo` was not found.');

        $symfonyExecutableFinder = m::mock(SymfonyExecutableFinder::class);
        $symfonyExecutableFinder->shouldReceive('find')->with('foo', null, ['bin'])->once()->andReturn(false);

        $executableFinder = new ExecutableFinder('bin', $symfonyExecutableFinder);
        $this->assertSame('bin/foo', $executableFinder->find('foo'));
    }
}
