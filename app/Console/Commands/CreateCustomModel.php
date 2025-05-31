<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateCustomModel extends Command
{
    protected $signature = 'make:model_c 
        {model : The name of the model}
        {--all : Create all associated files}
        {--c : Create controller}
        {--m : Create migration}
        {--f : Create factory}
        {--s : Create seeder}
        {--p : Create policy}';

    protected $description = 'Create model with custom domain structure';

    public function handle()
    {
        $model = $this->argument('model');
        $options = $this->options();

        // Select domain
        if ($options != []) {
            $domain = $this->choice('Select domain', [
                'default',
                'coupons',
                'payments',
                'trips',
                'users',
                'vehicles'
            ]);

            if ($domain == 'default') {
                $this->call('make:model', [
                    'name' => $model,
                    '--all' => true
                ]);
            } else {

                // Create model directory structure
                $folderName = Str::snake(Str::pluralStudly($model));
                $basePath = database_path("domains/{$domain}/{$folderName}");

                if ($options['all']) {
                    $this->createModel($model);
                    $this->createMigration($model, $domain, $folderName);
                    $this->createFactory($model, $domain, $folderName);
                    $this->createSeeder($model, $domain, $folderName);
                    $this->createController($model);
                } else {
                    $this->call('make:model', [
                        'name' => $model,
                    ]);
                    if ($options['m']) $this->createMigration($model, $domain, $folderName);
                    if ($options['f']) $this->createFactory($model, $domain, $folderName);
                    if ($options['s']) $this->createSeeder($model, $domain, $folderName);
                    if ($options['c']) $this->createController($model);
                    if ($options['p']) $this->createPolicy($model, $domain, $folderName);
                }
            }
        } else {
            $this->call('make:model', [
                'name' => $model,
            ]);
        }

        $this->info('Operation completed successfully!');
    }

    protected function createDirIfNeeded(string $path): void
    {
        File::ensureDirectoryExists($path);
        // File::makeDirectory($path);
        // if (!File::exists($path)) {
        //     File::makeDirectory($path, 0755, true);
        // }
    }

    protected function createMigration(string $model, string $domain, string $folderName): void
    {
        // $path = database_path("domains/{$domain}/{$folderName}/migrations");
        // $this->createDirIfNeeded($path);


        // $path = database_path("../migrations/domains/{$domain}/{$folderName}");
        // $this->createDirIfNeeded($path);
        $this->call('make:migration', [
            'name' => "\create_.".$folderName."_table",
            '--path' => "database\migrations\domains\\".$domain."\\". $folderName,
            // '--create' => $folderName,
            // '--fullpath' => ("../domains/{$domain}/{$folderName}/")
        ]);
    }

    protected function createFactory(string $model, string $domain, string $folderName): void
    {
        // $path = database_path("../factories/domains/{$domain}/{$folderName}");
        // $this->createDirIfNeeded($path);

        $this->call('make:factory', [
            'name' => "/domains/{$domain}/{$folderName}/{$model}",
            "--model" => "{$model}"
        ]);
    }
    protected function createPolicy(string $model, string $domain, string $folderName): void
    {
        $path = "../domains/{$domain}/{$folderName}/";
        // $this->createDirIfNeeded(("domains/{$domain}/{$folderName}/factories"));

        $this->call('make:policy', [
            'name' => "{$model}Policy",
            '--model' => "App\\Models\\{$model}",
            '--path' => $path
        ]);
    }
    protected function createSeeder(string $model, string $domain, string $folderName): void
    {
        // $path = database_path("../seeders/domains/{$domain}/{$folderName}");
        // $this->createDirIfNeeded($path);

        $this->call('make:seeder', [
            'name' => "/domains/{$domain}/{$folderName}/{$model}",
        ]);
        // $path = database_path("../domains/{$domain}/{$folderName}/seeders");
        // $this->createDirIfNeeded(database_path("domains/{$domain}/{$folderName}/seeders"));

        // $this->call('make:seeder', [
        //     'name' => "{$model}Seeder",
        //     '--path' => $path
        // ]);
    }

    protected function createController(string $model): void
    {
        $this->call('make:controller', [
            'name' => "{$model}Controller",
            '--model' => $model
        ]);
    }

    protected function createModel(string $model): void
    {
        $this->call('make:model', [
            'name' => $model
        ]);
    }
}
