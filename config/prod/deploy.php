<?php

use EasyCorp\Bundle\EasyDeployBundle\Deployer\DefaultDeployer;

return new class extends DefaultDeployer
{
    public function configure()
    {
        return $this->getConfigBuilder()
            ->server('root@212.227.213.145')
            // the absolute path of the remote server directory where the project is deployed
            ->deployDir('/var/www/vhosts/faceit_encounters')
            // the URL of the Git repository where the project code is hosted
            ->repositoryUrl('https://github.com/TheRealFantasia/faceit_encounters')
            // the repository branch to deploy
            ->repositoryBranch('master')
        ;
    }

    // run some local or remote commands before the deployment is started
    public function beforeStartingDeploy()
    {
        // $this->runLocal('./vendor/bin/simple-phpunit');
    }

    // run some local or remote commands after the deployment is finished
    public function beforeFinishingDeploy()
    {
        $this->runRemote('yarn build');
        // $this->runLocal('say "The deployment has finished."');
    }
};
