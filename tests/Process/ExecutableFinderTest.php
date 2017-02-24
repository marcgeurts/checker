<?php

namespace ClickNow\Checker\Process;

use ClickNow\Checker\Exception\ExecutableNotFoundException;
use Mockery as m;
use Symfony\Component\Process\ExecutableFinder as SymfonyExecutableFinder;

/**
 * @group  process
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
        $this->executableFinder = new ExecutableFinder('foo', $this->finder);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testFindExecutable()
    {
        $this->finder->shouldReceive('find')->with('bar', null, m::contains('foo'))->twice()->andReturn('foo/bar');

        $this->assertSame('foo/bar', $this->executableFinder->find('bar'));
        $this->assertSame('foo/bar', $this->executableFinder->find('bar', true));
    }

    public function testExecutableNotFound()
    {
        $this->setExpectedException(ExecutableNotFoundException::class, 'Executable `bar` was not found.');

        $this->finder->shouldReceive('find')->with('bar', null, m::contains('foo'))->once()->andReturn(false);
        $this->assertSame('foo/bar', $this->executableFinder->find('bar'));
    }
}
