<?php
// general url helper fn 

function redirect($whereTo)
{
    header("Location: " . URLROOT . $whereTo);
}