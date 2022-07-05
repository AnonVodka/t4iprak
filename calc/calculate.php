<?php
    require("calculator.php");

    const COOKIE_NAME = "calc_history";
    const COOKIE_EXPIRE = (30 * 24 * 60 * 60); // 30 Days

    if (!empty($_POST)) {
        $calculator = new Calculator();

        if (isset($_POST["clear"]) === true) {
            setcookie(COOKIE_NAME, base64_encode(json_encode([])), time() + COOKIE_EXPIRE);
        }

        $calc = $_POST["calc"] ?? null;

        if ($calc == null)
            die;

        $history = array();
    
        if (isset($_COOKIE[COOKIE_NAME]) === false)  {
            setcookie(COOKIE_NAME, base64_encode(json_encode([])), time() + COOKIE_EXPIRE);
        }
        else {
            $history = json_decode(base64_decode($_COOKIE[COOKIE_NAME]), true);
        }

        $result = $calculator->calculate($calc);
        $calc = $calculator->get_calculation("");
        $ooe = $calculator->get_order_of_exection();

        $history[$calc] = $result;
        setcookie(COOKIE_NAME, base64_encode(json_encode($history)), time() + COOKIE_EXPIRE);

        header("Content-Type: application/json");
        echo json_encode([
            "result" => $result,
            "ooe" => $ooe,
            "calculation" => $calc
        ]);
    } 
    else {
        die;
    }
?>