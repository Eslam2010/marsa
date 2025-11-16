<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunTests extends Command
{
    protected $signature = 'test {filter?} {--suite=}';
    protected $description = 'Run PHPUnit tests';

    public function handle()
    {
        $filter = $this->argument('filter');
        $suite = $this->option('suite');

        $command = 'vendor/bin/phpunit';
        
        if ($suite) {
            $command .= " tests/{$suite}";
        } elseif ($filter) {
            $command .= " {$filter}";
        }

        $this->info("Running: {$command}");
        passthru($command);
    }
}
