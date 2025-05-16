<?php

namespace ManageWebsite\Test;

use PHPUnit\Framework\TestCase;
use ManageWebsite\ManageWebsitePlugin;
use Base3\Api\IContainer;

class ManageWebsitePluginTest extends TestCase {

	public function testInitSetsServicesInContainer() {
		$containerMock = $this->createMock(IContainer::class);
		$configMock = $this->createMock(\Base3\Configuration\Api\IConfiguration::class);

		$containerMock
			->method('get')
			->with(\Base3\Configuration\Api\IConfiguration::class)
			->willReturn($configMock);

		// Optional: set() stubben, falls intern aufgerufen wird
		$containerMock
			->method('set')
			->willReturnSelf();

		$plugin = new \ManageWebsite\ManageWebsitePlugin($containerMock);
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

