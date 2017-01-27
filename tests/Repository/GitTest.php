<?php

namespace ClickNow\Checker\Repository;

use Gitonomy\Git\Diff\Diff;
use Gitonomy\Git\Diff\File;
use Gitonomy\Git\Repository;
use Mockery as m;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @group  repository
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
     * @var \Symfony\Component\Process\ProcessBuilder|\Mockery\MockInterface
     */
    protected $processBuilder;

    /**
     * @var \ClickNow\Checker\Repository\Git
     */
    protected $git;

    protected function setUp()
    {
        $this->repository = m::mock(Repository::class);
        $this->filesystem = m::mock(Filesystem::class);
        $this->processBuilder = m::mock(ProcessBuilder::class);
        $this->processBuilder->shouldReceive('setPrefix')->with('git')->once()->andReturnSelf();
        $this->git = new Git($this->repository, $this->filesystem, $this->processBuilder);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testGetCommitMessage()
    {
        $tmpDir = __DIR__.'/tmp/';

        $fs = new Filesystem();
        $fs->mkdir($tmpDir);

        file_put_contents($tmpDir.'COMMIT_EDITMSG', 'foo');

        $this->repository->shouldReceive('getGitDir')->withNoArgs()->once()->andReturn($tmpDir);
        $this->filesystem->shouldReceive('exists')->withAnyArgs()->once()->andReturn(true);

        $this->assertSame('foo', $this->git->getCommitMessage());

        $fs->remove($tmpDir);
    }

    public function testGetCommitMessageError()
    {
        $this->repository->shouldReceive('getGitDir')->withNoArgs()->once()->andReturn('./');
        $this->filesystem->shouldReceive('exists')->withAnyArgs()->once()->andReturn(false);

        $this->assertNull($this->git->getCommitMessage());
    }

    public function testGetUserName()
    {
        $process = m::mock(Process::class);
        $process->shouldReceive('stop')->withAnyArgs()->atMost()->once()->andReturnNull();
        $process->shouldReceive('run')->withNoArgs()->once()->andReturnNull();
        $process->shouldReceive('isSuccessful')->withNoArgs()->once()->andReturn(true);
        $process->shouldReceive('getOutput')->withNoArgs()->once()->andReturn('foo');

        $this->processBuilder->shouldReceive('setArguments')->with(['config', 'user.name'])->once()->andReturnSelf();
        $this->processBuilder->shouldReceive('getProcess')->withNoArgs()->once()->andReturn($process);

        $this->assertSame('foo', $this->git->getUserName());
    }

    public function testGetUserNameError()
    {
        $process = m::mock(Process::class);
        $process->shouldReceive('stop')->withAnyArgs()->atMost()->once()->andReturnNull();
        $process->shouldReceive('run')->withNoArgs()->once()->andReturnNull();
        $process->shouldReceive('isSuccessful')->withNoArgs()->once()->andReturn(false);
        $process->shouldReceive('getOutput')->withNoArgs()->never();

        $this->processBuilder->shouldReceive('setArguments')->with(['config', 'user.name'])->once()->andReturnSelf();
        $this->processBuilder->shouldReceive('getProcess')->withNoArgs()->once()->andReturn($process);

        $this->assertNull($this->git->getUserName());
    }

    public function testGetUserEmail()
    {
        $process = m::mock(Process::class);
        $process->shouldReceive('stop')->withAnyArgs()->atMost()->once()->andReturnNull();
        $process->shouldReceive('run')->withNoArgs()->once()->andReturnNull();
        $process->shouldReceive('isSuccessful')->withNoArgs()->once()->andReturn(false);
        $process->shouldReceive('getOutput')->withNoArgs()->never();

        $this->processBuilder->shouldReceive('setArguments')->with(['config', 'user.email'])->once()->andReturnSelf();
        $this->processBuilder->shouldReceive('getProcess')->withNoArgs()->once()->andReturn($process);

        $this->assertNull($this->git->getUserEmail());
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

    public function testGetCommittedFiles()
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

        $this->repository->shouldReceive('getDiff')->with(['@{u}', 'HEAD'])->once()->andReturn($diff);
        $result = $this->git->getCommittedFiles(null);

        $this->assertInstanceOf(FilesCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(SplFileInfo::class, $result[0]);
        $this->assertSame('file1.txt', $result[0]->getPathname());
        $this->assertInstanceOf(SplFileInfo::class, $result[1]);
        $this->assertSame('file2.txt', $result[1]->getPathname());
    }

    public function testGetCommittedFilesNonExistingFiles()
    {
        $file = m::mock(File::class);
        $file->shouldReceive('isDeletion')->withNoArgs()->once()->andReturn(false);
        $file->shouldReceive('isRename')->withNoArgs()->once()->andReturn(false);
        $file->shouldReceive('getNewName')->withNoArgs()->never();
        $file->shouldReceive('getName')->withNoArgs()->once()->andReturn('file.txt');

        $this->filesystem->shouldReceive('exists')->with('file.txt')->once()->andReturn(false);

        $diff = m::mock(Diff::class);
        $diff->shouldReceive('getFiles')->withNoArgs()->once()->andReturn([$file]);

        $this->repository->shouldReceive('getDiff')->with(['@{u}', 'HEAD'])->once()->andReturn($diff);
        $result = $this->git->getCommittedFiles(null);

        $this->assertInstanceOf(FilesCollection::class, $result);
        $this->assertCount(0, $result);
    }

    public function testGetCommittedFilesFromRawDiff()
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

        $result = $this->git->getCommittedFiles($rawDiff);

        $this->assertInstanceOf(FilesCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(SplFileInfo::class, $result[0]);
        $this->assertSame('file.txt', $result[0]->getPathname());
    }
}
