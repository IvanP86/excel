<?php

namespace App\Console\Commands;

use App\Imports\ProjectImport;
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
        // dd(11111111111111);
        Excel::import(new ProjectImport(), '/files/projects.xlsx', 'public');
        // return Command::SUCCESS;
    }
}
