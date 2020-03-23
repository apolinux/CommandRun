<?php

use Apolinux\Commandable;

class TestRunClass implements Commandable{
    
    public function run(int $param1, bool $param2, $param3=null, string $param4=''){
        echo "in method ". __METHOD__ .PHP_EOL ;
        echo "receive parameters:  ". PHP_EOL;
        echo "param1:". ($param1). PHP_EOL;
        echo "param2:". ($param2). PHP_EOL;
        echo "param3:". ($param3). PHP_EOL;
        echo "param4:". ($param4). PHP_EOL;
    }
    
    public function processSome($name, $phone, $amount=10){
        echo "name: $name, $phone:$phone, $amount:$amount". PHP_EOL ;
    }
}
