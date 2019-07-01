<?php
namespace app\components\job;

class TestJob
{
    public function setUp()
    {
        # Set up environment for this job
    }

    public function perform()
    {

        echo "Hello World testJob";
        # Run task
    }

    public function tearDown()
    {
        # Remove environment for this job
    }
}