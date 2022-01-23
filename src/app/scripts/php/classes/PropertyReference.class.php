<?php

class PropertyReference {
    private $target;
    private $get;
    private $set;
    private $name;

    public function __construct($target, $get = null, $set = null, $name = null) {
        $this->target = $target;
        $this->get = $get;
        $this->set = $set;
        $this->name = $name;
    }

    public function get() {
        $target = [$this->target, $this->get];

        if ($this->name == null) {
            return call_user_func($target);
        } else {
            return call_user_func($target, $this->name);
        }
    }

    public function set($value) {
        $target = [$this->target, $this->set];
        if ($this->name == null) {
            return call_user_func($target, $value);
        } else {
            return call_user_func($target, $this->name, $value);
        }
    }
}
