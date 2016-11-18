<?php

namespace ClickNow\Checker\Exception;

/**
 * @group exception
 * @covers \ClickNow\Checker\Exception\ExecutableNotFoundException
 */
class ExecutableNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Exception\ExecutableNotFoundException
     */
    protected $executableNotFoundException;

    public function setUp()
    {
        $this->executableNotFoundException = new ExecutableNotFoundException('executable');
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(RuntimeException::class, $this->executableNotFoundException);
    }

    public function testGetMessage()
    {
        $this->assertSame(
            'Executable `executable` was not found.',
            $this->executableNotFoundException->getMessage()
        );
    }

    public function testGetExecutable()
    {
        $this->assertSame('executable', $this->executableNotFoundException->getExecutable());
    }
}
