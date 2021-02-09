<?php

    class PagingModel {
        private $size;
        private $currentIndex;
        private $curentOffset = null;
        private $totalCount;
        private $count;
        private $list;

        public function __construct($size, $currentIndex) {
            $this->size = $size;
            $this->currentIndex = $currentIndex;
        }

        public function getSize() {
            return $this->size;
        }

        public function setOffset($offset) {
            $this->curentOffset = $offset;
        }

        public function getOffset() {
            if ($this->curentOffset != null) {
                return $this->curentOffset;
            }

            return $this->currentIndex * $this->size;
        }

        public function getCurrentIndex() {
            return $this->currentIndex;
        }

        public function setTotalCount($total) {
            $this->totalCount = $total;
            $this->count = ceil($this->totalCount / $this->size);
        }

        public function getTotalCount() {
            return $this->totalCount;
        }

        public function getList() {
            if ($this->list == null) {
                $items = [];

                $count = $this->getCount();
                for ($i=0; $i < $count; $i++) { 
                    $items[] = [
                        "index" => $i,
                        "number" => $i + 1
                    ];
                }

                $this->list = new ListModel();
                $this->list->items($items);
            }

            $this->list->render();
            return $this->list;
        }

        public function getCount() {
            return $this->count;
        }
    }

?>