@extends('templates.mainTemplate')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm">
        <a class="my-0 mr-md-auto font-weight-normal" href="/"><h5 >TestHub</h5></a>
        <a class="btn btn-outline-primary" href="#">Войти</a>
    </div>	
	<div class="container">
			<div class="col-md-12">
				<input type="hidden" id="questionNumber" value="{{ $question->number }}">
				<input type="hidden" id="questionId" value="{{ $question->id }}">
				<input type="hidden" id="questionType" value="{{ $question->type }}">
				<input type="hidden" id="testId" value="{{ $question->testId }}">
				<h1 class="mb-4">{{ $test->name }}</h1>
				@if ($test->minutesLimit != NULL)
					<p>Осталось времени {{ $test->minutesLimit }}:00</p>
				@endif
				<p id="questionsBalls" class="text-secondary">За ответ на этот вопрос даётся {{ $question->balls }} баллов</p>
				<h5 id="questionText" class="mb-4">{{ $question->questions }}</h5>
				<div id="answersBlock">
					@if ($question->type == "oneAnswer")
						<p>Выберите один вариант ответа</p>
						<ul id="answer" style="list-style-type: none" class="pl-0">
							@foreach (json_decode($question->answer, true) as $number => $answer)
								<li>
									<label>
										<input type="radio" name="answer" class="mr-1">{{ $answer }}
									</label>
								</li>
							@endforeach
						</ul>
					@elseif ($question->type == "multipleAnswers")
						<p>Выберите ответ(ы)</p>
						<ul id="answer" style="list-style-type: none" class="pl-0">
							@foreach (json_decode($question->answer, true) as $number => $answer)
								<li>
									<label>
										<input type="checkbox" name="answer" class="mr-1">{{ $answer }}
									</label>
								</li>
							@endforeach
						</ul>						
					@elseif ($question->type == "textAnswer")
						<p>Введите ответ</p>
						<input id="answer" type="text" name="answer" class="col-md-10">
					@elseif ($question->type == "numberAnswer")
						<p>Введите число</p>
						<input id="answer" type="number" name="answer">
					@endif
				</div>
				<div class="mt-4">
					<button type="button" class="btn btn-light text-">Вернуться</button>
					<button type="button" class="btn btn-primary" onclick="setAnswer()">Ответить</button>
				</div>
			</div>
		</div>
	</div>

    <script type="text/x-template" id="errorPopup">
        <div class="alert alert-danger" role="alert">
            [[message]]
        </div>        
    </script>

	 <script src="{{ URL::asset('js/testQuestion.js') }}"></script>
@endsection