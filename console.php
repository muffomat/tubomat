<?php
    include __DIR__.'/operator.php';

    if(!array_key_exists('command', $_REQUEST)) {
        echo 'Missing param \'command\'';
        die;
    }

    // split commands and args
    $query = mb_strtolower($_REQUEST['command']);
    $parts = preg_split('#\\s#', $query);

    // check length
    if(mb_strlen($query) === 0)
        die;

    // get args
    $plainCommand = array_shift($parts);
    $command = 'exec'.ucfirst($plainCommand);

    // call method
    $operator = new Operator();
    if(!method_exists($operator, $command)) {
        echo "Unknown command '$plainCommand'";
        die;
    }
    call_user_func_array(array($operator, $command), $parts);

?>