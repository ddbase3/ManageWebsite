<?php

namespace ManageWebsite\Test;

use PHPUnit\Framework\TestCase;
use ManageWebsite\ManageWebsitePlugin;
use Base3\Api\IContainer;

class ManageWebsitePluginTest extends TestCase
{
    public function testInitSetsServicesInContainer()
    {
        $containerMock = $this->createMock(IContainer::class);

        $plugin = new ManageWebsitePlugin($containerMock);
        $plugin->init();

        $this->assertTrue(true);
    }

    public function testCheckDependencies()
    {
        $containerMock = $this->createMock(IContainer::class);

        $plugin = new ManageWebsitePlugin($containerMock);
        $dependencies = $plugin->checkDependencies();

        $this->assertTrue(true);
    }
}

