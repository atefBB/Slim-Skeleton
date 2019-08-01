<?php
namespace Queue\Jobs;

class MyJob
{
    public function perform()
    {
        // Work work work
        echo $this->args['name'];
    }
}
