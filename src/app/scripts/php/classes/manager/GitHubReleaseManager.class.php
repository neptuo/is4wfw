<?php

require_once(APP_SCRIPTS_PHP_PATH . "libs/BaseTagLib.class.php");
require_once("BaseHttpManager.class.php");
require_once("Version.class.php");

class GitHubReleaseManager extends BaseHttpManager {
    private static $Url = 'https://api.github.com/repos/neptuo/is4wfw/releases';

    public function getList() {
        $result = array('result' => false, 'log' => '', 'data' => array());

        $json = parent::httpGetJson(self::$Url);
        if ($json == null) {
            $result['log'] .= "Unnable to get releases from '" . self::$Url . "'.";
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
                
            $fullUrl = null;
            $fullSize = null;
            $patchUrl = null;
            $patchSize = null;
            foreach ($release->assets as $asset) {
                if ($asset->content_type == "application/x-zip-compressed") {
                    $assetName = $asset->name;
                    if (strpos($assetName, '-patch') > 0) {
                        $patchUrl = $asset->browser_download_url;
                        $patchSize = $asset->size;
                    } else {
                        $fullUrl = $asset->browser_download_url;
                        $fullSize = $asset->size;
                    }
                }
            }

            if ($fullUrl != null || $patchUrl != null) {
                $release = array(
                    'version' => Version::parse($release->tag_name),
                    'published_at' => $release->published_at,
                    'html_url' => $release->html_url,
                    'download' => array()
                );

                if ($fullUrl != null) {
                    $release['download']['full'] = array(
                        'url' => $fullUrl,
                        'size' => $fullSize
                    );
                }

                if ($patchUrl != null) {
                    $release['download']['patch'] = array(
                        'url' => $patchUrl,
                        'size' => $patchSize
                    );
                }
                
                $result['data'][] = $release;
            }
        }

        $result['result'] = true;
        return $result;
    }
}
