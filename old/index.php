<?php

    require("farm.php");

    $farm = new Farm("Test");
    $farm->AddAnimal("bobby", "Cat", "black");


    echo $farm;     

    // echo "<br/>";
    // foreach ($_SERVER as $k => $v)
    //     echo $k . " => " . $v . "<br/>";

?>