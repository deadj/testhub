<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NewTestControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testPrintNewTestPage()
    {
        $response = $this->get('/new');

        $response->assertStatus(200);
    }

    public function testAddTest()
    {
        $response = $this->post('/addTest', [
            'testName'         => 'Testing test',
            'testForeword'     => 'teestiiiing',
            'minBalls'         => 10,
            'timeLimit'        => 10,
            'showWrongAnswers' => true,
            'publicResults'    => true
        ]);

        $response->assertStatus(200);
    }

    public function testAddTestFail()
    {
        $response = $this->post('/addTest', [
        ]);

        $response->assertStatus(422);
    }

    public function testAddQuestionText()
    {
        factory(\App\Models\Test::class)->create();

        $response = $this->post('/addQuestion', [
            'testId' => 1,
            'question' => "Test?",
            'balls' => 10,
            'type' => 'textAnswer',
            'answer' => json_encode('null'),
            'trueAnswer' => json_encode('Yes'),
        ]);

        $response->assertStatus(200);
    }

    public function testAddQuestionNumber()
    {
        factory(\App\Models\Test::class)->create();

        $response = $this->post('/addQuestion', [
            'testId' => 1,
            'question' => "Test?",
            'balls' => 10,
            'type' => 'numberAnswer',
            'answer' => json_encode('null'),
            'trueAnswer' => json_encode([10 ,1]),
        ]);

        $response->assertStatus(200);
    }

    public function testAddQuestionOne()
    {
        factory(\App\Models\Test::class)->create();

        $response = $this->post('/addQuestion', [
            'testId' => 1,
            'question' => "Test?",
            'balls' => 10,
            'type' => 'oneAnswer',
            'answer' => json_encode(['1', '2']),
            'trueAnswer' => json_encode('0'),
        ]);

        $response->assertStatus(200);
    }

    public function testAddQuestionMultiple()
    {
        factory(\App\Models\Test::class)->create();

        $response = $this->post('/addQuestion', [
            'testId' => 1,
            'question' => "Test?",
            'balls' => 10,
            'type' => 'multipleAnswer',
            'answer' => json_encode(['1', '2']),
            'trueAnswer' => json_encode(0),
        ]);

        $response->assertStatus(200);
    }

    public function testAddQuestionTextFail()
    {
        factory(\App\Models\Test::class)->create();

        $response = $this->post('/addQuestion', [
            'testId' => 1,
            'balls' => 10,
            'type' => 'textAnswer',
            'answer' => json_encode('null'),
            'trueAnswer' => json_encode('Yes'),
        ]);

        $response->assertStatus(422);
    }

    public function testAddQuestionNumberFail()
    {
        factory(\App\Models\Test::class)->create();

        $response = $this->post('/addQuestion', [
            'question' => "Test?",
            'balls' => 10,
            'type' => 'numberAnswer',
            'answer' => json_encode('null'),
            'trueAnswer' => json_encode([10 ,1]),
        ]);

        $response->assertStatus(422);
    }

    public function testAddQuestionOneFail()
    {
        factory(\App\Models\Test::class)->create();

        $response = $this->post('/addQuestion', [
            'testId' => 1,
            'question' => "Test?",
            'type' => 'oneAnswer',
            'answer' => json_encode(['1', '2']),
            'trueAnswer' => json_encode('0'),
        ]);

        $response->assertStatus(422);
    }

    public function testAddQuestionMultipleFail()
    {
        factory(\App\Models\Test::class)->create();

        $response = $this->post('/addQuestion', [
            'question' => "Test?",
            'balls' => 10,
            'type' => 'multipleAnswer',
            'answer' => json_encode(['1', '2']),
            'trueAnswer' => json_encode(0),
        ]);

        $response->assertStatus(422);
    }

    public function testChangeOrderOfQuestionNumbers()
    {   
        factory(\App\Models\Test::class)->create();
        factory(\App\Models\Question::class)->create();
        factory(\App\Models\Question::class)->create([
            'id' => 2,
            'number' => 2
        ]);

        $response = $this->post('/changeOrderOfQuestionNumbers', [
            'numbersArray' => json_encode([[1, 2], [2, 1]])
        ]);

        $response->assertStatus(200);
    }

    public function testGetQuestionForCreate()
    {
        factory(\App\Models\Question::class)->create();
        $response = $this->post('/getQuestionForCreate', ['id' => 1]);
        $response
            ->assertStatus(200)
            ->assertJson(['id' => 1]);
    }

    public function testUpdateQuestion()
    {
        factory(\App\Models\Test::class)->create();
        factory(\App\Models\Question::class)->create();

        $response = $this->post('/updateQuestion', [
            'id' => 1,
            'testId' => 1,
            'question' => "testUpdateQuestion",
            'balls' => 100,
            'type' => 'textAnswer',
            'answer' => json_encode('null'),
            'trueAnswer' => json_encode('testUpdateQuestion'),
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'fullAnswer' => 'testUpdateQuestion',
                'balls' => 100,
            ]);        
    }

    public function testGetTestInfoToView()
    {
        factory(\App\Models\Test::class)->create();
        factory(\App\Models\Question::class)->create();
        factory(\App\Models\Question::class)->create([
            'id' => 2,
            'number' => 2
        ]);

        $response = $this->post('/getTestInfoToView', ['testId' => 1]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'questionCount' => 2,
                'balls' => 20,
            ]);
    }

    public function testFinishCreatingTest()
    {
        factory(\App\Models\Test::class)->create();
        $response = $this->post('/finishCreatingTest', ['testId' => 1]);
        $response->assertStatus(200);
    }

    public function testGetTags()
    {
        factory(\App\Models\Tag::class)->create(['tag' => 'this']);
        factory(\App\Models\Tag::class)->create(['tag' => 'is']);
        factory(\App\Models\Tag::class)->create(['tag' => 'tag']);

        $response = $this->get('/getTags');
        $response
            ->assertStatus(200)
            ->assertJson(['this', 'is', 'tag']);
    }
}
