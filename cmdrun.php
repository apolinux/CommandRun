#!/bin/env php
<?php

use \Apolinux\ClassManager\DirectoryClassType;
use \Apolinux\ClassManager\ClassContainer;

require __DIR__ .'/vendor/autoload.php' ;

$container = new ClassContainer ;
$container->add(new DirectoryClassType(__DIR__. '/test/unit/Commands'));

$cmd = new \Apolinux\CommandRun($container);

$cmd->start();

