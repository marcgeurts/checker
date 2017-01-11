<?php

namespace ClickNow\Checker\Repository;

use Gitonomy\Git\Diff\Diff;
use Gitonomy\Git\Diff\File;
use Gitonomy\Git\Repository;
use Mockery as m;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @group repository
 * @covers \ClickNow\Checker\Repository\Git
 */
class GitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Gitonomy\Git\Repository|\Mockery\MockInterface
     */
    protected $repository;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem|\Mockery\MockInterface
     */
    protected $filesystem;

    /**
     * @var \ClickNow\Checker\Repository\Git
     */
    protected $git;

    protected function setUp()
    {
        $this->repository = m::mock(Repository::class);
        $this->filesystem = m::mock(Filesystem::class);
        $this->git = new Git($this->repository, $this->filesystem);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testHooks()
    {
        $this->assertClassHasStaticAttribute('hooks', Git::class);
        $this->assertInternalType('array', Git::$hooks);
    }

    public function testGetRegisteredFiles()
    {
        $this->repository->shouldReceive('run')->with('ls-files')->once()->andReturn('file1.txt'.PHP_EOL.'file2.txt');
        $result = $this->git->getRegisteredFiles();

        $this->assertInstanceOf(FilesCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(SplFileInfo::class, $result[0]);
        $this->assertSame('file1.txt', $result[0]->getPathname());
        $this->assertInstanceOf(SplFileInfo::class, $result[1]);
    }

    public function testGetChangedFiles()
    {
        $file1 = m::mock(File::class);
        $file2 = m::mock(File::class);
        $file3 = m::mock(File::class);

        $file1->shouldReceive('isRename')->withNoArgs()->once()->andReturn(false);
        $file2->shouldReceive('isRename')->withNoArgs()->once()->andReturn(true);
        $file3->shouldReceive('isRename')->withNoArgs()->once()->andReturn(false);

        $file1->shouldReceive('getNewName')->withNoArgs()->never();
        $file2->shouldReceive('getNewName')->withNoArgs()->once()->andReturn('file2.txt');
        $file3->shouldReceive('getNewName')->withNoArgs()->never();

        $file1->shouldReceive('getName')->withNoArgs()->once()->andReturn('file1.txt');
        $file2->shouldReceive('getName')->withNoArgs()->never();
        $file3->shouldReceive('getName')->withNoArgs()->once()->andReturn('file3.txt');

        $file1->shouldReceive('isDeletion')->withNoArgs()->once()->andReturn(false);
        $file2->shouldReceive('isDeletion')->withNoArgs()->once()->andReturn(false);
        $file3->shouldReceive('isDeletion')->withNoArgs()->once()->andReturn(true);

        $this->filesystem->shouldReceive('exists')->with('file1.txt')->once()->andReturn(true);
        $this->filesystem->shouldReceive('exists')->with('file2.txt')->once()->andReturn(true);
        $this->filesystem->shouldReceive('exists')->with('file3.txt')->never();

        $diff = m::mock(Diff::class);
        $diff->shouldReceive('getFiles')->withNoArgs()->once()->andReturn([$file1, $file2, $file3]);

        $this->repository->shouldReceive('getWorkingCopy->getDiffStaged')->withNoArgs()->once()->andReturn($diff);
        $result = $this->git->getChangedFiles(null);

        $this->assertInstanceOf(FilesCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(SplFileInfo::class, $result[0]);
        $this->assertSame('file1.txt', $result[0]->getPathname());
        $this->assertInstanceOf(SplFileInfo::class, $result[1]);
        $this->assertSame('file2.txt', $result[1]->getPathname());
    }

    public function testGetChangedFilesNonExistingFiles()
    {
        $file = m::mock(File::class);
        $file->shouldReceive('isDeletion')->withNoArgs()->once()->andReturn(false);
        $file->shouldReceive('isRename')->withNoArgs()->once()->andReturn(false);
        $file->shouldReceive('getNewName')->withNoArgs()->never();
        $file->shouldReceive('getName')->withNoArgs()->once()->andReturn('file.txt');

        $this->filesystem->shouldReceive('exists')->with('file.txt')->once()->andReturn(false);

        $diff = m::mock(Diff::class);
        $diff->shouldReceive('getFiles')->withNoArgs()->once()->andReturn([$file]);

        $this->repository->shouldReceive('getWorkingCopy->getDiffStaged')->withNoArgs()->once()->andReturn($diff);
        $result = $this->git->getChangedFiles(null);

        $this->assertInstanceOf(FilesCollection::class, $result);
        $this->assertCount(0, $result);
    }

    public function testGetChangedFilesFromRawDiff()
    {
        $rawDiff = 'diff --git a/file.txt b/file.txt
new file mode 100644
index 0000000000000000000000000000000000000000..9766475a4185a151dc9d56d614ffb9aaea3bfd42
--- /dev/null
+++ b/file.txt
@@ -0,0 +1 @@
+content
';
        $this->filesystem->shouldReceive('exists')->with('file.txt')->once()->andReturn(true);

        $result = $this->git->getChangedFiles($rawDiff);

        $this->assertInstanceOf(FilesCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(SplFileInfo::class, $result[0]);
        $this->assertSame('file.txt', $result[0]->getPathname());
    }
}
