<?php

namespace Modules\Auth\Tests\Unit;

use Modules\Auth\Services\LoginFieldDetector;
use Tests\TestCase;

class LoginFieldDetectorTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_it_detects_username_correctly(): void
    {
        $detector = new LoginFieldDetector;
        $result = $detector->detect('johndoe');
        $this->assertEquals('username', $result);
    }

    public function test_it_detects_phone_correctly(): void
    {
        $detector = new LoginFieldDetector;
        $result = $detector->detect('09123456789');
        $this->assertEquals('phone', $result);
    }

    public function test_it_detects_email_correctly(): void
    {
        $detector = new LoginFieldDetector;
        $result = $detector->detect('john@example.com');
        $this->assertEquals('email', $result);
    }
}
