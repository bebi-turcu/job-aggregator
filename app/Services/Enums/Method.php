<?php

declare(strict_types=1);

namespace App\Services\Enums;

class Method
{
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const PATCH = 'PATCH';
    const DELETE = 'DELETE';

    const OPTIONS = 'OPTIONS';
    const HEAD = 'HEAD';
    const TRACE = 'TRACE';
}
