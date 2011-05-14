<?php

class entry extends simpleCRUD {

    public function __construct() {
        parent::__construct();
        $this->table = "entries";
    }

}