<?php

class Utils {
    const MAX_GENERATIONS = 30;
    const MAX_NUMBERS_PER = 15;
    const DIGITS = 5;
    const OPERATORS = array("+", "-", "*", "/");

    static function GenerateCalculations($amount = Utils::MAX_GENERATIONS, $digits = Utils::DIGITS, $max_numbers = Utils::MAX_NUMBERS_PER) {
        for ($i = 0; $i < $amount; $i++) {
            $calc = "";
            $length = rand(3, $max_numbers);
            for ($j = 0; $j < $length; $j++) {
        
                $operator = Utils::OPERATORS[rand(0, count(Utils::OPERATORS)-1)];
                $nmbr = rand(0, 10 ** $digits);
        
                if ($j !== $length - 1 && $j !== 0) {
                    if ($operator === "*") {
                        // exponent
                        if (rand(1, 15) == 5) {
                            $calc .= $operator;
                            // override number so that the exponent isnt too high
                            $nmbr = rand(1, 10);
                        }
                    }
                    $calc .= $operator;
                }
                $calc .= $nmbr;
            }
            yield $calc;
        }
    }

    static $alreadyDumped = array();

    static function DumpArray(array $arr, string $app = "") {
        global $alreadyDumped;
        foreach($arr as $k => $v) {
            $type = gettype($v);
            if ($type == "array") {
                
                if (in_array($k, $alreadyDumped)) 
                    continue;   

                echo $app . $k . ":<br>";
                $alreadyDumped[] = $k;
                Utils::DumpArray($v, "&nbsp; " . $app);
            }
            else {
                echo $app . " - " . $k . " => " . $v . "<br/>";
            }
        }   
    }
}






?>