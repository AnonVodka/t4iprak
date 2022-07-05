<?php
    /**
     * Description: Simpler Taschrechner der die 4 Grunderechenarten beherrscht, sowei alle
     *              mathematischen Gesetzte(Punkt vor Strict, Klammern zu erst)
     *              und vergangene Rechenoperationen ausgibt
     * Sprachen:    PHP, HTML, CSS, KEIN JAVASCRIPT
     */

    require("calculator.php");

    $calc = new Calculator();

    const COOKIE_NAME = "calc_history";
    const COOKIE_EXPIRE = (30 * 24 * 60 * 60); // 30 Days
    $history = array();

    if (isset($_COOKIE[COOKIE_NAME]) === false)  {
        // create the history cookie if it doesn't exist already
        // set it to expire in 30 days from now (30 days * 24 hours * 60 minutes * 60 seconds)
        // set the default value to an empty json table
       
        setcookie(COOKIE_NAME, base64_encode(json_encode([])), time() + COOKIE_EXPIRE);
    }
    else {
        // if the cookie already exists, decode it and store it in a variable
        $history = json_decode(base64_decode($_COOKIE[COOKIE_NAME]), true);
    }

    if (isset($_POST["clear"]) === true) {
        // clear the history
        setcookie(COOKIE_NAME, base64_encode(json_encode([])), time() + COOKIE_EXPIRE);
        $history = [];
    }

?>

<html>
    <head>
        <title>Taschenrechner</title>
        <link rel="stylesheet" href="../static/main.css"/>
    </head>
    <body>
        <div class="flex">
            <div class="calc">
                <h2>Taschenrechner</h2>
                <div class="vsep"></div>
                <div id="calc">
                    <form method="post">
                        <input type="text" name="calculation" placeholder="1+2*3" required/><br/>
                        <button type="submit">Calculate</button>
                    </form>
                    <div id="result">
                        <?php
                            if (isset($_POST["calculation"])) {
                                $result = null;
                                $calculation = $_POST["calculation"];

                                if (array_key_exists($calculation, $history)) {
                                    // this calculation was already calculated
                                    // so get the result from the history
                                    $result = $history[$calculation];
                                }
                                else { 
                                    // this is a new calculation
                                    $result = $calc->calculate($calculation);

                                    // add it to the history
                                    $history[$calculation] = $result;
                                    setcookie(COOKIE_NAME, base64_encode(json_encode($history)), time() + COOKIE_EXPIRE);

                                    $calculation = $calc->get_calculation();
                                }
                                echo $calculation . " = " . $result;
                            }
                        ?>
                    </div>
                    
                    <div class="vsep" style="margin-top: 15px;"></div>
                    <h4 style="margin: 5;">Order of execution</h4>
                    <div class="vsep"></div>
                    <div class="flex" style="height: 50%;">
                        <ol id="ooe">
                            <?php
                                $ooe = $calc->get_order_of_exection();
                                for ($i = 0; $i < count($ooe); $i++) {
                                    echo "<li>" . $ooe[$i] . "</li>";                                    
                                }
                            ?>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="hsep"></div>
            <div class="history">
                <h2>History</h2>
                <div class="vsep"></div>
                <div id="history">
                    <?php
                        if (!empty($history) && is_array($history)) {
                            // only loop if $history is an array
                            $i=0;
                            foreach ($history as $k => $v) {
                                echo sprintf("<div class='history-entry %s'>%s = %s</div>", ($i++ > 0) ? " brd" : "", $k, $v);
                            }
                        }
                    ?>
                </div>
                <form method="post">
                    <button type="submit" name="clear">Clear</button>
                </form>
            </div>
        </div>
    </body>
</html>