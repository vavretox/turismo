<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class RouteReferenceTest extends TestCase
{
    public function test_views_do_not_reference_removed_province_route(): void
    {
        $home = file_get_contents(dirname(__DIR__, 2).'/resources/views/home.blade.php');

        $this->assertStringNotContainsString("route('provincias.show'", $home);
    }
}
