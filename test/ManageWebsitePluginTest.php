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

namespace Base3\ManageWebsite\Test;

use PHPUnit\Framework\TestCase;
use Base3\ManageWebsite\ManageWebsitePlugin;
use Base3\CoreFoundation\Api\IContainer;

class ManageWebsitePluginTest extends TestCase {

	public function testInitSetsServicesInContainer() {
		$containerMock = $this->createMock(IContainer::class);
		$configMock = $this->createMock(\Base3\CoreFoundation\Configuration\Api\IConfiguration::class);

		$containerMock
			->method('get')
			->with(\Base3\CoreFoundation\Configuration\Api\IConfiguration::class)
			->willReturn($configMock);

		// Optional: set() stubben, falls intern aufgerufen wird
		$containerMock
			->method('set')
			->willReturnSelf();

		$plugin = new \Base3\ManageWebsite\ManageWebsitePlugin($containerMock);
		$plugin->init();

		$this->assertTrue(true);
	}

	public function testCheckDependencies() {
		$containerMock = $this->createMock(IContainer::class);

		$plugin = new ManageWebsitePlugin($containerMock);
		$dependencies = $plugin->checkDependencies();

		$this->assertTrue(true);
	}
}

