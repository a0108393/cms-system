<?php
// vendors/shells/demo.php
class DemoShell extends Shell {

    function initialize() {
        // empty
    }
		
    function main() {
        $this->out('Demo Script');
        $this->hr();
			
        if (count($this->args) === 0) {
            $filename = $this->in('Please enter the filename:');
        } else {
            $filename = $this->args[0];
        }
        $this->createFile(TMP.$filename, 'Test content');
    }
		
    function help() {
        $this->out('Here comes the help message');
    }
}