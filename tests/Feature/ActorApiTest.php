<?php

namespace Tests\Feature;

use App\Models\Actor;
use App\Services\ActorExtraction\ActorExtractionPrompt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenAI\Contracts\ClientContract;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use Tests\TestCase;

class ActorApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Prevent ApiKeyIsMissing when the OpenAI client is resolved by the container.
        config(['openai.api_key' => 'test']);
    }

    public function test_store_requires_email_and_description(): void
    {
        $this->postJson('/api/actors', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'description']);
    }

    public function test_store_requires_unique_email(): void
    {
        Actor::create([
            'email' => 'a@example.com',
            'description' => 'Some description',
            'description_hash' => hash('sha256', 'Some description'),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'address' => '1 Main St',
        ]);

        $this->postJson('/api/actors', [
            'email' => 'a@example.com',
            'description' => 'Different description',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_store_requires_unique_description(): void
    {
        $original = 'Hello   world';
        $normalized = trim(preg_replace('/\s+/', ' ', $original) ?? $original);

        Actor::create([
            'email' => 'a@example.com',
            'description' => $normalized,
            'description_hash' => hash('sha256', $normalized),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'address' => '1 Main St',
        ]);

        // Same description but with different whitespace should still be considered the same.
        $this->postJson('/api/actors', [
            'email' => 'b@example.com',
            'description' => "Hello     world\n",
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['description']);
    }

    public function test_store_returns_error_when_required_fields_missing_in_description(): void
    {
        $fake = OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'first_name' => null,
                                'last_name' => null,
                                'address' => null,
                                'height_cm' => null,
                                'weight_kg' => null,
                                'gender' => null,
                                'age' => null,
                            ], JSON_THROW_ON_ERROR),
                        ],
                    ],
                ],
            ]),
        ]);
        // Ensure the same fake is used for DI (OpenAiActorExtractor depends on ClientContract).
        $this->app->instance(ClientContract::class, $fake);
        $this->app->instance('openai', $fake);

        $this->postJson('/api/actors', [
            'email' => 'a@example.com',
            'description' => 'Just some text without required fields.',
        ])
            ->assertStatus(422)
            ->assertJson([
                'message' => trans('actors.errors.missing_required_fields'),
                'errors' => [
                    'description' => [trans('actors.errors.missing_required_fields')],
                ],
            ]);
    }

    public function test_store_succeeds_and_persists_actor(): void
    {
        $fake = OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'first_name' => 'Jane',
                                'last_name' => 'Doe',
                                'address' => '221B Baker Street, London',
                                'height_cm' => 170,
                                'weight_kg' => 60,
                                'gender' => 'female',
                                'age' => 28,
                            ], JSON_THROW_ON_ERROR),
                        ],
                    ],
                ],
            ]),
        ]);
        // Ensure the same fake is used for DI (OpenAiActorExtractor depends on ClientContract).
        $this->app->instance(ClientContract::class, $fake);
        $this->app->instance('openai', $fake);

        $res = $this->postJson('/api/actors', [
            'email' => 'jane@example.com',
            'description' => 'Jane Doe, 221B Baker Street, London. 170cm, 60kg.',
        ])->assertStatus(201);

        $this->assertDatabaseHas('actors', [
            'email' => 'jane@example.com',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        $res->assertJsonPath('data.email', 'jane@example.com');
    }

    public function test_index_returns_actors(): void
    {
        Actor::create([
            'email' => 'a@example.com',
            'description' => 'Desc',
            'description_hash' => hash('sha256', 'Desc'),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'address' => '1 Main St',
            'gender' => 'male',
            'height_cm' => 180,
        ]);

        $this->getJson('/api/actors')
            ->assertOk()
            ->assertJsonPath('data.0.first_name', 'John')
            ->assertJsonPath('data.0.address', '1 Main St')
            ->assertJsonPath('data.0.gender', 'male')
            ->assertJsonPath('data.0.height_cm', 180);
    }

    public function test_prompt_validation_endpoint_returns_the_actual_prompt_text(): void
    {
        $res = $this->getJson('/api/actors/prompt-validation')->assertOk();

        $res->assertJson([
            'message' => app(ActorExtractionPrompt::class)->text(),
        ]);
    }
}

