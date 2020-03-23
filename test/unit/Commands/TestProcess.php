<?php

class TestProcess {
    
   public function run(){
       echo "I'm here!" . PHP_EOL ;
   }
   
   public function execute($amount, $limit=10){
       echo "amount: $amount, limit:$limit". PHP_EOL ;
   }
}
