window.onload = function() {
    let display = document.getElementById("display");
    let ooe = document.getElementById("ooe");
    let result = document.getElementById("result");
    let history = document.getElementById("history");
    let clear_history = document.getElementById("clear-history");

    function getCalculation() {
        return display.value;
    }
    function setCalculation(calculation) {
        display.value = calculation;
    }
    function appendCalculation(calculation) {
        setCalculation(getCalculation() + calculation);
    }

    function handleHistory() {
        
        let cookies = [];
        document.cookie.split(";").forEach(cookie => {
            var [name, value] = cookie.split("=");
            cookies[name] = decodeURIComponent(value);
        })

        let _history = cookies["calc_history"];
        _history = atob(_history);
        _history = JSON.parse(_history);
        history.innerHTML = "";

        for ([k, v] of Object.entries(_history)) {
            let div = document.createElement("div");
            div.className = "history-entry";
            div.innerText = `${k} = ${v}`;
            div.onclick = function() {
                setCalculation(this.innerText.split(" =")[0]);
            }
            history.append(div);
        }
    }

    handleHistory();

    function calculate() {
        var req = new XMLHttpRequest();

        req.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {

                var json = JSON.parse(this.response);

                let _result = json.result;
                let _ooe = json.ooe;
                let _calc = json.calculation;

                if (_ooe.length > 0) {
                    _ooe.forEach(a => {
                        var li = document.createElement("li");
                        li.className = "ooe-entry";
                        li.innerText = a;
                        li.onclick = function() {
                            setCalculation(this.innerText.split(" =")[0]);
                        }
                        ooe.append(li);
                    })
                }

                result.value = _result;
                setCalculation(_calc);
                handleHistory();
            }
        }

        req.open("POST", "calculate.php", true);
        req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        req.send("calc=" + encodeURIComponent(getCalculation()));
    }

    function handleKey(key) {

        if (["+", "-", "*", "/", "."].includes(key)) {
            if (getCalculation() == "0" && key != ".") {
                setCalculation("");
            }
            appendCalculation(key);
        }
        else if (key == "CE") {
            setCalculation("0");
        }
        else if (key == "C") {
            setCalculation(getCalculation().slice(0, -1));
            if (getCalculation().length == 0) {
                setCalculation("0");
            }
        }
        else if (Number.isInteger(parseInt(key))) {
            if (getCalculation() == "0") {
                setCalculation("");
            }
            appendCalculation(key);
        }
        else if (key == "=" || key == "Enter") {
            calculate();
        }
        
    }

    document.onkeydown = function(e) {
        // console.log(e)   
        var key = e.key;

        if (e.ctrlKey && key == "v") {
            // paste from clipboard
            return navigator.clipboard.readText().then(setCalculation);
        }

        if (key == "." || key == ",")
            key = ".";

        if (key == "Backspace") {
            key = "C";
            if (e.ctrlKey)
                key += "E";
        }

        handleKey(key);
    }

    Array.from(document.getElementsByClassName("col")).forEach(elmnt => {
        elmnt.onclick = function() {
            handleKey(this.innerText);
        }
    });

    clear_history.onclick = function() {
        var req = new XMLHttpRequest();

        req.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                handleHistory();
            }
        }

        req.open("POST", "calculate.php", true);
        req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        req.send("clear");
    }
}
