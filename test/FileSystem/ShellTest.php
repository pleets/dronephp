<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace DroneTest\FileSystem;

use Drone\FileSystem\Shell;
use PHPUnit\Framework\TestCase;

class ShellTest extends TestCase
{
    /**
     * Tests home path position
     *
     * @return null
     */
    public function testHomePath()
    {
        mkdir('foo');

        $shell = new Shell('foo');
        $this->assertSame('foo', $shell->getHome());
        $this->assertSame('foo', basename($shell->pwd()));
        $this->assertSame('foo', basename(getcwd()));
    }

    /**
     * Tests file creation
     *
     * @return null
     */
    public function testFileCreation()
    {
        $shell = new Shell('foo');
        $cmd = $shell->touch('new.txt');

        $this->assertTrue($cmd);
        $this->assertTrue(file_exists('new.txt'));
    }

    /**
     * Tests changing path
     *
     * @return null
     */
    public function testChangePath()
    {
        $shell = new Shell('foo');
        $shell->cd('..');

        $this->assertTrue(file_exists('foo'));
        $this->assertTrue(file_exists('foo/new.txt'));

        # back to home path
        $shell->cd();
        $this->assertSame('foo', basename($shell->pwd()));
        $this->assertSame('foo', basename(getcwd()));
    }

    /**
     * Tests simple copy
     *
     * @return null
     */
    public function testFileCopy()
    {
        $shell = new Shell('foo');
        $shell->cp('new.txt', 'new2.txt');

        $this->assertTrue(file_exists('new2.txt'));

        mkdir('bar');

        $shell->cp('new.txt', 'bar');
        $this->assertTrue(file_exists('bar/new.txt'));
    }

    /**
     * Tests directory creation
     *
     * @return null
     */
    public function testMakeDirectory()
    {
        $shell = new Shell('foo');
        $cmd = $shell->mkdir('foo2');

        $this->assertTrue(file_exists('foo2'));
        $this->assertTrue(is_dir('foo2'));
        $this->assertTrue($cmd);
    }

    /**
     * Tests the list of files retrived by ls command
     *
     * @return null
     */
    public function testListingFiles()
    {
        $shell = new Shell('foo');
        $files = $shell->ls();
        sort($files);

        $expected = ['bar', 'foo2', 'new.txt', 'new2.txt'];
        sort($expected);

        $this->assertSame($expected, $files);
    }

    /**
     * Tests the list of files retrived by ls command
     *
     * @return null
     */
    public function testListingFilesRecursively()
    {
        $shell = new Shell('foo');
        $files = $shell->ls('.', true);

        /**
         * Actually the $files variable would be the following array
         * [['bar' => ['new.txt']], ['foo2' => []], 'new.txt', 'new2.txt'];
         */

        # check directories
        $this->assertTrue(array_key_exists('bar', $files));
        $this->assertTrue(is_array($files["bar"]));
        $this->assertTrue(array_key_exists('foo2', $files));
        $this->assertTrue(is_array($files["foo2"]));

        # check files
        $this->assertSame(['new.txt'], $files["bar"]);
        $this->assertTrue(in_array('new.txt', $files));
        $this->assertTrue(in_array('new2.txt', $files));
    }

    /**
     * Tests copying a directory with its contents
     *
     * @return null
     */
    public function testDirectoryCopy()
    {
        $shell = new Shell('foo');

        $shell->touch('foo2/new3.txt');
        $shell->touch('foo2/new4.txt');

        $errorObject = null;

        try
        {
            $shell->cp('foo2', 'foo3');
        }
        catch (\Exception $e)
        {
            # omitting directory
            $errorObject = ($e instanceof \RuntimeException);
        }
        finally
        {
            $this->assertTrue($errorObject, $e->getMessage());
        }

        mkdir('foo3');

        # must be recursive
        $shell->cp(
            'foo2', // directory
            'foo3', // directory
        true);

        $this->assertTrue(file_exists('foo3'));
        $this->assertTrue(is_dir('foo3'));
        $this->assertTrue(file_exists('foo3/foo2'));
        $this->assertTrue(is_dir('foo3/foo2'));
        $this->assertTrue(file_exists('foo3/foo2/new3.txt'));
        $this->assertTrue(file_exists('foo3/foo2/new4.txt'));

        $shell->cp(
            'foo3', // directory
            'foo4', // not a directory or file
        true);

        $this->assertTrue(file_exists('foo4'));
        $this->assertTrue(is_dir('foo4'));
        $this->assertTrue(file_exists('foo4/foo2'));
        $this->assertTrue(is_dir('foo4/foo2'));
        $this->assertTrue(file_exists('foo4/foo2/new3.txt'));
        $this->assertTrue(file_exists('foo4/foo2/new4.txt'));
   }

    /**
     * Tests removing files
     *
     * @return null
     */
    public function testRemovingFiles()
    {
        $shell = new Shell('foo');
        $cmd = $shell->rm('new2.txt');

        $this->assertTrue($cmd);
        $this->assertNotTrue(file_exists('new2.txt'));
    }

    /**
     * Tests removing not empty directories
     *
     * @return null
     */
    public function testRemovingNotEmptyDirectories()
    {
        $shell = new Shell('foo');
        $cmd = $shell->rm('foo4', true);

        $this->assertTrue($cmd);
        $this->assertNotTrue(file_exists('foo4'));
    }

    /**
     * Tests renaming files
     *
     * @return null
     */
    public function testRenamingFiles()
    {
        $shell = new Shell('foo');
        $cmd = $shell->mv('new.txt', 'renamed.txt');

        $this->assertTrue($cmd);
        $this->assertTrue(file_exists('renamed.txt'));
    }

    /**
     * Tests moving files
     *
     * @return null
     */
    public function testMovingFiles()
    {
        $shell = new Shell('foo');
        $cmd = $shell->mv('renamed.txt', 'bar');

        $this->assertTrue($cmd);
        $this->assertTrue(file_exists('bar/renamed.txt'));
    }

    /**
     * Tests removing empty directories
     *
     * @return null
     */
    public function testRemovingDirectories()
    {
        $shell = new Shell('foo');
        mkdir('temp');
        $cmd = $shell->rmdir('temp');

        $this->assertTrue($cmd);
        $this->assertNotTrue(file_exists('temp'));
    }
}