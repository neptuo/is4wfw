<?php

require_once(APP_SCRIPTS_PHP_PATH . "libs/BaseTagLib.class.php");
require_once("BaseHttpManager.class.php");
require_once("Version.class.php");

class GitHubReleaseClient extends BaseHttpManager {
    public function getList($repositoryName) {
        $result = ['result' => false, 'log' => '', 'data' => []];

        $url = "https://api.github.com/repos/$repositoryName/releases";
        $json = parent::httpGetJson($url);
        if ($json == null) {
            $result['log'] .= "Unnable to get releases from '" . $url . "'.";
            return $result;
        }

        if (!is_array($json)) {
            if (isset($json->message)) {
                $result['log'] .= "Remote error: " . $json->message;
            } else {
                $result['log'] .= "No release found.";
            }

            return $result;
        }

        foreach ($json as $release) {
            if ($release->draft) {
                continue;
            }
                
            foreach ($release->assets as $asset) {
                if ($asset->content_type == "application/x-zip-compressed") {
                    $assetName = $asset->name;
                    $fullUrl = $asset->browser_download_url;
                    $fullSize = $asset->size;

                    $release = [
                        "id" => $asset->id,
                        "name" => $assetName,
                        "version" => $release->tag_name,
                        "published_at" => $release->published_at,
                        "html_url" => $release->html_url,
                        "download" => [
                            "url" => $fullUrl,
                            "size" => $fullSize
                        ]
                    ];
                    $result['data'][] = $release;
                }
            }
        }

        $result['result'] = true;
        return $result;
    }

    public function downloadReleaseAsset($repositoryName, $assetId, $fileName) {
        $result = ['result' => false, 'log' => ''];

        $url = "https://api.github.com/repos/$repositoryName/releases/assets/$assetId";
        $json = parent::httpGetJson($url);
        if ($json == null) {
            $result['log'] .= "Unnable to get releases from '" . $url . "'.";
            return $result;
        }

        if ($json->browser_download_url) {
            $content = $this->httpGet($url, [ "Accept: application/octet-stream" ]);
            file_put_contents($fileName, $content);
            $result["result"] = true;
        }

        return $result;
    }
}
