<?php

use PHPUnit\Framework\TestCase;

class CommandRunTest extends TestCase{
    
    public function testRunCmd(){
        // define enviroment and input arguments
        $argv=['fido', 'TestRunClass', '--param1=555','--param2=true'];
        
        // define command running class
        
        // run command
        ob_start();
        $cmd = new \Apolinux\CommandRun(__DIR__);
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
        $cmd = new \Apolinux\CommandRun(__DIR__);
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString('shows this help', $output);
        $this->assertStringContainsString('Usage:', $output);
        
        $argv=['fido', '--help'];
        
        // define command running class
        
        // run command
        ob_start();
        $cmd = new \Apolinux\CommandRun(__DIR__);
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
        $cmd = new \Apolinux\CommandRun(__DIR__);
        $cmd->start($argv);
        $output = ob_get_clean();
        // validate answer
        $this->assertStringContainsString('classes list', $output);
    }
}
