<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once ("GenericService.class.php");

/**
 * Description of LoginService
 *
 * @author Mara
 */
class LoginService extends GenericService {

    public function performPost($url, $args, $accept) {
        if($args['username'] != '' && $args['password'] != '') {
            echo 'Arguments OK!';
        } else {
            echo 'Messing arguments!';
        }
    }
}

?>
