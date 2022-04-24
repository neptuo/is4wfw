<?php

namespace php\libs;

require_once("BaseTagLib.class.php");
require_once(APP_SCRIPTS_PHP_PATH . "classes/ListModel.class.php");

use BaseTagLib;
use Exception;
use PhpRuntime;
use ListModel;


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

    public function boundary(callable $template, ?string $name = null, bool $logException = true, array $logParam = []){
        try {
            unset($this->storage[$name]);
            return $template();
        } catch (Exception $e) {
            $this->storage[$name][] = $e;
            return $this->processException($e, $name, $logException, $logParam);
        }
    }

    private function processException($e, $boundaryName, $writeToLog, $logParam) {
        $params = [];
        if ($boundaryName) {
            $params["boundary"] = $boundaryName;
        }
        if (count($logParam) > 0) {
            $params["params"] = $logParam;
        }

        global $logObject;
        if ($writeToLog) {
            return $logObject->exception($e, $params);
        } else {
            return $logObject->getDebugExceptionView($e, $params);
        }
    }
    
    public function isPassed($name) {
        $isPassed = !array_key_exists($name, $this->storage);
        return [PhpRuntime::$DecoratorExecuteName => $isPassed];
    }
    
    public function isFailed($name) {
        if ($name == "*") {
            $isFailed = !empty($this->storage);
        } else {
            $isFailed = array_key_exists($name, $this->storage);
        }
        return [PhpRuntime::$DecoratorExecuteName => $isFailed];
    }

    public function exceptionList(callable $template, ?string $name = "") {
        $model = new ListModel();
        parent::pushListModel($model);

        $data = [];
        foreach ($this->storage as $boundaryName => $exceptions) {
            if (!$name || $name === $boundaryName) {
                foreach ($exceptions as $exception) {
                    $data[] = $exception;
                }
            }
        }

        $model->render();
        $model->items($data);
        $result = $template();

        parent::popListModel();
        return $result;
    }

    public function getExceptionList() {
        return parent::peekListModel();
    }

    public function getExceptionType() {
        return get_class(parent::peekListModel()->currentItem());
    }

    public function getExceptionMessage() {
        return parent::peekListModel()->currentItem()->getMessage();
    }

    public function getExceptionTrace() {
        return parent::peekListModel()->currentItem()->getTraceAsString();
    }
}

?>