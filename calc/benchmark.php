<?php
require("calculator.php");
require("old_calc.php");
require("../static/utils.php");


$calculator = new Calculator();
$oldCalc = new OldCalculator();

function get_avrg($arr) {
    return array_sum($arr) / count($arr);
}

?>

<html>
    <head>
        <title>Calculator benchmark</title>
        <style>
            body {
                margin: 5px;
            }
            .rechnung {
                font-size: 0.6em;
                margin-bottom: 5px;
                border-bottom: 1px solid black;
            }
        </style>
    </head>
    <body>
            <?php
                $times = array();
                $otimes = array();
                foreach (Utils::GenerateCalculations(30, 10, 50) as $calc) {

                    $start = hrtime(true);
                    $res = $calculator->calculate($calc);
                    $ms = (hrtime(true) - $start) / 1e+6;
                    $times[] = $ms;
                    echo join(" ", array("<div class='rechnung'>", $calculator->get_calculation(), "=", $res, "<strong>took", $ms, "\rms</strong></div>"));

                    $ostart = hrtime(true);
                    $res = $oldCalc->calculate($calc);
                    $oms = (hrtime(true) - $ostart) / 1e+6;
                    $otimes[] = $oms;
                    echo join(" ", array("<div class='rechnung'>", $oldCalc->get_calculation(),  "=", $res, "<strong>took", $oms, "\rms</strong></div>"));
                }
                printf("<div><strong>New Calculator: total time: %04.5fms (avg: %04.8fms)</strong></div>", array_sum($times), get_avrg($times));
                printf("<div><strong>Old Calculator: total time: %04.5fms (avg: %04.8fms)</strong></div>", array_sum($otimes), get_avrg($otimes));
            ?>
    </body>
</html>