<?php

use App\Models\Blog;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


it('should login successfully ', function () {

    $user = User::factory()->create();
    $response = $this->postJson('/api/auth/login',[
        'email' => $user->email,
        'password' => 'password'
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'user' => ['id', 'name','email'], 'token'
    ]);
});


it('login should failed ', function ($email, $password) {

    $user = User::factory()->create();

    $response = $this->postJson('/api/auth/login',[
        'email' => $email ?? $user->email,
        'password' => $password ?? fake()->password
    ]);

    $response->assertStatus(422);
})->with([
    "Bad password" => ['email' => null, 'password' => fake()->password],
    "Bad Email" =>  ['email' => fake()->safeEmail(), 'password' => null],
]);

it('should register a new user  ', function () {

    $data  = [
        'name' => fake()->name,
        'email' => fake()->safeEmail,
        'password' => fake()->password
    ];

    $response = $this->postJson('/api/auth/register', $data);

    $response->assertStatus(200);

    $response->assertJsonStructure([
        'user' => ['id', 'name','email'], 'token'
    ]);
});


it('should failed when email exists', function () {
    $user = User::factory()->create();

    $data  = [
        'name' => fake()->name,
        'email' => $user->email,
        'password' => fake()->password
    ];

    $response = $this->postJson('/api/auth/register', $data);

    $response->assertStatus(422);
});
