<?php

namespace Apolinux\ClassManager;

interface ClassTypeInterface {
    
    public function instance(string $classname) ; // anonymous can't be typed
    public function getList() : Array ;
}
