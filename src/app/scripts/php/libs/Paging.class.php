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
        
        public function container($template, $size, $number = "", $index = "", $offset = "") {
            if ($number != "") {
                $index = intval($number) - 1;
            }

            if ($offset != "") {
                if ($offset % $size == 0) {
                    $index = $offset / $size;
                } else {

                }
            }

            if ($index == "") {
                $index = 0;
            } else {
                $offset = "";
            }

            $prevContainer = $this->container;
            $this->container = new PagingModel($size, $index);
            if ($offset != "") {
                $this->container->setOffset($offset);
            }

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
        
        public function getCurrentOffset() {
            $this->ensureContainer();
            return $this->container->getOffset();
        }
        
        public function getPrevIndex() {
            $this->ensureContainer();
            $current = $this->container->getCurrentIndex();
            if ($current > 0) {
                return $current - 1;
            }

            return null;
        }
        
        public function getPrevNumber() {
            $this->ensureContainer();
            $index = $this->getPrevIndex();
            if ($index !== null) {
                return $index + 1;
            }
            
            return null;
        }
        
        public function getPrevOffset() {
            $this->ensureContainer();
            $current = $this->container->getOffset();
            if ($current > 0) {
                $prev = $current - $this->container->getSize();
                if ($prev < 0) {
                    $prev = 0;
                }

                return $prev;
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
        
        public function getNextOffset() {
            $this->ensureContainer();
            $current = $this->container->getOffset();
            $size = $this->container->getSize();
            $count = $this->container->getTotalCount();
            if ($current + $size < $count) {
                return $current + $size;
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