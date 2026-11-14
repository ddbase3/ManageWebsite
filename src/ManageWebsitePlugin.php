<?php declare(strict_types=1);

/***********************************************************************
 * This file is part of ManageWebsite for BASE3 Framework.
 *
 * ManageWebsite provides website administration views.
 * It adds website controls through Base3Manager.
 * Application and host integration remain separate owners.
 *
 * Developed by Daniel Dahme
 * Licensed under GPL-3.0
 * https://www.gnu.org/licenses/gpl-3.0.en.html
 *
 * https://base3.de
 ***********************************************************************/


namespace Base3\ManageWebsite;

use Base3\CoreFoundation\Api\IContainer;
use Base3\CoreFoundation\Configuration\Api\IConfiguration;
use Base3\Base3Manager\Plugin\AbstractPlugin;

class ManageWebsitePlugin extends AbstractPlugin {

	// Implementation of IPlugin

	public function init() {

		$this->container
			->set(self::getName(), $this, IContainer::SHARED)
			->set('websiteloaderjob', new \Base3\ManageWebsite\Job\WebsiteLoaderJob($this->container->get(IConfiguration::class)), IContainer::SHARED)
			;
	}

	// Implementation of ICheck

	public function checkDependencies(): array {
		return array(
			"Check" => "Ok"
		);
	}
}
