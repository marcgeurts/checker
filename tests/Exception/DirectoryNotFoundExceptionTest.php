<?php

namespace ClickNow\Checker\Exception;

/**
 * @group exception
 * @covers \ClickNow\Checker\Exception\DirectoryNotFoundException
 */
class DirectoryNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Exception\DirectoryNotFoundException
     */
    protected $directoryNotFoundException;

    public function setUp()
    {
        $this->directoryNotFoundException = new DirectoryNotFoundException('directory');
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(RuntimeException::class, $this->directoryNotFoundException);
    }

    public function testGetMessage()
    {
        $this->assertSame(
            'Directory `directory` was not found.',
            $this->directoryNotFoundException->getMessage()
        );
    }

    public function testGetDirectory()
    {
        $this->assertSame('directory', $this->directoryNotFoundException->getDirectory());
    }
}
