<?php

namespace DavidePastore\ComposerAudit\Tests;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

class ComposerAuditTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Composer
     */
    private $composer;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|IOInterface
     */
    private $io;

    /**
     * Array.
     *
     * @var [type]
     */
    private $packages;

    /**
     * Run before each test.
     */
    public function setUp()
    {
        $this->composer = $this->getMockBuilder('Composer\Composer')
          ->disableOriginalConstructor()
          ->getMock();
        $this->io = $this->getMock('Composer\IO\IOInterface');
    }

    /**
     * Setup for the POST requests.
     */
    /*
    public function setUpPost()
    {
        $dispatcher = $this->getMockBuilder('Composer\EventDispatcher\EventDispatcher')
          ->disableOriginalConstructor()
          ->getMock();
        $this->composer->expects($this->once())
          ->method('getEventDispatcher')
          ->willReturn($dispatcher);
        $dispatcher->expects($this->once())
          ->method('addSubscriber')
          ->with($this->plugin);
        $this->plugin->activate($this->composer, $this->io);
    }

    public function testRunPostInstall()
    {
        $event = new Event(ScriptEvents::POST_INSTALL_CMD, $this->composer, $this->io);
        $this->expects($this->once())
            ->method('postInstall');
        $this->plugin->listen($event);
    }
    */

    public function testInstall($packages)
    {
        // Don't proceed if packages haven't changed.
        if ($packages == self::dump()) {
            return false;
        }
        putenv('COMPOSER_HOME='.__DIR__.'/../../vendor/bin/composer');
        $this->createComposerJson($packages);
        chdir(storage_dir());
        // Setup composer output formatter
        $stream = fopen('php://temp', 'w+');
        $output = new StreamOutput($stream);
        // Programmatically run `composer install`
        $application = new Application();
        $application->setAutoExit(false);
        $code = $application->run(new ArrayInput(array('command' => 'install')), $output);
        // remove composer.lock
        if (file_exists(storage_dir().'composer.lock')) {
            unlink(storage_dir().'composer.lock');
        }
        // rewind stream to read full contents
        rewind($stream);

        return stream_get_contents($stream);
    }

    protected static function createComposerJson($packages)
    {
        $composer_json = str_replace("\/", '/', json_encode(array(
            'config' => array('vendor-dir' => self::VENDOR_DIR),
            'require' => $packages,
            //
            // TODO:
            // windowsazure requires PEAR repository
            //
            'repositories' => array(array(
                'type' => 'pear',
                'url' => 'http://pear.php.net',
            )),
            'preferred-install' => 'dist',
        )));

        return file_put_contents(storage_dir().'composer.json', $composer_json);
    }
}
