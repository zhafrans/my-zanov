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
    protected $description = 'Get materials, Odoo products and seed the database';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting data setup...');

        $steps = [
            'Migrate Fresh (1/3)',
            'Seeding Database (2/3)',
            'Migrate Old Database (3/3)',
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
            }
        }

        $this->newLine();
        $this->info('Data setup completed successfully!');
    }
}
