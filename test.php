<?php
require './db.php';
require './abstractmodel.php';
require './employees.php';
$emp = Employees::get(
        'SELECT * FROM employees WHERE address = :address',
        array(
            'address' => array(Employees::DATA_TYPE_STR , "giza")
        )
        );

        var_dump($emp);