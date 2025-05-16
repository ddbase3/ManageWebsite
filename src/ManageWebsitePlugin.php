<?php declare(strict_types=1);

namespace ManageWebsite;

use Base3\Api\IContainer;
use Base3\Configuration\Api\IConfiguration;
use Base3Manager\Plugin\AbstractPlugin;

class ManageWebsitePlugin extends AbstractPlugin {

	// Implementation of IPlugin

	public function init() {

		$this->container
			->set($this->getName(), $this, IContainer::SHARED)
			->set('websiteloaderjob', new \ManageWebsite\Job\WebsiteLoaderJob($this->container->get(IConfiguration::class)), IContainer::SHARED)
			;
	}

	// Implementation of ICheck

	public function checkDependencies(): array {
		return array(
			"Check" => "Ok"
		);
	}
}
