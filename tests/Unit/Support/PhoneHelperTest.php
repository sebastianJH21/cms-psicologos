<?php

namespace Tests\Unit\Support;

use App\Support\PhoneHelper;
use PHPUnit\Framework\TestCase;

class PhoneHelperTest extends TestCase
{
    public function test_quita_espacios_y_separadores(): void
    {
        $this->assertSame('600112233', PhoneHelper::normalize(' 600 11 22 33 '));
        $this->assertSame('600112233', PhoneHelper::normalize('600-11-22-33'));
        $this->assertSame('600112233', PhoneHelper::normalize('(600) 11.22.33'));
    }

    public function test_conserva_el_prefijo_internacional(): void
    {
        $this->assertSame('+34600112233', PhoneHelper::normalize('+34 600 11 22 33'));
    }

    public function test_devuelve_null_si_no_quedan_digitos(): void
    {
        $this->assertNull(PhoneHelper::normalize(''));
        $this->assertNull(PhoneHelper::normalize('   '));
        $this->assertNull(PhoneHelper::normalize(null));
        $this->assertNull(PhoneHelper::normalize('++--'));
    }
}
