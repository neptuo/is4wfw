<?php

	class EmbeddedResource {
	
		private $id;
		private $type;
		private $url;
		private $rid;
		private $cache;
		
		public function __construct($id = null, $type = null, $url = null, $rid = null, $cache = null) {
			$this->setId($id);
			$this->setType($type);
			$this->setUrl($url);
			$this->setRid($rid);
			$this->setCache($cache);
		}
	
		public function getId() {
			return $this->id;
		}
		public function setId($id) {
			$this->id = $id;
		}
	
		public function getType() {
			return $this->type;
		}
		public function setType($type) {
			$this->type = $type;
		}
	
		public function getUrl() {
			return $this->url;
		}
		public function setUrl($url) {
			$this->url = $url;
		}
	
		public function getRid() {
			return $this->rid;
		}
		public function setRid($rid) {
			$this->rid = $rid;
		}
	
		public function getCache() {
			return $this->cache;
		}
		public function setCache($cache) {
			$this->cache = $cache;
		}
	
	}

?>