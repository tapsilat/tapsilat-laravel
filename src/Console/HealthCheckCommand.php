<?php

namespace Tapsilat\Laravel\Console;

use Illuminate\Console\Command;
use Tapsilat\APIException;
use Tapsilat\Laravel\Facades\Tapsilat;

class HealthCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tapsilat:health';

    /**
     * The console command description.
     */
    protected $description = 'Check the health of the Tapsilat API connection';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking Tapsilat API health...');

        try {
            $response = Tapsilat::healthCheck();

            $this->newLine();
            $this->info('✓ Tapsilat API is healthy!');
            $this->newLine();

            $this->table(
                ['Key', 'Value'],
                collect($response)->map(fn ($value, $key) => [$key, is_array($value) ? json_encode($value) : $value])->values()->toArray()
            );

            return Command::SUCCESS;
        } catch (APIException $e) {
            $this->newLine();
            $this->error('✗ Tapsilat API health check failed!');
            $this->newLine();

            $this->table(
                ['Error Type', 'Value'],
                [
                    ['Status Code', $e->statusCode],
                    ['Code', $e->code],
                    ['Error', $e->error],
                ]
            );

            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error('✗ Connection failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
