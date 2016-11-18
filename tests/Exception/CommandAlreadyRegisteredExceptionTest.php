<?php

namespace ClickNow\Checker\Exception;

/**
 * @group exception
 * @covers \ClickNow\Checker\Exception\CommandAlreadyRegisteredException
 */
class CommandAlreadyRegisteredExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Exception\CommandAlreadyRegisteredException
     */
    protected $commandAlreadyRegisteredException;

    public function setUp()
    {
        $this->commandAlreadyRegisteredException = new CommandAlreadyRegisteredException('command');
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(CommandException::class, $this->commandAlreadyRegisteredException);
    }

    public function testGetMessage()
    {
        $this->assertSame(
            'Command `command` already registered.',
            $this->commandAlreadyRegisteredException->getMessage()
        );
    }
}
