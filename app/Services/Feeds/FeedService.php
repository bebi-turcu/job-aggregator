<?php

declare(strict_types=1);

namespace App\Services\Feeds;

use App\Contracts\FeedClientContract;
use App\DataObjects\JobData;
use App\Models\Eloquent\Feed;
use App\Models\Eloquent\Job;
use App\Services\Feeds\Resources\JobResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class FeedService implements FeedClientContract
{
    /**
     * Get all feeds from the DB
     * @return Collection
     */
    public function feeds(): Collection
    {
        return Feed::get();
    }

    /**
     * Get all jobs read from given feed
     * @param Feed $feed
     * @return Collection
     */
    public function jobs(Feed $feed): Collection
    {
        $jobs = new JobResource($feed);

        return $jobs->list();
    }

    /**
     * Import job data into table `jobs`
     * @param JobData $jobData
     */
    public function importJob(JobData $jobData): void
    {
        $job = Job::withTrashed()
            ->where('title', '=', $jobData->title)
            ->where('description', '=', $jobData->description)
            ->where('company', '=', $jobData->company)
            ->first();

        $jobData = (array) $jobData;
        $jobData['posted_on'] = (new Carbon($jobData['posted_on']))->format('Y-m-d');

        // Is this job not present in the DB? insert it!
        if (! $job) {
            Job::insert($jobData);
        }
        // Update job if it was soft-deleted or it has a lower/equal CPC value than the one from feed
        elseif ($job->deleted_at || $job->cpc <= $jobData['cpc']) {
            $job->update($jobData);
            $job->updateTimestamps()->restore();
        }
    }

    /**
     * Mark as "deleted" all jobs left not updated after processing all feeds
     * @param int $unixTime just before processing feeds
     */
    public function deleteExpiredJobs(int $unixTime): void
    {
        Job::whereRaw("UNIX_TIMESTAMP(updated_at) < {$unixTime}")->delete();
    }
}
