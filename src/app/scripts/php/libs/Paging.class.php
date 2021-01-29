<?php

require_once("BaseTagLib.class.php");

	/**
	 * 
	 *  Class Paging. 
	 *      
	 *  @author     maraf
	 *  @timestamp  2021-01-29
	 * 
	 */
	class Paging extends BaseTagLib {

        private $container;

		public function __construct() {
			parent::setTagLibXml("Paging.xml");
        }

        private function ensureContainer() {
            if ($this->container == null) {
                throw new Exception("Missing paging container.");
            }
        }
        
        public function container($template, $size, $number = "", $index = "") {
            if ($number != "") {
                $index = intval($number) - 1;
            }

            if ($index == "") {
                $index = 0;
            }

            $prevContainer = $this->container;
            $this->container = new PagingModel($size, $index);

            $result = $template();
            
            $this->container = $prevContainer;

            return $result;
        }

        public function getContainer() {
            $this->ensureContainer();
            return $this->container;
        }

        public function getList() {
            $this->ensureContainer();
            return $this->container->getList();
        }
        
        public function getTotalCount() {
            $this->ensureContainer();
            return $this->container->getTotalCount();
        }
        
        public function getCurrentIndex() {
            $this->ensureContainer();
            return $this->container->getCurrentIndex();
        }
        
        public function getCurrentNumber() {
            $this->ensureContainer();
            return $this->container->getCurrentIndex() + 1;
        }
        
        public function getPreviousIndex() {
            $this->ensureContainer();
            $current = $this->container->getCurrentIndex();
            if ($current > 0) {
                return $current - 1;
            }

            return null;
        }
        
        public function getPreviousNumber() {
            $this->ensureContainer();
            $index = $this->getPreviousIndex();
            if ($index !== null) {
                return $index + 1;
            }
            
            return null;
        }
        
        public function getNextIndex() {
            $this->ensureContainer();
            $current = $this->container->getCurrentIndex();
            $count = $this->container->getCount();
            if ($current + 1 < $count) {
                return $current + 1;
            }

            return null;
        }
        
        public function getNextNumber() {
            $this->ensureContainer();
            $index = $this->getNextIndex();
            if ($index != null) {
                return $index + 1;
            }
            
            return null;
        }
        
        public function getIndex() {
            $this->ensureContainer();
            return $this->container->getList()->field("index");
        }
        
        public function getNumber() {
            $this->ensureContainer();
            return $this->container->getList()->field("number");
        }
    }

?>