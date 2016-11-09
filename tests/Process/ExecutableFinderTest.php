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
        $executableFinder = m::mock(SymfonyExecutableFinder::class);
        $executableFinder->shouldReceive('find')->with('foo', null, ['bin'])->twice()->andReturn('bin/foo');

        $finder = new ExecutableFinder('bin', $executableFinder);
        $this->assertEquals('bin/foo', $finder->find('foo'));

        // force unix
        $this->assertEquals('bin/foo', $finder->find('foo', true));
    }

    public function testExecutableNotFound()
    {
        $this->setExpectedException(ExecutableNotFoundException::class, 'Executable `foo` was not found.');

        $executableFinder = m::mock(SymfonyExecutableFinder::class);
        $executableFinder->shouldReceive('find')->with('foo', null, ['bin'])->once()->andReturn(false);

        $finder = new ExecutableFinder('bin', $executableFinder);
        $this->assertEquals('bin/foo', $finder->find('foo'));
    }
}
