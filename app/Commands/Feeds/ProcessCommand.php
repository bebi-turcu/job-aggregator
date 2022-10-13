<?php

declare(strict_types=1);

namespace App\Commands\Feeds;

use App\Contracts\FeedClientContract;
use App\DataObjects\JobData;
use LaravelZero\Framework\Commands\Command;
use Throwable;

final class ProcessCommand extends Command
{
    const TIMEZONE = 'Europe/Bucharest';

    protected $signature = 'feeds:process';

    protected $description = 'Process each feed for importing jobs.';

    public function handle(FeedClientContract $feedService): int
    {
        date_default_timezone_set(self::TIMEZONE);
        $unixTime = time();
        $success = false;

        foreach ($feedService->feeds() as $feed) {
            try {
                $jobs = $feedService->jobs($feed);

                foreach ($jobs as $job) {
                    $feedService->importJob($job);
                }

                $this->info("Imported jobs from feed {$feed->name}");

                $this->table(
                    headers: ['Title', 'Description', 'Company', 'Posted', 'CPC', 'Partner ID'],
                    rows: $jobs->map(fn (JobData $job): array => (array) $job)->toArray(),
                );
                $success = true;
            } catch (Throwable $exception) {
                $this->warn($exception->getMessage());
            }
        }

        if (! $success) {
            $this->error('Failed processing feeds!');
            return ProcessCommand::FAILURE;
        }

        $feedService->deleteExpiredJobs($unixTime);
        return ProcessCommand::SUCCESS;
    }
}
