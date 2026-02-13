<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use PHPUnit\Framework\TestCase;

/** Testes do enum UserRole: valores e labels (admin, aluno). */
class UserRoleTest extends TestCase
{
    public function test_admin_has_correct_value_and_label(): void
    {
        $this->assertSame('admin', UserRole::ADMIN->value);
        $this->assertSame('Administrador', UserRole::ADMIN->label());
    }

    public function test_student_has_correct_value_and_label(): void
    {
        $this->assertSame('student', UserRole::STUDENT->value);
        $this->assertSame('Aluno', UserRole::STUDENT->label());
    }
}
