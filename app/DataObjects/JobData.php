<?php

declare(strict_types=1);

namespace App\DataObjects;

final class JobData
{
    public function __construct(
        public string $title,
        public string $description,
        public string $company,
        public string $posted_on,
        public int $cpc,
        public int $partner_id
    ) {}
}
