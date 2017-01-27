<?php

namespace ClickNow\Checker\Exception;

/**
 * @group  exception
 * @covers \ClickNow\Checker\Exception\CommandInvalidException
 */
class CommandInvalidExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Exception\CommandInvalidException
     */
    protected $commandInvalidException;

    public function setUp()
    {
        $this->commandInvalidException = new CommandInvalidException('command');
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(CommandException::class, $this->commandInvalidException);
    }

    public function testGetMessage()
    {
        $this->assertSame(
            'Command `command` must implement RunnerInterface.',
            $this->commandInvalidException->getMessage()
        );
    }
}
