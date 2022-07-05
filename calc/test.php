<?php

require("calculator.php");
require("../static/utils.php");

$calculator = new Calculator();

$calcdCalculations = array();
$evaldCalculations = array();
$correctCalculations = 0;

foreach (Utils::GenerateCalculations(45, 5, Utils::MAX_NUMBERS_PER) as $calc) {
    $calcRes = $calculator->calculate($calc);
    $evalRes = (string)eval("return " . $calc . ";");

    if ($calcRes == $evalRes)
        $correctCalculations++;
    
    $calcdCalculations[] = join("", array("<div class='", $calcRes == $evalRes ? "correct" : "wrong" , "'>", $calc, " = <strong>", $calcRes, "</strong></div>"));
    $evaldCalculations[] = join("", array("<div class='", $calcRes == $evalRes ? "correct" : "wrong" , "'>", $calc, " = <strong>", $evalRes, "</strong></div>"));
}


?>
<html>
    <head>
        <title>Calculator testing</title>
        <link rel="stylesheet" href="../static/main.css" />
        <style>
            .wrong {
                color: red;
                font-size: 0.7em;
                font-weight: bold;
            }
            .correct {
                color: yellowgreen;
                font-size: 0.7em;
            }
        </style>
    </head>
    <body>
        <div class="flex">
            <div class="calc">
                <div style="min-height: 5em;">
                    <h1 style="margin: 0;">Calculated</h1>
                    <small>Calculated using $calc->calculate</small><br/>
                    <?php echo "<small> Correct calculations: " . $correctCalculations . " / " . count($evaldCalculations) . "</small>"?>
                </div>
                <div id="vsep" class="vsep"></div>
                <div style="text-align: left">
                    <?php
                        foreach($calcdCalculations as $a) {
                            echo $a;
                        }
                    ?>
                </div>
            </div>
            <div id="hsep" class="hsep"></div>
            <div class="history">
                <div style="min-height: 5em;">
                    <h1 style="margin: 0;">Expected</h1>
                    <small>Calculated using eval</small><br/>
                    <?php echo "<small> Correct calculations: " . $correctCalculations . " / " . count($evaldCalculations) . "</small>"?>
                </div>
                <div id="vsep" class="vsep"></div>
                <div style="text-align: left">
                    <?php
                        foreach($evaldCalculations as $a) {
                            echo $a;
                        }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>