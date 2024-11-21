<?php

namespace App\Console\Commands;

use App\Imports\ProjectDynamicImport;
use App\Imports\ProjectImport;
use App\Models\Task;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testcomm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test command';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Excel::import(new ProjectDynamicImport(Task::find(1)), '/files/projects2.xlsx', 'public');
        $this->info('The command was successful!');
    }
}
