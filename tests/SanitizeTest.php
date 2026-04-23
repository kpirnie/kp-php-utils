<?php

declare(strict_types=1);

namespace KPT\Tests;

use KPT\Sanitize;
use PHPUnit\Framework\TestCase;

class SanitizeTest extends TestCase
{
    /** @test */
    public function string_strips_tags_and_trims(): void
    {
        $this->assertSame('hello world', Sanitize::string('<b>hello world</b>'));
    }

    /** @test */
    public function email_returns_empty_for_invalid(): void
    {
        $this->assertSame('', Sanitize::email('not-an-email'));
    }

    /** @test */
    public function int_returns_zero_for_invalid(): void
    {
        $this->assertSame(0, Sanitize::int('abc'));
    }
}
