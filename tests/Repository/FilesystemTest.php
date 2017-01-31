<?php

namespace ClickNow\Checker\Repository;

use Mockery as m;
use SplFileInfo;
use SplFileObject;
use Symfony\Component\Filesystem\Filesystem as SymfonFilesystem;

/**
 * @group  repository
 * @covers \ClickNow\Checker\Repository\Filesystem
 */
class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Repository\Filesystem
     */
    protected $filesystem;

    protected function setUp()
    {
        $this->filesystem = new Filesystem();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(SymfonFilesystem::class, $this->filesystem);
    }

    public function testReadFromFileInfo()
    {
        $content = new SplFileObject('php://memory', 'r+');
        $content->fwrite('content');
        $content->rewind();

        $file = m::mock(SplFileInfo::class);
        $file->shouldReceive('openFile')->with('r')->once()->andReturn($content);

        $this->assertSame('content', $this->filesystem->readFromFileInfo($file));
    }
}
