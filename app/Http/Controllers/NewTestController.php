<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Question;
use App\Models\Tag;
use Validator;

class NewTestController extends Controller
{
    private $question;
    private $test;

    public function __construct()
    {
        $this->question = new Question();
        $this->test = new Test();
        $this->tag = new Tag();
    }

    public function print()
    {
    	return view('new');
    }

    public function addTest(Request $request)
    {
        $validator = $this->getTestValidator($request);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        } else {
            $test = new Test();

            $this->test->name              = $request->testName;
            $this->test->tags              = $request->tags;
            $this->test->foreword          = $request->testForeword;
            $this->test->minBalls          = $request->minBalls;
            $this->test->maxBalls          = 0;
            $this->test->minutesLimit      = $request->timeLimit;
            $this->test->showWrongAnswers  = $request->showWrongAnswers;
            $this->test->publicResults     = $request->publicResults;

            if ($request->showWrongAnswers) {
                $this->test->showWrongAnswers = 1;
            } else {
                $this->test->showWrongAnswers = 0;
            }

            if ($request->publicResults) {
                $this->test->publicResults = 1;
            } else {
                $this->test->publicResults = 0;
            }

            $this->test->save();

            if ($request->has('tags')) {
                $this->addTags($request->tags);
            } 

            return $this->test->id;     
        }
    }

    public function addQuestion(Request $request)
    {
        $validator = $this->getQuestionValidator($request);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        } else {
            $testId = $request->testId;

            $this->question->testId     = $testId;
            $this->question->questions  = $request->question;
            $this->question->balls      = $request->balls;
            $this->question->type       = $request->type;
            $this->question->answer     = $request->answer;
            $this->question->trueAnswer = $request->trueAnswer;
            $this->question->number     = $this->question->where('testId', $testId)->count() + 1;

            $this->question->save();

            $questionCount = $this->question->where('testId', $testId)->count();
            $balls = $this->question->where('testId', $testId)->get()->sum('balls');
            $time = Test::find($testId)->minutesLimit;

            if ($time == NULL) {
                $time = "&#8734;";
            } else {
                $time /= $questionCount;
            }

            $trueAnswer = $this->getTrueAnswer($this->question->id);

            return array(
                'questionId'       => $this->question->id,
                'questionCount'    => $questionCount,
                'balls'            => $balls,
                'time'             => $time,
                'cutQuestion'      => mb_substr($this->question->questions, 0, 20) . "...",
                'fullQuestion'     => $this->question->questions,
                'cutAnswer'        => mb_substr($trueAnswer, 0, 20) . "...",
                'fullAnswer'       => $trueAnswer,
                'ballsForQuestion' => $this->question->balls,
                'questionNumber'   => $this->question->number
            );
        }
    }

    public function changeOrderOfQuestionNumbers(Request $request)
    {
        foreach (json_decode($request->numbersArray, true) as $number) {
            $this->question->where('id', $number[0])->update(['number' => $number[1]]);
        }    
    }

    public function getQuestion(Request $request): string
    {
        return json_encode($this->question->find($request->id));
    }

    public function updateQuestion(Request $request): array
    {
        $validator = $this->getQuestionValidator($request);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        } else {
            $question = $this->question->where('id', $request->id);

            $question->update([
                'questions'  => $request->question,
                'balls'      => $request->balls,
                'type'       => $request->type,
                'answer'     => $request->answer,
                'trueAnswer' => $request->trueAnswer
            ]);

            
            $trueAnswer = $this->getTrueAnswer($request->id);
            $question = $this->question->find($request->id);

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

    public function getTags()
    {
        $tagsObjects = $this->tag->get();
        $tags = [];
        
        foreach ($tagsObjects as $tag) {
            $tags[] = $tag->tag;
        }

        return $tags;
    }

    private function addTags(string $tags): void
    {   
        $tags = preg_split('/,[ ]?/ui', mb_strtolower($tags), 0, PREG_SPLIT_NO_EMPTY);
        
        foreach ($tags as $tag) {
            if ($this->tag->where('tag', $tag)->doesntExist()) {
                $newTag = new Tag();
                $newTag->tag = trim($tag);
                $newTag->save();
            }
        }
    }

    private function getTestInfo(int $testId): array
    {
        $questionCount = $this->question->where('testId', $testId)->count();
        $balls = $this->question->where('testId', $testId)->get()->sum('balls');
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
        $trueAnswer = json_decode($this->question->find($id)->trueAnswer, true);
        $type = $this->question->find($id)->type;
        $answer = NULL;

        if ($type == Question::TYPE_ONE_ANSWER) {
            $answer = json_decode($this->question->find($id)->answer, true)[$trueAnswer];
        } elseif ($type == Question::TYPE_MULTIPLE_ANSWER) {
            foreach ($trueAnswer as $stepAnswer) {
                $answer .= json_decode($this->question->find($id)->answer, true)[$stepAnswer] . "; ";
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
