<?php

namespace Sitepilot\Runtime;

use Sitepilot\Stack\Runtime\AbstractRuntime;

class Runtime extends AbstractRuntime
{
    public function init()
    {
        $this->modifyUser();

        $this->createFolders();

        $this->generateTemplates();
    }

    public function modifyUser()
    {
        ($getId = $this->getProcess(['id', 'runtime']))->run();

        if ('runtime' != $this->getUsername() && $getId->getOutput()) {
            $this->output->writeln("Rename runtime user to " . $this->getUsername());
            $this->getProcess(['usermod', '-l', $this->getUsername(), 'runtime'])->mustRun();
            $this->getProcess(['groupmod', '-n', $this->getUsername(), 'runtime'])->mustRun();
        }

        if ($password = getenv('RUNTIME_USER_PASSWORD')) {
            $this->output->writeln("Update user password");

            $this->getProcess(['chpasswd', '-e'])
                ->setInput("{$this->getUsername()}:{$password}")
                ->mustRun();
        }
    }

    public function createFolders(): void
    {
        $this->output->writeln("Create user folders");
        foreach (['/home/runtime/logs', '/home/runtime/public'] as $folder) {
            $this->filesystem->mkdir($folder, 0750);
            $this->filesystem->chown($folder, $this->getUsername());
            $this->filesystem->chgrp($folder, $this->getUsername());
        }

        $this->output->writeln("Copy .bashrc to user home");
        $this->filesystem->copy('/root/.bashrc', '/home/runtime/.bashrc');
    }

    public function generateTemplates(): void
    {
        $this->output->writeln("Generate lshttpd configuration");
        $this->generateTemplate('lshttpd.conf', '/usr/local/lsws/conf/httpd_config.conf');

        $this->output->writeln("Generate vhost configuration");
        $this->generateTemplate('runtime.conf', '/usr/local/lsws/conf/templates/runtime.conf');
    }

    public function getUsername()
    {
        return $this->getEnv('RUNTIME_USER_NAME');
    }
}
