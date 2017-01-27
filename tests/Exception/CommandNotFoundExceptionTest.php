<?php

namespace ClickNow\Checker\Exception;

/**
 * @group  exception
 * @covers \ClickNow\Checker\Exception\CommandNotFoundException
 */
class CommandNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Exception\CommandNotFoundException
     */
    protected $commandNotFoundException;

    public function setUp()
    {
        $this->commandNotFoundException = new CommandNotFoundException('command');
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(CommandException::class, $this->commandNotFoundException);
    }

    public function testGetMessage()
    {
        $this->assertSame(
            'Command `command` was not found.',
            $this->commandNotFoundException->getMessage()
        );
    }
}
