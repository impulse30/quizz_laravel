<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category; // Needed for testing category creation
use App\Models\Question; // Needed for start_quiz tests

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private $creatorUser;
    private $playerUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user with the 'creator' role
        $this->creatorUser = User::factory()->create(['role' => 'creator']);

        // Create a user with the 'player' role
        $this->playerUser = User::factory()->create(['role' => 'player']);
    }

    // Test cases for Category Management (creator routes)

    public function test_player_cannot_access_create_category_route()
    {
        $response = $this->actingAs($this->playerUser)->postJson('/api/categories', [
            'name' => 'Player Category',
            'description' => 'This should not be created.'
        ]);
        $response->assertStatus(403); // Forbidden
    }

    public function test_player_cannot_access_list_categories_route()
    {
        // Assuming /api/categories (index) is creator only based on route group
        $response = $this->actingAs($this->playerUser)->getJson('/api/categories');
        $response->assertStatus(403);
    }

    public function test_creator_can_access_create_category_route()
    {
        $categoryData = ['name' => 'Creator Category', 'description' => 'Created by a creator.'];
        $response = $this->actingAs($this->creatorUser)->postJson('/api/categories', $categoryData);
        $response->assertStatus(201); // Created
        $this->assertDatabaseHas('categories', ['name' => 'Creator Category']);
    }

    public function test_creator_can_access_list_categories_route()
    {
        Category::factory()->create(['name' => 'Test Category']);
        $response = $this->actingAs($this->creatorUser)->getJson('/api/categories');
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Test Category']);
    }

    public function test_unauthenticated_user_cannot_access_create_category_route()
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'Unauth Category',
            'description' => 'This should not be created.'
        ]);
        $response->assertStatus(401); // Unauthorized
    }

    // Test cases for Question Management (creator routes)
    // Similar tests should be written for /api/questions routes

    public function test_player_cannot_access_create_question_route()
    {
        $category = Category::factory()->create(); // Questions need a category
        $response = $this->actingAs($this->playerUser)->postJson('/api/questions', [
            'content' => 'Player Question?',
            'category_id' => $category->id,
            'difficulty' => 'easy',
            'choices' => [
                ['content' => 'A', 'is_correct' => true],
                ['content' => 'B', 'is_correct' => false],
            ]
        ]);
        $response->assertStatus(403);
    }

    public function test_creator_can_access_create_question_route()
    {
        $category = Category::factory()->create();
        $questionData = [
            'content' => 'Creator Question?',
            'category_id' => $category->id,
            'difficulty' => 'medium',
            'choices' => [
                ['content' => 'Opt1', 'is_correct' => true],
                ['content' => 'Opt2', 'is_correct' => false],
            ]
        ];
        $response = $this->actingAs($this->creatorUser)->postJson('/api/questions', $questionData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('questions', ['content' => 'Creator Question?']);
    }

    public function test_unauthenticated_user_cannot_access_create_question_route()
    {
        $category = Category::factory()->create();
        $response = $this->postJson('/api/questions', [
            'content' => 'Unauth Question?',
            'category_id' => $category->id,
            'difficulty' => 'hard',
            'choices' => [
                ['content' => 'X', 'is_correct' => true],
                ['content' => 'Y', 'is_correct' => false],
            ]
        ]);
        $response->assertStatus(401);
    }

    // Tests for general authenticated routes (e.g., quiz start, accessible by both roles)

    public function test_player_can_access_start_quiz_route()
    {
        $category = Category::factory()->create();
        // Ensure at least one question exists in the category for the quiz to start
        Question::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($this->playerUser)->getJson("/api/quiz/start?category_id={$category->id}&count=1");
        $response->assertStatus(200);
    }

    public function test_creator_can_access_start_quiz_route()
    {
        $category = Category::factory()->create();
        Question::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($this->creatorUser)->getJson("/api/quiz/start?category_id={$category->id}&count=1");
        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_access_start_quiz_route()
    {
        $category = Category::factory()->create();
        Question::factory()->create(['category_id' => $category->id]);

        $response = $this->getJson("/api/quiz/start?category_id={$category->id}&count=1");
        $response->assertStatus(401);
    }
}
