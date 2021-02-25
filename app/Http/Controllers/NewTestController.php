<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Question;
use App\Models\Tag;
use App\Models\User;
use Validator;

class NewTestController extends Controller
{
    public function printNewTestPage()
    {
        return view('new');
    }

    public function printPublishTestPage(Request $request, int $id)
    {
        if (Test::where('id', $id)->exists()) {
            $test = Test::find($id);
        
            if (!$request->cookie('userId') || $test->userId != $request->cookie('userId')) {
                abort(404);
            }
        } else {
            abort(404);
        }

        return view('publish', ['id' => $id]);
    }

    public function addTest(Request $request)
    {
        $validator = $this->getTestValidator($request);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        } else {
            if (!$request->cookie('userId')) {
                $user = new User;
                $user->save();
            } else {
                $user = User::find($request->cookie('userId'));
            }

            $test = new Test();

            $test->name                = $request->testName;
            $test->foreword            = $request->testForeword;
            $test->minBalls            = $request->minBalls;
            $test->minutesLimit        = $request->timeLimit;
            $test->userId              = $user->id;

            if ($request->has('showWrongAnswers')) {
                $test->showWrongAnswers = 1;
            } else {
                $test->showWrongAnswers = 0;
            }

            if ($request->has('publicResults')) {
                $test->publicResults = 1;
            } else {
                $test->publicResults = 0;
            }

            if ($request->has('tags')) {
                $tags = preg_split('/,[ ]?/ui', mb_strtolower($request->tags), 0, PREG_SPLIT_NO_EMPTY);
                $test->tags = json_encode($tags);
                $this->addTags($tags);
            } else {
                $test->tags = json_encode([]);
            }

            $test->save();

            return response($test->id)->cookie('userId', $user->id, 60 * 24 * 30 * 12);
        }
    }

    public function addQuestion(Request $request)
    {
        $validator = $this->getQuestionValidator($request);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        } else {
            $testId = $request->testId;

            $question = new Question();

            $question->testId     = $testId;
            $question->questions  = $request->question;
            $question->balls      = $request->balls;
            $question->type       = $request->type;
            $question->answer     = $request->answer;
            $question->trueAnswer = mb_strtolower($request->trueAnswer);
            $question->number     = $question->where('testId', $testId)->count() + 1;

            $question->save();

            $questionCount = $question->where('testId', $testId)->count();
            $balls = $question->where('testId', $testId)->get()->sum('balls');
            $time = Test::find($testId)->minutesLimit;
            Test::where('id', $testId)->update(['maxBalls' => $balls]);

            if ($time == NULL) {
                $time = "&#8734;";
            } else {
                $time /= $questionCount;
            }

            $trueAnswer = $this->getTrueAnswer($question->id);

            return array(
                'questionId'       => $question->id,
                'questionCount'    => $questionCount,
                'balls'            => $balls,
                'time'             => $time,
                'cutQuestion'      => mb_substr($question->questions, 0, 20) . "...",
                'fullQuestion'     => $question->questions,
                'cutAnswer'        => mb_substr($trueAnswer, 0, 20) . "...",
                'fullAnswer'       => $trueAnswer,
                'ballsForQuestion' => $question->balls,
                'questionNumber'   => $question->number
            );
        }
    }

    public function changeOrderOfQuestionNumbers(Request $request)
    {
        foreach (json_decode($request->numbersArray, true) as $number) {
            Question::where('id', $number[0])->update(['number' => $number[1]]);
        }    
    }

    public function getQuestion(Request $request): string
    {
        return json_encode(Question::find($request->id));
    }

    public function updateQuestion(Request $request): array
    {
        $validator = $this->getQuestionValidator($request);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        } else {
            Question::where('id', $request->id)->update([
                'questions'  => $request->question,
                'balls'      => $request->balls,
                'type'       => $request->type,
                'answer'     => $request->answer,
                'trueAnswer' => $request->trueAnswer
            ]);
            
            $trueAnswer = $this->getTrueAnswer($request->id);
            $question = Question::find($request->id);

            $questionInfo = [
                'cutQuestion'      => mb_substr($question->questions, 0, 20) . "...",
                'fullQuestion'     => $question->questions,
                'cutAnswer'        => mb_substr($trueAnswer, 0, 20) . "...",
                'fullAnswer'       => $trueAnswer,
                'balls'            => $question->balls
            ];

            $testInfo = $this->getTestInfo($request->testId);
            
            $returnArray = $testInfo + $questionInfo;
            return $returnArray;
        }
    }

    public function getTestInfoToView(Request $request): array
    {
        return $this->getTestInfo($request->testId);
    }

    public function finishCreatingTest(Request $request): void
    {
        Test::where('id', $request->testId)->update(['done' => true]);
    }

    public function getTags()
    {
        $tagsObjects = Tag::get();
        $tags = [];
        
        foreach ($tagsObjects as $tag) {
            $tags[] = $tag->tag;
        }

        return $tags;
    }

    private function addTags(array $tags): void
    {           
        foreach ($tags as $tag) {
            if (Tag::where('tag', $tag)->doesntExist()) {
                $newTag = new Tag();
                $newTag->tag = trim($tag);
                $newTag->save();
            }
        }
    }

    private function getTestInfo(int $testId): array
    {
        $questionCount = Question::where('testId', $testId)->count();
        $balls = Question::where('testId', $testId)->get()->sum('balls');
        $time = Test::find($testId)->minutesLimit;

        if ($time == NULL) {
            $time = "&#8734;";
        } else {
            $time /= $questionCount;
        }       

        return [
            'questionCount' => $questionCount,
            'balls' => $balls,
            'time' => $time
        ];
    }

    private function getTrueAnswer(int $id)
    {
        $trueAnswer = json_decode(Question::find($id)->trueAnswer, true);
        $type = Question::find($id)->type;
        $answer = NULL;

        if ($type == Question::TYPE_ONE_ANSWER) {
            $answer = json_decode(Question::find($id)->answer, true)[$trueAnswer];
        } elseif ($type == Question::TYPE_MULTIPLE_ANSWER) {
            foreach ($trueAnswer as $stepAnswer) {
                $answer .= json_decode(Question::find($id)->answer, true)[$stepAnswer] . "; ";
            }
        } elseif ($type == Question::TYPE_NUMBER_ANSWER) {
            $answer = "от " . 
            strval($trueAnswer[0] - $trueAnswer[1]) . 
            " до " . 
            strval($trueAnswer[0] + $trueAnswer[1]);
        } elseif ($type == Question::TYPE_TEXT_ANSWER) {
            $answer = $trueAnswer;
        }

        return $answer;        
    }

    private function getTestValidator(object $request): object
    {
        return Validator::make($request->all(), [
            'testName' => 'required',
            'minBalls' => 'required|numeric|min:0',
            'tags' => 'not_regex:/[^\w\d\s,-]/ui'
        ],[
            'testName.required' => 'Введите название',
            'minBalls.required' => 'Введите минимальный балл',  
            'minBalls.min' => 'Минимальный балл должен быть не меньше 0',   
            'tags.not_regex' => 'Теги могут включать буквы, цифвы, пробелы, запятые и дефис'      
        ]);
    }

    private function getQuestionValidator(object $request): object
    {
        return Validator::make($request->all(), [
            'testId'   => 'required',
            'question' => 'required',
            'balls' => 'required|numeric',
            'type' => 'required',
            'trueAnswer' => 'required'
        ],[
            'question.required' => 'Введите вопрос',
            'balls.required' => 'Введите количество баллов',
            'trueAnswer' => 'Правильный ответ не выбран'
        ]); 
    }
}
