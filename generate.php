<html>
    <head>
        <title>Generate calculations</title>
        <style>
            input {
                width: 15%;
            }
        </style>
    </head>
    <body>
        <form method="post">
            <label for="amount">Amount</label>
            <input id="amount" type="number" min="1" max="20" placeholder="Amount to generate" name="amount" required>

            <label for="amount">Max digits per number</label>
            <input id="digits" type="number" min="1" max="10" placeholder="Max digits per number" name="digits" value="5">

            <label for="amount">Max numbers per calculation</label>
            <input id="npc" type="number" min="1" max="15" placeholder="Max numbers per calculation" name="npc" value="5">

            <button type="submit" name="generate">Generate</button>
        </form>
        <?php
            require("static/utils.php");
            if (isset($_POST["generate"]) && isset($_POST["amount"]) && isset($_POST["digits"])) {
                $amount = $_POST["amount"];
                $digits = $_POST["digits"];
                $npc = $_POST["npc"];
                foreach (Utils::GenerateCalculations($amount, $digits, $npc) as $calc) {
                    $res = (string)eval("return " . $calc . ";");
                    echo "<div>" . $calc . " = " . $res . "</div>";
                }
            }
        ?>
    </body>
</html>