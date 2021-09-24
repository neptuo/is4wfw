<?php

require_once(APP_SCRIPTS_PHP_PATH . "libs/BaseTagLib.class.php");
require_once("BaseHttpManager.class.php");
require_once("Version.class.php");

class AppVeyorReleaseManager extends BaseHttpManager {
    private static $BaseUrl = 'https://ci.appveyor.com/api';
    private static $Url = 'https://ci.appveyor.com/api/projects/neptuo/is4wfw';
    private static $WebUrl = 'https://ci.appveyor.com/project/neptuo/is4wfw';

    public function getList($count = 3) {
        $result = array('result' => false, 'log' => '', 'data' => array());

        $url = self::$Url . "/history?recordsNumber=$count";
        $json = parent::httpGetJson($url);
        if ($json == null) {
            $result['log'] .= "Unnable to get releases from '" . $url . "'.";
            return $result;
        }

        if ($json->builds == null) {
            $result['log'] .= "No release found.";
            return $result;
        }

        foreach ($json->builds as $build) {
            if ($build->status != "success") {
                continue;
            }

            $version = Version::parse($build->tag);
            
            $release = [
                "version" => $version,
                "published_at" => $build->finished,
                "html_url" => self::$WebUrl . "/builds/" . $build->buildId,
                "download" => []
            ];
            
            $buildNumber = $build->buildNumber;
            $jobId = $this->getJobId($buildNumber);
            if ($jobId == null) {
                continue;
            }
            
            $artifactsUrl = self::$BaseUrl . "/buildjobs/$jobId/artifacts";
            $artifacts = parent::httpGetJson($artifactsUrl);
            if ($artifacts == null) {
                continue;
            }
            
            foreach ($artifacts as $artifact) {
                if ($artifact->type == "Zip") {
                    $artifactName = $artifact->fileName;
                    $artifactUrl = $artifactsUrl . "/" . $artifactName;
                    if (strpos($artifactName, '-patch') > 0) {
                        $patchUrl = $artifactUrl;
                        $patchSize = $artifact->size;
                    } else {
                        $fullUrl = $artifactUrl;
                        $fullSize = $artifact->size;
                    }
                }
            }

            if ($fullUrl != null || $patchUrl != null) {
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

    private function getJobId($buildNumber) {
        $url = self::$Url . "/build/$buildNumber";
        $json = parent::httpGetJson($url);
        if ($json == null || $json->build == null || $json->build->jobs == null || count($json->build->jobs) == 0) {
            return null;
        }

        return $json->build->jobs[0]->jobId;
    }
}
