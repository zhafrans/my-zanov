<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'migrate old database';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting data setup...');

        $steps = [
            'Migrate Fresh (1/4)',
            'Seeding Database (2/4)',
            'Migrate Old Database (3/4)',
            'Migrate Old Database (4/4)',
        ];

        foreach ($steps as $step) {
            $this->info("\nExecuting: {$step}...");
            
            switch ($step) {
                case $steps[0]:
                    $this->call('migrate:fresh');
                    break;
                case $steps[1]:
                    $this->call('db:seed');
                    break;
                case $steps[2]:
                    $this->call('app:migrate-old');
                    break;
                case $steps[3]:
                    $this->call('app:map-product');
                    break;
            }
        }

        $this->newLine();
        $this->info('Data setup completed successfully!');
    }
}
