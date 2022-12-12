<?php
function printDataErrors($errors, $key)
{
    $message='Row Number: '.$key;
    foreach ($errors as  $error) {
        echo '<p>'.$error. '</p>';
    }
}
?>