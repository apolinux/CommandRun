<?php

use PHPUnit\Framework\TestCase;

class CommandRunTest extends TestCase{
    
    public function testRunCmd(){
        // define enviroment and input arguments
        $argv=['fido', 'TestRunClass', '--param1=555','--param2=true'];
        
        // run command
        ob_start();
        // define command running class
        $cmd = new \Apolinux\CommandRun(__DIR__ .'/Commands');
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString('receive parameters', $output);
        $this->assertStringContainsString('555', $output);
        $this->assertStringContainsString('param2', $output);
    }
    
    public function testRunCmdHelp(){
        // define enviroment and input arguments
        $argv=['fido', '-h'];
        
        // define command running class
        
        // run command
        ob_start();
        $cmd = new \Apolinux\CommandRun(__DIR__.'/Commands');
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString('shows this help', $output);
        $this->assertStringContainsString('Usage:', $output);
        
        $argv=['fido', '--help'];
        
        // define command running class
        
        // run command
        ob_start();
        $cmd = new \Apolinux\CommandRun(__DIR__.'/Commands');
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString('shows this help', $output);
        $this->assertStringContainsString('Usage:', $output);
    }
    
    public function testRunCmdList(){
        // define enviroment and input arguments
        $argv=['fido', '-l'];
        
        // run command
        ob_start();
        $cmd = new \Apolinux\CommandRun(__DIR__.'/Commands');
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString('classes list', $output);
    }
    
    public function testRunCmdClassHelp(){
        // define enviroment and input arguments
        $argv=['fido', 'TestRunClass', '-h'];
        
        // run command
        ob_start();
        $cmd = new \Apolinux\CommandRun(__DIR__.'/Commands');
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString('method list for class', $output);
        $this->assertStringContainsString('::run [default] ( int $param1', $output);
    }
    
    public function testRunCmdMissingParameter(){
        // define enviroment and input arguments
        $argv=['fido', 'TestRunClass', 'run'];
        
        // run command
        ob_start();
        $cmd = new \Apolinux\CommandRun(__DIR__.'/Commands');
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString("The parameter 'param1' is not defined", $output);
    }
    
    public function testRunCmdListingMethods(){
        // define enviroment and input arguments
        $argv=['fido', 'TestRunClass', '-h'];
        
        // run command
        ob_start();
        $cmd = new \Apolinux\CommandRun(__DIR__.'/Commands');
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString(' * ::run [default] ( int $param1', $output);
    }
    
    public function testRunCmdClassNotValid(){
        // define enviroment and input arguments
        $argv=['fido', 'NotValidClass'];
        
        // run command
        ob_start();
        $cmd = new \Apolinux\CommandRun(__DIR__.'/Commands');
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString("class 'NotValidClass' can not be loaded", $output);
    }
    
    public function testRunCmdMethodNotValid(){
        // define enviroment and input arguments
        $argv=['fido', 'TestRunClass' ,'notvalidmethod'];
        
        // run command
        ob_start();
        $cmd = new \Apolinux\CommandRun(__DIR__.'/Commands');
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString("Method TestRunClass::notvalidmethod() does not exist", $output);
    }
    
    public function testAnonymousClass(){
        $message = "run from anonymous class" ;
        $GLOBALS['message'] = $message ;
        $class = new class {
          public function run(){
              echo $GLOBALS['message'] ;
          }  
        };
        
        ob_start();
        
        // define enviroment and input arguments
        $argv=['fido', 'testanonymous' ,'run'];
        
        $cmd = new \Apolinux\CommandRun();
        $cmd->setAnonymousClasses([
                    'testanonymous' => $class
                ]);
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString($message, $output);
    }
}
