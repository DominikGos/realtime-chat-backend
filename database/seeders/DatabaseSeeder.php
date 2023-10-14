<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        DB::table('chats')->truncate();
        DB::table('user_chats')->truncate();
        DB::table('message_files')->truncate();
        DB::table('messages')->truncate();
        DB::table('unread_messages')->truncate();
        Schema::enableForeignKeyConstraints();

        $users = User::factory()->count(2)->create();

        $chat = Chat::factory()
            ->hasAttached($users[0])
            ->hasAttached($users[1])
            ->has(Message::factory()->for($users[0])->count(5))
            ->create();
    }
}
