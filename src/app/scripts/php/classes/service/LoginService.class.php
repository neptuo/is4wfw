<?php

    require_once("GenericService.class.php");

    class LoginService extends GenericService {

        public function post($url, $args, $accept) {
            if($args['username'] != '' && $args['password'] != '') {
                echo 'Arguments OK!';
            } else {
                echo 'Messing arguments!';
            }
        }
    }

?>
