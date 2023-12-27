<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\Services\UserTestService;
use Tests\TestCase;

class UserUpdateTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_user_can_update_own_profile_with_correct_credentials(): void
    {
        $user = User::factory()->create();
        $updatedUserData = User::factory()->make();

        Sanctum::actingAs($user);

        $response = $this->updateUser($updatedUserData, $user->id);

        $response
            ->assertOk()
            ->assertJsonFragment(['first_name' => $updatedUserData->first_name])
            ->assertJsonFragment(['last_name' => $updatedUserData->last_name])
            ->assertJsonFragment(['email' => $updatedUserData->email]);
    }

    public function test_user_cannot_update_own_profile_with_incorrect_credentials(): void
    {
        $user = User::factory()->create();
        $incorrectUserData = User::factory()->create(['first_name' => '']);

        Sanctum::actingAs($user);

        $response = $this->updateUser($incorrectUserData, $user->id);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor('first_name')
            ->assertJsonMissingPath('user');
    }

    public function test_user_cannot_update_another_persons_profile(): void 
    {
        $user = User::factory()->create();
        $updatedUserData = User::factory()->make();
        $profileOwner = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->updateUser($updatedUserData, $profileOwner->id);
        
        $response
            ->assertForbidden()
            ->assertJsonMissingPath('user');
    }
    
    public function test_user_can_store_avatar(): void
    {
        $user = User::factory()->create(['avatar_path' => null]);
        Storage::fake('avatars');
        Sanctum::actingAs($user);
        
        $avatarName = 'avatar.jpg';
        $avatar = UploadedFile::fake()->image($avatarName);

        $response = $this->postJson(route('users.files.store'), [
            'files' => [$avatar]
        ]);
        
        $response->assertCreated();
        $this->assertNotEmpty($response->json('files_links.0'));
        Storage::disk('avatars')->assertExists($avatar->hashName());
    }
}
