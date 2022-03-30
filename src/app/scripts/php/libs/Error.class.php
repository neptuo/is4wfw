<?php

namespace php\libs;

require_once("BaseTagLib.class.php");

use BaseTagLib;
use Exception;
use PhpRuntime;


/**
 * 
 *  Class Error.
 *      
 *  @author     maraf
 *  @timestamp  2022-03-28
 * 
 */
class Error extends BaseTagLib {

    private $storage = [];

    public function boundary(callable $template, ?string $name = null){
        try {
            unset($this->storage[$name]);
            return $template();
        } catch (Exception $e) {
            $this->storage[$name][] = $e;
            return $this->logException($e, $name);
        }
    }

    private function logException($e, $boundaryName) {
        $params = [];
        if ($boundaryName) {
            $params["boundary"] = $boundaryName;
        }

        global $logObject;
        return $logObject->exception($e, $params);
    }
    
    public function isPassed($name) {
        $isPassed = !array_key_exists($name, $this->storage);
        return [PhpRuntime::$DecoratorExecuteName => $isPassed];
    }
    
    public function isFailed($name) {
        $isFailed = array_key_exists($name, $this->storage);
        return [PhpRuntime::$DecoratorExecuteName => $isFailed];
    }
}

?>