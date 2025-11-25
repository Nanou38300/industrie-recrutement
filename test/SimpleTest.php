<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class SimpleTest extends TestCase
{
    public function testAdditionFonctionne(): void
    {
        $this->assertSame(2, 1 + 1);
    }
}
