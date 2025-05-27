<?php

namespace ManageWebsite\Job;

use Base3\Worker\Api\IJob;
use Base3\Configuration\Api\IConfiguration;
use Base3\Api\ICheck;

class WebsiteLoaderJob implements IJob, ICheck {

	private $configuration;
	private $nextRun = 6 * 3600;

	public function __construct(IConfiguration $configuration) {
		$this->configuration = $configuration;
	}

	// Implementation of IBase

	public static function getName(): string {
		return 'websiteloaderjob';
	}

	// Implementation of IJob

	public function isActive() {
		return true;
	}

	public function getPriority() {
		return 1;
	}

	public function go() {
		$dataDir = $this->getDataDir();
		if (!strlen($dataDir)) return 'data dir undefined';

		$nextRunFile = $this->getNextRunFile();
		$now = time();
		$nextRun = is_file($nextRunFile) ? intval(file_get_contents($nextRunFile)) : 0;
		if ($now < $nextRun) return 'skipped (next run: ' . date('c', $nextRun) . ')';
		file_put_contents($nextRunFile, strval($now + $this->nextRun));

		$result = $this->getWebsites();
		return $result;
	}

	// Implementation of ICheck

	public function checkDependencies() {
		return array(
			'managewebsite_dir_defined' => strlen($this->getDataDir()) ? 'Ok' : 'managewebsite dir not defined',
			'managewebsite_dir_writable' => is_writable($this->getDataDir()) ? 'Ok' : 'managewebsite dir not writable'
		);
	}

	// Private methods

	private function getDataDir() {
		$directories = $this->configuration->get('directories');
		return isset($directories['data'])
			? $directories['data'] . DIRECTORY_SEPARATOR . 'managewebsite' . DIRECTORY_SEPARATOR
			: '';
	}

	private function getNextRunFile(): string {
		return $this->getDataDir() . 'nextrun';
	}

private function getWebsites(): string {
    $dataDir = $this->getDataDir();
    if (!strlen($dataDir)) return 'data dir undefined';

    $configFile = $dataDir . 'managewebsite-config.ini';
    if (!is_file($configFile)) return 'config file missing';

    $configuration = parse_ini_file($configFile, true);
    $websites = [];

    foreach ($configuration as $group => $entries) {
        if (!isset($entries['url'])) continue;
        $urls = is_array($entries['url']) ? $entries['url'] : [$entries['url']];
        foreach ($urls as $url) {
            $info = [
                'group' => $group,
                'url' => $url,
                'http_status' => null,
                'title' => null,
                'meta_generator' => null,
                'meta_description' => null,
                'meta_keywords' => null,
                'last_access' => date('c'),
                'load_time_ms' => null,
                'final_url' => null,
                'content_length' => null,
                'ssl_info' => null,
                'has_robots_txt' => null,
                'has_favicon_ico' => null
            ];

            // HTML abrufen
            $start = microtime(true);
            $curl = curl_init($url);
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_USERAGENT => 'WebsiteLoaderJob/1.0',
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);
            $html = curl_exec($curl);
            $info['load_time_ms'] = round((microtime(true) - $start) * 1000);
            $info['http_status'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $info['final_url'] = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
            $info['content_length'] = curl_getinfo($curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

            // SSL-Info (wenn HTTPS)
            if (stripos($info['final_url'], 'https://') === 0) {
                $cert = curl_getinfo($curl, CURLINFO_CERTINFO);
                if ($cert && is_array($cert) && isset($cert[0])) {
                    $info['ssl_info'] = [
                        'subject' => $cert[0]['Subject'] ?? null,
                        'issuer' => $cert[0]['Issuer'] ?? null
                    ];
                }
            }

            curl_close($curl);

            if ($html !== false && $info['http_status'] >= 200 && $info['http_status'] < 400) {
                // Titel extrahieren
                if (preg_match('/<title>(.*?)<\/title>/is', $html, $matches)) {
                    $info['title'] = trim($matches[1]);
                }

                // Meta Generator extrahieren
                if (preg_match('/<meta[^>]+name=["\']?generator["\']?[^>]*content=["\']([^"\']+)["\']/i', $html, $matches)) {
                    $info['meta_generator'] = trim($matches[1]);
                }

                // Meta Description extrahieren
                if (preg_match('/<meta[^>]+name=["\']?description["\']?[^>]*content=["\']([^"\']+)["\']/i', $html, $matches)) {
                    $info['meta_description'] = trim($matches[1]);
                }

                // Meta Keywords extrahieren
                if (preg_match('/<meta[^>]+name=["\']?keywords["\']?[^>]*content=["\']([^"\']+)["\']/i', $html, $matches)) {
                    $info['meta_keywords'] = trim($matches[1]);
                }
            }

            $baseUrl = rtrim($url, '/');
            $info['has_robots_txt'] = $this->checkUrlExists($baseUrl . '/robots.txt');
            $info['has_favicon_ico'] = $this->checkUrlExists($baseUrl . '/favicon.ico');

            $websites[] = $info;
        }
    }

    $websitesUtf8 = $this->convertToUtf8($websites);

    file_put_contents($dataDir . 'websites.json', json_encode($websitesUtf8, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    return 'done';
}

private function convertToUtf8($data) {
    if (is_array($data)) {
        return array_map([$this, 'convertToUtf8'], $data);
    } elseif (is_string($data)) {
        // return utf8_encode($data);
        return mb_convert_encoding($data, 'UTF-8', 'auto');
    }
    return $data;
}

private function checkUrlExists(string $url): bool {
    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_NOBODY => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 3,
        CURLOPT_USERAGENT => 'WebsiteLoaderJob/1.0',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);
    curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    return $httpCode >= 200 && $httpCode < 400;
}

}
