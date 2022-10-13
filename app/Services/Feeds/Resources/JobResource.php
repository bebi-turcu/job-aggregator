<?php

declare(strict_types=1);

namespace App\Services\Feeds\Resources;

use App\Contracts\ResourceContract;
use App\DataObjects\JobData;
use App\Models\Eloquent\Feed;
use App\Services\Concerns\SendsRequests;
use Exception;
use Illuminate\Support\Collection;

final class JobResource implements ResourceContract
{
    use SendsRequests;

    private const REQUIRED_FIELDS = ['job_title', 'job_description', 'company', 'posted_date', 'cpc_value'];

    public function __construct(
        private Feed $feed
    ) {}

    /**
     * Return a collection of JobData objects read from given feed
     */
    public function list(): Collection
    {
        $response = $this->parseCSV($this->getResponseBody());

        return (new Collection($response))->map(
            callback: fn (array $job): JobData =>
                new JobData(
                    title: $job['job_title'],
                    description: $job['job_description'],
                    company: $job['company'],
                    posted_on: $job['posted_date'],
                    cpc: (int) $job['cpc_value'],
                    partner_id: $this->feed->partner_id
                )
        );
    }

    private function parseCSV(string $csv): array
    {
        if (! $csv) {
            throw new Exception('Feed '. $this->feed->name . ' has no content. Skipping.');
        }

        // Using fgetcsv(), as str_getcsv() fails on newlines within fields
        $fp = fopen("php://temp", 'r+');
        fputs($fp, $csv);
        rewind($fp);
        $lines = [];

        while (($line = fgetcsv($fp) ) !== FALSE) {
            $lines[] = $line;
        }
        fclose($fp);

        $headers = array_shift($lines);
        $this->validateHeaders($headers);
        $data = [];

        foreach ($lines as $line) {
            $item = [];

            foreach ($headers as $key => $header) {
                $item[$header] = isset($line[$key]) ? trim($line[$key]) : '';
            }

            // Discard jobs with required fields empty
            if ($item['job_title'] && $item['job_description'] && $item['company']) {
                $data[] = $item;
            }
        }

        return $data;
    }

    private function validateHeaders(array $headers): void
    {
        if (count(array_intersect($headers, self::REQUIRED_FIELDS)) < count(self::REQUIRED_FIELDS)) {
            throw new Exception('Feed '. $this->feed->name . ' does not have all required fields. Skipping.');
        }
    }
}
