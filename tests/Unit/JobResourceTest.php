<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Eloquent\Feed;
use App\Services\Feeds\Resources\JobResource;
use DG\BypassFinals;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class JobResourceTest extends TestCase
{
    protected MockObject $mock;

    // Simulate reading a valid job from feed and test job properties
    public function testReadJobFromFeed(): void
    {
        $this->mock->method('getResponseBody')->willReturn(
            "id,job_title,job_description,company,posted_date,cpc_value\n" .
            "111,\"Job 1 title\",\"Description with\nnewlines\",Company 1,2022-08-23,1006\n"
        );

        $jobs = $this->mock->list();
        $this->assertCount(1, $jobs);

        $job = $jobs->first();
        $this->assertEquals('Job 1 title', $job->title);
        $this->assertEquals("Description with\nnewlines", $job->description);
        $this->assertEquals('Company 1', $job->company);
        $this->assertEquals('2022-08-23', $job->posted_on);
        $this->assertEquals(1006, $job->cpc);
        $this->assertEquals(1, $job->partner_id);
    }

    // Simulate discarding a job with a required field empty (job_description)
    public function testDiscardJobWithFieldEmpty(): void
    {
        $this->mock->method('getResponseBody')->willReturn(
            "job_title,job_description,company,posted_date,cpc_value\n" .
            "\"Job 1 title\",,Company 1,2022-08-23,1006\n"
        );

        $jobs = $this->mock->list();
        $this->assertCount(0, $jobs);
    }

    // Simulate invalid/empty feed response and test exception thrown
    public function testInvalidFeed(): void
    {
        $this->expectException(\Exception::class);

        // Some required columns missing
        $this->mock->method('getResponseBody')->willReturn("job_title,missing_required_columns\n");
        $this->mock->list();

        // Empty response
        $this->mock->method('getResponseBody')->willReturn('');
        $this->mock->list();
    }

    // Enable mocking method getResponseBody() in final class JobResource()
    protected function setUp(): void
    {
        BypassFinals::enable();

        $feed = new Feed();
        $feed->partner_id = 1;

        $this->mock = $this->getMockBuilder(JobResource::class)
            ->setConstructorArgs([$feed])
            ->onlyMethods(['getResponseBody'])
            ->getMock();
    }
}
