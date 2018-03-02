<?php

    class GenericService {

        private $supportedMethods;

        public function __construct($supportedMethods = array('json', 'xml')) {
            $this->supportedMethods = $supportedMethods;
        }

        public function handleRawRequest($_SERVER, $_GET, $_POST) {
            $url = $this->getFullUrl($_SERVER);
            $method = $_SERVER['REQUEST_METHOD'];
            switch ($method) {
                case 'GET':
                case 'HEAD':
                    $arguments = $_GET;
                    break;
                case 'POST':
                    $arguments = $_POST;
                    break;
                case 'PUT':
                case 'DELETE':
                    parse_str(file_get_contents('php://input'), $arguments);
                    break;
            }
            $accept = $_SERVER['HTTP_ACCEPT'];
            $this->handleRequest($url, $method, $arguments, $accept);
        }

        protected function getFullUrl($_SERVER) {
            $protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
            $location = $_SERVER['REQUEST_URI'];
            if ($_SERVER['QUERY_STRING']) {
                $location = substr($location, 0, strrpos($location, $_SERVER['QUERY_STRING']) - 1);
            }

            return $protocol . '://' . $_SERVER['HTTP_HOST'] . $location;
        }

        public function handleRequest($url, $method, $arguments, $accept) {
            switch ($method) {
                case 'GET':
                    $this->get($url, $arguments, $accept);
                    break;
                case 'HEAD':
                    $this->head($url, $arguments, $accept);
                    break;
                case 'POST':
                    $this->post($url, $arguments, $accept);
                    break;
                case 'PUT':
                    $this->put($url, $arguments, $accept);
                    break;
                case 'DELETE':
                    $this->delete($url, $arguments, $accept);
                    break;
                default:
                    /* 501 (Not Implemented) for any unknown methods */
                    header('Allow: ' . $this->supportedMethods, true, 501);
            }
        }

        protected function methodNotAllowedResponse() {
            /* 405 (Method Not Allowed) */
            header('Allow: ' . $this->supportedMethods, true, 405);
        }

        public function get($url, $arguments, $accept) {
            $this->methodNotAllowedResponse();
        }

        public function head($url, $arguments, $accept) {
            $this->methodNotAllowedResponse();
        }

        public function post($url, $arguments, $accept) {
            $this->methodNotAllowedResponse();
        }

        public function put($url, $arguments, $accept) {
            $this->methodNotAllowedResponse();
        }

        public function delete($url, $arguments, $accept) {
            $this->methodNotAllowedResponse();
        }
    }

?>
