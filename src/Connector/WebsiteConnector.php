<?php

namespace ManageWebsite\Connector;

use Base3\Api\IOutput;
use Base3\Configuration\Api\IConfiguration;
use Base3\Accesscontrol\Api\IAccesscontrol;

class WebsiteConnector implements IOutput {

    private $configuration;
    private $accesscontrol;
    private $defaultPageSize = 10;

    public function __construct(
        IAccesscontrol $accesscontrol,
        IConfiguration $configuration
    ) {
        $this->accesscontrol = $accesscontrol;
        $this->configuration = $configuration;
    }

    // Implementation of IBase

    public function getName() {
        return "websiteconnector";
    }

    // Implementation of IOutput

    public function getOutput($out = "html") {
        if ($out !== "json") return null;
        if (!$this->accesscontrol->getUserId()) return null;

        $directories = $this->configuration->get('directories');
        $datadir = $directories['data'];

        $file = $datadir . DIRECTORY_SEPARATOR . 'managewebsite' . DIRECTORY_SEPARATOR . 'websites.json';
        if (!file_exists($file)) {
            return json_encode(['error' => true, 'message' => 'File not found']);
        }

        $json = file_get_contents($file);
        $websites = json_decode($json, true);
        if (!is_array($websites)) {
            return json_encode(['error' => true, 'message' => 'Invalid JSON format']);
        }

        // Boolesche Werte in Strings umwandeln
        array_walk_recursive($websites, function (&$value, $key) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
        });

        // Sortierung
        $sort = $_GET['sort'] ?? 'name';
        $direction = strtolower($_GET['direction'] ?? 'asc');
        usort($websites, function ($a, $b) use ($sort, $direction) {
            if ($sort == 'load_time_ms') {
                return $direction === 'desc'
                    ? $b[$sort] <=> $a[$sort]
                    : $a[$sort] <=> $b[$sort];
            }
            $aVal = strtolower($a[$sort] ?? '');
            $bVal = strtolower($b[$sort] ?? '');
            return ($direction === 'desc' ? -1 : 1) * strcmp($aVal, $bVal);
        });

        // Filter
        $filters = $_GET['filter'] ?? [];
        $websites = array_filter($websites, function ($site) use ($filters) {
            foreach ($filters as $key => $val) {
                if (!isset($site[$key])) return false;
                if (stripos($site[$key], $val) === false) return false;
            }
            return true;
        });

        // Paging
        $total = count($websites);
        $pageSize = $_GET['pageSize'] ?? $this->defaultPageSize;
        $totalPages = ceil($total / $pageSize);
        $page = min(max(1, intval($_GET['page'] ?? 1)), $totalPages);
        $offset = ($page - 1) * $pageSize;
        $pagedData = array_slice($websites, $offset, $pageSize);

        return json_encode([
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPages' => $totalPages,
            'data' => $pagedData
        ]);
    }

    public function getHelp() {
        return "Liefert eine Liste gecrawlter Websites als JSON (aus websites.json). Optional: ?sort=name&direction=asc&page=1&filter[key]=value";
    }
}

