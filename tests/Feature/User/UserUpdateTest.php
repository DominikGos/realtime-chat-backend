<?php

namespace Tests\Feature\User;

use App\Events\UserUpdated;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
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
    
    public function test_user_can_store_avatar_and_assign_it_to_his_profile(): void
    {
        $user = User::factory()->create(['avatar_path' => null]);
        Storage::fake('avatars');
        Sanctum::actingAs($user);
        
        $avatarName = 'avatar.jpg';
        $avatarLink = '';
        $avatar = UploadedFile::fake()->image($avatarName);

        $response = $this->postJson(route('users.files.store'), [
            'files' => [$avatar]
        ]);
        
        $response->assertCreated();
        $avatarLink = $response->json('files_links.0');
        $this->assertNotEmpty($avatarLink);
        Storage::disk('avatars')->assertExists($avatar->hashName());

        $response = $this->updateUser($user, $user->id, $avatarLink);

        $this->assertStringContainsString($avatarLink, $response->json('user.avatar_link'));
    }

    public function test_UserUpdated_event_fires_when_user_provides_correct_credentials(): void {
        Event::fake();

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->updateUser($user, $user->id);
        
        $response->assertJsonMissingValidationErrors();
        Event::assertDispatched(UserUpdated::class);
    }

    public function test_UserUpdated_event_is_not_fired_when_user_provides_incorrect_credentials(): void {
        Event::fake();

        $incorrectName = '';
        $user = User::factory()->create(['first_name' => $incorrectName]);

        Sanctum::actingAs($user);

        $response = $this->updateUser($user, $user->id);
        
        $response->assertJsonValidationErrorFor('first_name');
        Event::assertNotDispatched(UserUpdated::class);
    }
}
