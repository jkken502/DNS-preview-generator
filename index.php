<?php
//index.php
require_once(dirname(__FILE__) . "/include/functions.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isArrayValid( [$_POST['url'], $_POST['ip'] ] ))
    {
        handleForm();
    }
    else
    {
      echo "Not all fields were filled out. Please try again.";
      displayForm();
    }
  }
  else
  {
    displayForm();
  }