<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TeacherControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test teacher creation form displays correctly.
     */
    public function test_create_form_displays_correctly(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/teachers/create');

        $response->assertStatus(200);
        $response->assertSee('Create New Teacher');
        $response->assertSee('Name');
        $response->assertSee('Email Address');
        $response->assertSee('Sex');
        $response->assertSee('Telephone Number');
        $response->assertDontSee('Password');
    }

    /**
     * Test teacher creation with valid data.
     */
    public function test_teacher_creation_with_valid_data(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $teacherData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'sex' => 'male',
            'tel_no' => '1234567890',
        ];

        $response = $this->actingAs($user)->post('/teachers', $teacherData);

        $response->assertRedirect('/teachers');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'role' => 'teacher',
        ]);
    }

    /**
     * Test teacher creation with invalid data.
     */
    public function test_teacher_creation_with_invalid_data(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $invalidData = [
            'name' => '',
            'email' => 'invalid-email',
            'sex' => 'invalid',
            'tel_no' => '',
        ];

        $response = $this->actingAs($user)->post('/teachers', $invalidData);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['name', 'email']);
    }

    /**
     * Test teacher index page displays teachers.
     */
    public function test_teacher_index_displays_teachers(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher']);

        $response = $this->actingAs($user)->get('/teachers');

        $response->assertStatus(200);
        $response->assertSee($teacher->name);
    }
}
