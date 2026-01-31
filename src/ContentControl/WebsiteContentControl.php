<?php declare(strict_types=1);

namespace ManageWebsite\ContentControl;

use Base3\Api\IAssetResolver;
use Base3\Api\IMvcView;
use Base3Manager\Service\Base3Manager;
use Base3Manager\ContentControl\AbstractContentControl;

class WebsiteContentControl extends AbstractContentControl {

	public function __construct(
		protected IMvcView $view,
		protected Base3Manager $base3manager,
		private readonly IAssetResolver $assetResolver
	) {
		parent::__construct($view, $base3manager);
	}

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

	protected function fillView() {
		$this->view->assign('resolve', fn($src) => $this->assetResolver->resolve($src));
	}
}

