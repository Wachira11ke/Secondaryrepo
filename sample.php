<?php
    $a= 23;
    $nationality = "Dutch";
    //applying conditions on nationality and age
    if ($nationality == "Dutch")
    {
        if ($a >= 18) {
            echo "Eligible to vote";
        }
        else {
            echo "Not eligible to vote";
        }
    }
?>
