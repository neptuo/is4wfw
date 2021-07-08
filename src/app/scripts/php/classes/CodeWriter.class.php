<?php

    class CodeWriter 
    {
        private $indent = 1;
        private $output = "";
        private $indentChar = "    ";

        public function addLine(string $statements, bool $indentLines = false) {
            if ($indentLines) {
                $statements = explode(PHP_EOL, $statements);
                foreach ($statements as $statement) {
                    $this->addLine($statement, false);
                }
            } else {
                if (strlen($this->output) > 0) {
                    $this->output .= PHP_EOL;
                }
                
                $this->output .= $this->getIndent() . $statements;
            }
        }

        public function addClass(string $name, string $base, $interfaces = []) {
            $line = "class $name";
            if (strlen($base) > 0) {
                $line .= " extends $base";
            }

            $intefaces = implode(", ", $interfaces);
            if (strlen($intefaces) > 0) {
                $line .= " implements $intefaces";
            }

            $line .= " {";
            $this->addLine($line);
            $this->addIndent();
        }

        public function addMethod($name, $modifier = "public", $arguments = []) {
            $arguments = array_map(function($argument) { return $this->getArgument($argument); }, $arguments);
            $argumentsString = implode(", ", $arguments);

            $line = "$modifier function $name($argumentsString) {";
            $this->addLine($line);
            $this->addIndent();
        }
        
        public function addTry() {
            $this->addLine("try {");
            $this->addIndent();
        }

        public function addCatch($exception) {
            $exception = $this->getArgument($exception);

            $this->removeIndent();
            $this->addLine("} catch($exception) {");
            $this->addIndent();
        }

        public function addFinally() {
            $this->removeIndent();
            $this->addLine("} finally {");
            $this->addIndent();
        }
        
        public function closeBlock() {
            $this->removeIndent();
            $this->addLine("}");
        }
        
        public function addIndent($size = 1) {
            $this->indent += $size;
        }
        
        public function removeIndent($size = 1) {
            $this->indent -= $size;
        }

        private function getArgument($argument) {
            if (is_array($argument) && count($argument) >= 2) {
                $result = $argument[0];
                if (strlen($result) > 0) {
                    $result .= " ";
                }

                $result .= '$' . $argument[1];
                if (count($argument) == 3) {
                    $result .= ' = ' . $argument[2];
                }

                return $result;
            }

            return '$' . $argument;
        }

        private function getIndent() {
            return str_repeat($this->indentChar, $this->indent);
        }

        public function __toString() {
            return $this->toString();
        }

        public function toString() {
            return $this->output;
        }

        public function writeToFile($filePath) {
            $content = $this->toString();
            $content = "<?php" . PHP_EOL . PHP_EOL . $content . PHP_EOL . PHP_EOL . "?>";
            file_put_contents($filePath, $content);
        }
    }

?>