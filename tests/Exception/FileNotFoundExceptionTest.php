<?php

namespace ClickNow\Checker\Exception;

/**
 * @group  exception
 * @covers \ClickNow\Checker\Exception\FileNotFoundException
 */
class FileNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Exception\FileNotFoundException
     */
    protected $fileNotFoundException;

    public function setUp()
    {
        $this->fileNotFoundException = new FileNotFoundException('file');
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(RuntimeException::class, $this->fileNotFoundException);
    }

    public function testGetMessage()
    {
        $this->assertSame(
            'File `file` was not found.',
            $this->fileNotFoundException->getMessage()
        );
    }

    public function testGetPath()
    {
        $this->assertSame('file', $this->fileNotFoundException->getPath());
    }
}
