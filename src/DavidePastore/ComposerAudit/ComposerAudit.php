<?php

namespace DavidePastore\ComposerAudit;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Json\JsonFile;
use Composer\IO\IOInterface;
use Composer\Package;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PluginEvents;

use SensioLabs\Security\SecurityChecker;

class ComposerAudit implements PluginInterface, EventSubscriberInterface
{
    protected $composer;
    protected $io;
    

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public static function getSubscribedEvents()
    {
        return array(
            "post-install-cmd" => array(
                array('securityAdvisoriesCheck', 0)
            ),
            "post-update-cmd" => array(
                array('securityAdvisoriesCheck', 0)
            )
        );
    }

    public function securityAdvisoriesCheck()
    {
        $path = $this->getComposerLockPath();
        
        //$this->io->write("Path: " . $path);
        $checker = new SecurityChecker();
        $alerts = $checker->check($path);
        
        if (count($alerts) == 0) {
            $this->io->write("All good from SensioLabs security advisories.");
        } else {
            $this->io->write("ALERTS from SensioLabs security advisories.");
            $this->io->write("");
            foreach ($alerts as $projectName => $project) {
                $version = $project['version'];
                //print_r($project);
                $this->io->write(" *** " . $projectName . "[" . $version . "] *** ");
                $this->io->write("");
                
                foreach ($project['advisories'] as $advisoryName => $advisory) {
                    $this->io->write(" * " . $advisoryName);
                    $this->io->write($advisory['title']);
                    $this->io->write($advisory['link']);
                    $this->io->write($advisory['cve']);
                    $this->io->write("");
                }
                
                $this->io->write("");
            }
            $this->io->write("Please fix these alerts from SensioLabs security advisories.");
        }
    }

    /**
     * Get the path of composer.lock file.
     *
     * @return The path of composer.lock file.
     */
    private function getComposerLockPath()
    {
        $locker = $this->composer->getLocker();

        $closure = function () {
            return $this->lockFile;
        };
        //Get the composer.lock file
        $getFile = \Closure::bind($closure, $locker, 'Composer\Package\Locker');

        $file = $getFile();

        //Get path of composer.lock file
        $closure = function () {
            return $this->path;
        };

        $getPath = \Closure::bind($closure, $file, 'Composer\Json\JsonFile');

        return $getPath();
    }
}
