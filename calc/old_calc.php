<?php
    /**
     * Description: Calculator "brain"
     * Order of operations: 
     *  P:  Parentheses
     *  E:  Exponents
     *  MD: Multiplication Division
     *  AS: Addition Subtraction
     */

    class OldCalculator {

        private array $ALLOWED_CHARS = array("+", "-", "*", "/", "(", ")", "e");

        private string $calculation; // the main calculation
        private array $chars; // the calculation split into an array
        public array $backupChars; // backup of $chars, used in get_calculation
        private array $oOE; // order of execution

        function __construct() {
            $this->calculation = "";
            $this->chars = array();
            $this->backupChars = array();
            $this->oOE = array();
        }

        function __destruct() {
            unset($this->calculation);
            unset($this->chars);
            unset($this->backupChars);
            unset($this->oOE);
        }

        // strips out any unwanted characters
        private function strip() : void {
            $this->calculation = str_replace("E", "e", $this->calculation);
            foreach(str_split($this->calculation) as $s) {
                if (!is_numeric($s) && !in_array($s, $this->ALLOWED_CHARS)) {
                    $this->calculation = str_replace($s, "", $this->calculation);
                }
            }
        }

        // splits our calculation string into an array

        private function split() : void {
            $cur = ""; // current char in the calculation
            $lwo = false; // last character was an operator
            $shouldInvert = false; // should we invert the next number

            // we are using str_split here
            // because we want to split the string
            // after each character, explode cant do that
            
            // we could also use preg_split to perfectly get the numbers
            // however, it would mean that our operators would get removed,
            // since we would use them as the delimiter, but we need them
            $iterations = 0;
            // https://github.com/wftutorials/php-mini-projects/blob/main/mycalculator/calculator.php#L18-L34
            $tmpArray = str_split($this->calculation);
            
            // set $a to the first value 
            // as long as the key of the current internal pointer isnt null
            // we use $a and set its value to the next value of the array
            for ($a = current($tmpArray); key($tmpArray) !== null; $a = next($tmpArray)) {

                if (is_numeric($a) || $a == ".") {
                    // we have a number or the decimal point of a number
                    // so concatenate to cur

                    $cur .= $a;

                    $lwo = false;
                }
                else if (!is_numeric($a)) {
                    if ($iterations == 0 && $a !== "(") {
                        // if we are on the first iteration and found an operator
                        // we add a 0 infront of the operator
                        // and continue execution like normal
                        $this->chars[] = 0;
                    }

                    if ($lwo === true) {
                        // an operator followed another operator
                        // if the operator is a minus, that means that the following number 
                        // needs to be inverted
                        // if it isnt a minus, we simply ignore it
                        if ($a === "-" && end($this->chars) !== "e") {
                            $shouldInvert = true;
                        }

                        // if previous and current char are both *, that means we have an exponent calculation
                        else if ($a === "*" && end($this->chars) === "*") {
                            // set cur to exponent operator
                            $a = "**";
                            // and delete the last entry in the erray
                            array_splice($this->chars, array_key_last($this->chars));
                        }
                        else if (end($this->chars) === "e" && ($a === "+" || $a === "-")) {
                            // last one was an e and current is either + or -
                            
                            // remove the number and the e from the array and save them to $tmp
                            $tmp = join("", array_splice($this->chars, array_key_last($this->chars) - 1, 2));

                            // use the removed values, the current operator and the current(next) value of the array
                            // as the e notation and cast it to a float to get the actual number
                            $this->chars[] = (float)($tmp . $a . current($tmpArray));

                            // advance the internal array pointer by 1 again to skip the number after the operator
                            next($tmpArray);
                            $lwo = false;
                            continue;
                        }
                        else if ($a !== "(" && $a !== ")") {
                            continue;
                        }
                        
                        $lwo = false;
                    }

                    // if $cur wasnt cleared previously, that means it must contain a number
                    // so append that number to the array, clear $cur
                    // and append our current character to the array as well
                    if (!empty($cur) || $cur === "0") {

                        if ($shouldInvert) {
                            $cur *= -1; // $cur = -$cur; works here too, i prefer *= -1 though
                            $shouldInvert = false;
                        }

                        $this->chars[] = (float)$cur;
                        $cur = "";
                    }

                    if (!$shouldInvert) {
                        $this->chars[] = $a;
                        if ($a !== "(" && $a !== ")")
                            $lwo = true;
                    }
                }

                // increase iterations
                $iterations++;
            } 
            // add the last number to the array
            if (!empty($cur) || $cur === "0") {

                if ($shouldInvert) {
                    $cur *= -1;
                    $shouldInvert = false;
                }
                $this->chars[] = (float)$cur;
            }
        }

        private function handle_parentheses(array &$chars = null, $add = true) : void {
            while (true) {
                $reset = false;
                $oP = -1;
                $cP = -1;

                foreach ($chars as $k => $v) {
                    // if our current character is an opening parentheses
                    // and we havent found a closing parentheses
                    // we save its key
                    if ($v === "(" && $cP === -1)
                        $oP = $k;
                    
                    // if our $v is an closing parentheses
                    // save its key
                    else if ($v === ")")
                        $cP = $k;
    
                    // if we found both, an opening parentheses and a closing parentheses
                    // we calculate everything thats in between them
                    if ($oP !== -1 && $cP !== -1) {
                        // slice of everything from the opening parentheses 
                        // to the closing parentheses
                        // since array_slices takes the amount of items to slice
                        // we subtract the index of the opening parentheses 
                        // from the closing parentheses to get the length
                        // relative to the index of the closing parentheses
                        // subtract 1 from the length to get the last number
                        $tmp = array_slice($chars, $oP + 1, ($cP - $oP) - 1);

                        // this should return the result of the calculation
                        // inside the parentheses
                        $result = $this->do($tmp, false);

                        // add to order of execution
                        if ($add) {
                            $this->oOE[] = join(" ", array(join(" ", array_slice($chars, $oP, $cP - $oP + 1)), "=", $result));
                        }

                        // remove everything from the array 
                        // that was inside the parentheses
                        // replace the last char with the result
    
                        for ($i = 0; $i < ($cP - $oP); $i++)
                            array_splice($chars, $oP, 1);
    
                        $chars[$oP] = $result;

                        // break out of the foreach loop
                        // and restart the iteration with the "new" array
                        $reset = true;
                        break;
                    }
                }

                if (!$reset)
                    break;
            }
        }

        private function handle_multiply_divide(array &$chars = null, $add = true) : void {

            // calculate exponents first    
            $exponent = array_search("**", $chars);

            while ($exponent != false) {
                // get number before and after exponent
                $f = $chars[$exponent - 1];
                $s = $chars[$exponent + 1];

                if (!is_numeric($s) || !is_numeric($f))
                    continue; // skip when any of the 2 vars are not numbers

                // calculate potency and replace operator with it
                $chars[$exponent] = $f ** $s;

                // add to order of execution
                if ($add)
                    $this->oOE[] = join(" ", array($f, "**", $s));

                // remove both numers
                array_splice($chars, $exponent + 1, 1);
                array_splice($chars, $exponent - 1, 1);

                $exponent = array_search("**", $chars);
            }


            while (true) {
                $reset = false;

                foreach ($chars as $k => $v) {

                    if ($v === "*") {
                        // get numbers before and after operator
                        $f = $chars[$k - 1];
                        $s = $chars[$k + 1];
    
                        if (!is_numeric($s) || !is_numeric($f))
                            continue; // skip when any of the 2 vars are not numbers
    
                        $chars[$k] = $f * $s; // replace the operator with the product

                        // add to order of execution
                        if ($add)
                            $this->oOE[] = join(" ", array($f, "*", $s, "=", $chars[$k]));
                        
                        // remove the 2 numbers
                        array_splice($chars, $k + 1, 1); 
                        array_splice($chars, $k - 1, 1);

                        $reset = true;
                        break;
                    }
                    else if ($v === "/") {
                        // get numbers before and after operator
                        $f = $chars[$k - 1];
                        $s = $chars[$k + 1];
    
                        if (!is_numeric($s) || !is_numeric($f))
                            continue; // skip when any of the 2 vars are not numbers

                        $chars[$k] = $f / $s;
    
                        // add to order of execution
                        if ($add)
                            $this->oOE[] = join(" ", array($f, "/", $s , "=", $chars[$k]));
                        
                        // remove the 2 numbers
                        array_splice($chars, $k + 1, 1); 
                        array_splice($chars, $k - 1, 1);
                        
                        $reset = true;
                        break;
                    }
                }

                if (!$reset)
                    break;
            }
        }

        private function do(array $charsOverride = null, $add = true) {

            $chars = null;
            if ($charsOverride !== null) {
                $chars = &$charsOverride;
            }
            else {
                $chars = &$this->chars;
            }

            $this->handle_parentheses($chars, $add);
            $this->handle_multiply_divide($chars, $add);

            // there is only 1 element left in the array
            // so just return it
            if (count($this->chars) == 1)
                return $this->chars[0];

            $sum = array_values(preg_grep("/[0-9]/", $chars))[0]; // set sum to the first number
            $operator = "";

            // loop through chars
            foreach ($chars as $a) {
                if (is_numeric($a) && $operator) {
                    // $a is a number and we previously had an operator

                    $tmp = 0;
                    if ($operator == "+") {
                        $tmp = $sum + $a;
                    }
                    else if ($operator == "-") {
                        $tmp = $sum - $a;
                    }

                    if ($add)
                        $this->oOE[] = join(" ", array($sum, $operator, $a, "=", $tmp));
                    
                    $sum = $tmp;

                    // reset operator, just in case something messes up 
                    // and we have 2 numbers following each other
                    // which shouldnt happen
                    $operator = "";
                }
                else {
                    // operator(+ - * /)
                    if ($a !== "(" || $a !== ")") {                   
                        $operator = $a;
                    }
                }
            }

            return $sum;
        }

        public function calculate(string $calculation) : string {

            $res = 0.0;

            if (strlen($calculation) == 0)
                return $res;

            $this->calculation = $calculation;

            // we found a division by 0, which is always undefined
            if (strstr($this->calculation, "/0") !== false)
                return "undefined (division by 0)";

            if ($this->calculation[-1] == "/") 
                return "undefined (division by 0)";

            // strip any unwanted characters
            $this->strip();

            // split our calculation string to an array container our numbers and operators
            $this->split();

            // set backupChars to chars 
            // so we can use it in get_calculation later
            $this->backupChars = $this->chars;

            if (strlen($this->calculation) == 0 || count($this->chars) == 0)
                return $res;

            // do the calculations with the given array
            $res = $this->do();

            // clean up
            unset($this->calculation);
            unset($this->chars);

            return $res;
        }


        // get order of execution 
        public function get_order_of_exection() : array {
            return $this->oOE;
        }

        // get the calculation as string from array
        public function get_calculation($sep = " ")
        {
            // backup char has no value yet, meaning we didnt get past the splitting/parsing
            // possible due to division by 0
            if (count($this->backupChars) == 0)
                return $this->calculation;

            return join($sep, $this->backupChars);
        }

        // get the raw calculation string
        public function get_raw_calculation() {
            return $this->calculation;
        }
    }
?>
