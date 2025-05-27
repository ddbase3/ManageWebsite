<?php declare(strict_types=1);

namespace ManageWebsite\ContentControl;

use Base3Manager\ContentControl\AbstractContentControl;

class WebsiteContentControl extends AbstractContentControl {

        // Implementation of IBase

        public static function getName(): string {
                return "websitecontentcontrol";
        }

	// Implementation of AbstractContentControl

        protected function getPath(): string {
                return DIR_PLUGIN . 'ManageWebsite';
        }

        protected function getTemplate(): string {
                return 'ContentControl/WebsiteContentControl.php';
        }

}

