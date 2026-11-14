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


namespace Base3\ManageWebsite\ContentControl;

use Base3\CoreFoundation\Api\IAssetResolver;
use Base3\CoreFoundation\Api\IMvcView;
use Base3\CoreFoundation\LinkTarget\Api\ILinkTargetService;
use Base3\Base3Manager\Service\Base3Manager;
use Base3\Base3Manager\ContentControl\AbstractContentControl;

class WebsiteContentControl extends AbstractContentControl {

	public function __construct(
		protected IMvcView $view,
		protected Base3Manager $base3manager,
		protected ILinkTargetService $linktargetservice,
		private readonly IAssetResolver $assetResolver
	) {
		parent::__construct($view, $base3manager, $linktargetservice);
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
