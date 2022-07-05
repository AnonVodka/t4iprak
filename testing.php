<?php
require("calc/calculator.php");
require("calc/old_calc.php");
$str = "89345-32958-41334*1007*92015*94663*64927+93042-45382-46382+86170/62711/4878/3701636252";

$start = hrtime(true);
$calc = new Calculator();
$res = $calc->calculate($str);
echo (hrtime(true) - $start) / 1e+6;

echo "\n";

$start = hrtime(true);
$calc = new OldCalculator();
$res = $calc->calculate($str);
echo (hrtime(true) - $start) / 1e+6;

// var_dump($res);
// var_dump($fCalc);
// var_dump($ooe);
// var_dump($calc->backupChars);


?>
