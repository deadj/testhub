<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;
use Tests\TestCase;

class TestControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testPrintPrefacePage()
    {
        factory(\App\Models\Test::class)->create();
        $response = $this->get('1/preface');
        $response->assertStatus(200);
    }

    public function testPrintPrefacePageFalse()
    {
        $response = $this->get('1/preface');
        $response->assertStatus(404);
    }    

    public function testPrintQuestionPage()
    {
        factory(\App\Models\Test::class)->create();
        factory(\App\Models\Question::class)->create();

        $response = $this->get('1/question');
        $response->assertStatus(200);
    }

    public function testPrintQuestionPageFalse()
    {
        $response = $this->get('1/question');
        $response->assertStatus(404);        
    }

    public function testPrintResultPage()
    {
        factory(\App\Models\Test::class)->create();
        factory(\App\Models\Question::class)->create();
        factory(\App\Models\Result::class)->create();

        $response = $this->withCookie('userId', 1)->get('1/result');
        $response->assertStatus(200);
    }

    public function testPrintResultPageFalse()
    {
        $response = $this->get('10/result');
        $response->assertStatus(302);        
    }

    public function testAddAnswerTest()
    {
        factory(\App\Models\Test::class)->create(['done' => 1]);
        factory(\App\Models\TestTime::class)->create();
        factory(\App\Models\Question::class)->create();
        factory(\App\Models\Question::class)->create([
            'id' => 2,
            'number' => 2
        ]);

        $response = $this->withCookie('userId', 1)->post('/addAnswer', [
            'questionId' => 1,
            'questionType' => 'textAnswer',
            'testId' => 1,
            'questionNumber' => 1,
            'value' => json_encode('done')
        ]);   

        $response->assertStatus(200);
    }

    public function testAddAnswerEndTime()
    {
        factory(\App\Models\TestTime::class)->create(['created_at' => Carbon::yesterday()]);
        factory(\App\Models\Test::class)->create(['done' => 1]);
        factory(\App\Models\Question::class)->create();
        factory(\App\Models\Question::class)->create([
            'id' => 2,
            'number' => 2
        ]);

        $response = $this->withCookie('userId', 1)->post('/addAnswer', [
            'questionId' => 1,
            'questionType' => 'textAnswer',
            'testId' => 1,
            'questionNumber' => 1,
            'value' => json_encode('done')
        ]);   

        $response
            ->assertStatus(200)
            ->assertSeeText('lastQuestion');
    }

    public function testAddAnswerLastQuestion()
    {
        factory(\App\Models\TestTime::class)->create();
        factory(\App\Models\Test::class)->create(['done' => 1]);
        factory(\App\Models\Question::class)->create();
        factory(\App\Models\Question::class)->create([
            'id' => 2,
            'number' => 2
        ]);

        $response = $this->withCookie('userId', 1)->post('/addAnswer', [
            'questionId' => 2,
            'questionType' => 'textAnswer',
            'testId' => 1,
            'questionNumber' => 3,
            'value' => json_encode('done')
        ]);   

        $response
            ->assertStatus(200)
            ->assertSeeText('lastQuestion');
    }

    public function testSetUserName()
    {
        factory(\App\Models\User::class)->create();
        $response = $this->withCookie('userId', 1)->post('/setUserName', [
            'userName' => 'TestUser'
        ]);   

        $response->assertStatus(200);
    }

    public function testGetQuestionForTest()
    {
        factory(\App\Models\Test::class)->create(['done' => 1]);
        factory(\App\Models\Question::class)->create();
        factory(\App\Models\Answer::class)->create();

        $response = $this->withCookie('userId', 1)->post('/getQuestionForTest', [
            'questionId' => 1,
            'testId' => 1,
        ]);     

        $response
            ->assertStatus(200)
            ->assertJson([
                'answer' => '1',
            ]);
    }

    public function testSaveResult()
    {
        factory(\App\Models\TestTime::class)->create();
        factory(\App\Models\Test::class)->create(['done' => 1]);
        factory(\App\Models\Answer::class)->create();
        factory(\App\Models\Answer::class)->create(['questionId' => 2]);
        factory(\App\Models\Question::class)->create();
        factory(\App\Models\Question::class)->create([
            'id' => 2,
            'number' => 2
        ]);        

        $response = $this->withCookie('userId', 1)->post('/stopTest', [
            'testId' => 1,
        ]);

        $response->assertStatus(200);
    }
}
