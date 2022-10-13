<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DataObjects\JobData;
use App\Models\Eloquent\Feed;
use Illuminate\Support\Collection;

interface FeedClientContract
{
    public function feeds(): Collection;

    public function jobs(Feed $feed): Collection;

    public function importJob(JobData $jobData): void;

    public function deleteExpiredJobs(int $unixTime): void;
}
