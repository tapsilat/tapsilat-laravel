<?php

namespace Tapsilat\Laravel\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tapsilat:install';

    /**
     * The console command description.
     */
    protected $description = 'Install the Tapsilat Laravel package';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Installing Tapsilat Laravel package...');

        // Publish config
        $this->info('Publishing configuration...');
        $this->callSilently('vendor:publish', [
            '--tag' => 'tapsilat-config',
        ]);
        $this->info('✓ Configuration published.');

        // Add environment variables
        $this->addEnvironmentVariables();

        $this->newLine();
        $this->info('✓ Tapsilat Laravel package installed successfully!');
        $this->newLine();

        $this->comment('Next steps:');
        $this->line('  1. Add your TAPSILAT_API_KEY to your .env file');
        $this->line('  2. Optionally add TAPSILAT_WEBHOOK_SECRET for webhook verification');
        $this->line('  3. Run: php artisan tapsilat:health to verify your connection');

        return Command::SUCCESS;
    }

    /**
     * Add the Tapsilat environment variables to the .env file.
     */
    protected function addEnvironmentVariables(): void
    {
        $envPath = base_path('.env');
        $envExamplePath = base_path('.env.example');

        $variables = [
            '',
            '# Tapsilat Payment Gateway',
            'TAPSILAT_API_KEY=',
            'TAPSILAT_WEBHOOK_SECRET=',
            '# TAPSILAT_BASE_URL=https://panel.tapsilat.dev/api/v1',
            '# TAPSILAT_TIMEOUT=30',
            '# TAPSILAT_DEFAULT_CURRENCY=TRY',
            '# TAPSILAT_DEFAULT_LOCALE=tr',
            '# TAPSILAT_PAYMENT_SUCCESS_URL=',
            '# TAPSILAT_PAYMENT_FAILURE_URL=',
            '# TAPSILAT_LOGGING_ENABLED=false',
        ];

        $content = implode("\n", $variables);

        // Add to .env
        if (File::exists($envPath)) {
            $envContent = File::get($envPath);
            if (!str_contains($envContent, 'TAPSILAT_API_KEY')) {
                File::append($envPath, "\n" . $content);
                $this->info('✓ Environment variables added to .env');
            } else {
                $this->line('  Environment variables already exist in .env');
            }
        }

        // Add to .env.example
        if (File::exists($envExamplePath)) {
            $envExampleContent = File::get($envExamplePath);
            if (!str_contains($envExampleContent, 'TAPSILAT_API_KEY')) {
                File::append($envExamplePath, "\n" . $content);
                $this->info('✓ Environment variables added to .env.example');
            } else {
                $this->line('  Environment variables already exist in .env.example');
            }
        }
    }
}
