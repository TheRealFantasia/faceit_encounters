<?php

use EasyCorp\Bundle\EasyDeployBundle\Configuration\Option;
use EasyCorp\Bundle\EasyDeployBundle\Deployer\DefaultDeployer;
use EasyCorp\Bundle\EasyDeployBundle\Requirement\AllowsLoginViaSsh;
use EasyCorp\Bundle\EasyDeployBundle\Requirement\CommandExists;
use EasyCorp\Bundle\EasyDeployBundle\Server\Server;

return new class extends DefaultDeployer
{
    public function getRequirements(): array
    {
        $localhost = $this->getContext()->getLocalHost();
        $allServers = $this->getServers()->findAll();

        $requirements[] = new CommandExists([$localhost], 'git');
        $requirements[] = new CommandExists([$localhost], 'ssh');

        $requirements[] = new AllowsLoginViaSsh($allServers);

        return $requirements;
    }

    public function configure()
    {
        return $this->getConfigBuilder()
            ->server('root@212.227.213.145')
            ->deployDir('/var/www/vhosts/faceit_encounters')
            ->repositoryUrl('git@github.com:TheRealFantasia/faceit_encounters.git')
            ->repositoryBranch('master')
            ->useSshAgentForwarding(true)
        ;
    }

    public function beforePreparing()
    {
        $this->log('Copying over the .env file');
        $this->runRemote('cp {{ deploy_dir }}/env/.env {{ project_dir }}/.env');
    }

    public function beforeFinishingDeploy()
    {
        $this->log('Setting rights for jms cache');
        $this->runRemote('chmod -R 777 {{ project_dir }}/var/cache/prod/jms_serializer/');

        $this->log('Checking Migrations');
        $upToDate = $this->runRemote('{{ console_bin }} doctrine:migrations:up-to-date')[0]->getOutput();
        $this->log($upToDate);
        if (strpos($upToDate, 'Up-to-date') === false) {
            $this->runRemote('{{ console_bin }} doctrine:migrations:diff');
            $this->runRemote('{{ console_bin }} doctrine:migrations:migrate');
        }

        $this->log('Vue prod build');
        $this->runRemote('yarn install');
        $this->runRemote('yarn build');
    }
};
