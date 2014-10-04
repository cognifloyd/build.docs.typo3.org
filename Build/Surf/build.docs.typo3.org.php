<?php

use TYPO3\Surf\Domain\Model\Node;
use TYPO3\Surf\Domain\Model\SimpleWorkflow;
use TYPO3\Surf\Application\TYPO3\Flow;

$node = new Node('build.docs.typo3.box');
$node->setHostname('build.docs.typo3.box');
$node->setOption('username', 'vagrant');

$application = new Flow();
$application->setOption('repositoryUrl', '');
$application->setDeploymentPath('var/www/vhosts/build.docs.typo3.org/');
$application->setOption('keepReleases', 10);
$application->setContext('Development');
$application->setOption('packageMethod', 'git');
$application->setOption('transferMethod', 'rsync');
$application->setOption('updateMethod', NULL);

if (file_exists('/usr/bin/composer')) {
	$application->setOption('composerCommandPath', '/usr/bin/composer');
} elseif (file_exists('/opt/local/bin/composer')) {
	$application->setOption('composerCommandPath', '/opt/local/bin/composer');
} else {
	// last is just a guess...
	$application->setOption('composerCommandPath', '/usr/local/bin/composer');
}

$workflow = new SimpleWorkflow();
$workflow->setEnableRollback(FALSE);

/** @var \TYPO3\Surf\Domain\Model\Deployment $deployment */
$application->addNode($node);
$deployment->addApplication($application);
$deployment->setWorkflow($workflow);

$deployment->onInitialize(function() use ($workflow, $application) {
	$workflow->removeTask('typo3.surf:typo3:flow:copyconfiguration');
	$workflow->removeTask('typo3.surf:typo3:neos:importsite');

	$workflow->afterStage('update', array('typo3.surf:typo3:flow:setfilepermissions'));
});
