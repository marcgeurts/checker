<?php

namespace ClickNow\Checker\Exception;

/**
 * @group  exception
 * @covers \ClickNow\Checker\Exception\CommandException
 */
class CommandExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Exception\CommandException
     */
    protected $commandException;

    public function setUp()
    {
        $this->commandException = new CommandException('command');
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(RuntimeException::class, $this->commandException);
    }

    public function testGetMessage()
    {
        $this->assertEmpty($this->commandException->getMessage());
    }

    public function testGetCommandName()
    {
        $this->assertSame('command', $this->commandException->getCommandName());
    }
}
