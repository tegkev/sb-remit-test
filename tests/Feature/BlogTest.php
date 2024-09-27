<?php

use App\Models\Blog;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns the blogs post list with the correct json structure', function () {
    $user = User::factory()->create();
    Blog::factory()->count(10)->create(['created_by_id' => $user->id]);

    $response = $this->get('/api/blogs');
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                "id", "title", "content", "thumbnail_url"
            ]
        ]
    ]);
});


it('Should not run when we are not authenticate', function () {

    $response = $this->postJson('/api/blogs', []);
    $response->assertStatus(401);

    $user = User::factory()->create();
    $blog = Blog::factory()->create(['created_by_id' => $user->id]);

    $response = $this->putJson('/api/blogs/' . $blog->id, []);
    $response->assertStatus(401);

    $response = $this->deleteJson('/api/blogs/' . $blog->id, []);
    $response->assertStatus(401);

});

it('Should  create a blog post', function () {
    $user = User::factory()->create();
    Storage::fake('local');

    $file = UploadedFile::fake()->image('avatar.jpg');

    $data = [
        'title' => fake()->title,
        'content' => fake()->paragraph(),
        'thumbnail' => $file
    ];

    $response = $this->actingAs($user)->post('/api/blogs', $data);

    $response->assertStatus(201);

    $response->assertJsonStructure([
        "id", "title", "content", "thumbnail_url"
    ]);

    $this->assertDatabaseHas('blogs', [
        'title' => $data['title'],
        'content' => $data['content'],
    ]);
});

it('Should  failed to create due to validation', function ($data) {
    $user = User::factory()->create();
    Storage::fake('local');

    $response = $this->actingAs($user)->postJson('/api/blogs', $data);

    $response->assertStatus(422);

})->with([
    "When title null" => fn() => [
        'content' => fake()->paragraph(),
        'file' => UploadedFile::fake()->image('avatar.jpg')
    ],
    "When content null" => fn() => [
        'title' => fake()->title(),
        'file' => UploadedFile::fake()->image('avatar.jpg')
    ],
    "When thumbnail null" => fn() => [
        'title' => fake()->title(),
        'content' => fake()->paragraph(),
    ],
]);


it('Should update an existing Blog', function ($data) {
    $user = User::factory()->create();
    $blog = Blog::factory()->create(['created_by_id' => $user->id]);
    Storage::fake('local');

    $response = $this->actingAs($user)->post('/api/blogs/' . $blog->id, array_merge($data, ['_method' => 'PUT']));

    $response->assertStatus(200);

    $response->assertJsonStructure([
        "id", "title", "content", "thumbnail_url"
    ]);

    foreach ($data as $key => $value) {
        $column = $key === 'thumbnail' ? 'thumbnail_path' : $key;

        $this->assertDatabaseMissing('blogs', [
            $column => $blog->$column,
        ]);

        $this->assertDatabaseHas('blogs', [
            $column => $key === 'thumbnail' ? $value->hashName() : $value
        ]);
    }
})->with(
    [
        "all attributes" => fn() => [
            'title' => fake()->title,
            'content' => fake()->paragraph(),
            'thumbnail' => UploadedFile::fake()->image('avatar.jpg')
        ],
        "Only title and content" => fn() => [
            'title' => fake()->title,
            'content' => fake()->paragraph(),
        ],
        "only title" => fn() => [
            'title' => fake()->title,
        ]
    ]
);

it('it should delete the blog', function () {

    $user = User::factory()->create();
    $blog = Blog::factory()->create(['created_by_id' => $user->id]);

    $response = $this->actingAs($user)->delete('/api/blogs/' . $blog->id);

    $response->assertStatus(200);
    $this->assertDatabaseMissing('blogs', [
        'id' => $blog->id,
    ]);
});
