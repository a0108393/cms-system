<?php

class OrderShell extends Shell {

        function main() // main needs to define
        {
                $option = !empty($this->args[0]) ? $this->args[0] : '';
                echo 'Cron started without any issue.';

                switch ($option)
                {
                        case 'first':
                        echo "First Method called at" .date('M d, Y h:i:s T')." \n";
                        break;
                        case 'second':
                        echo "Second Method called at" .date('M d, Y h:i:s T')." \n";
                        break;
                        default:
                        echo 'No Parameters passed .';
                }
        }
}