@extends('templates.mainTemplate')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm">
        <a class="my-0 mr-md-auto font-weight-normal" href="/"><h5 >TestHub</h5></a>
        <a class="btn btn-outline-primary" href="#">Войти</a>
    </div>	
	<div class="container">
			<div class="col-md-12">
				<h1 class="mb-4">{{ $test->name }}</h1>
				@if ($test->minutesLimit != NULL)
					<p>Осталось времени {{ $test->minutesLimit }}:00</p>
				@endif
				<p class="text-secondary">За ответ на этот вопрос даётся {{ $question->balls }} баллов</p>
				<h3 class="mb-4">{{ $question->questions }}</h3>
				@if ($question->type == "oneAnswer")
					<p>Выберите один вариант ответа</p>
					<ul style="list-style-type: none" class="pl-0">
						@foreach (json_decode($question->answer, true) as $number => $answer)
							<li>
								<label>
									<input id="answer" type="radio" name="answer" number="{{ $number }}">
								{{ $answer }}
								</label>
							</li>
						@endforeach
					</ul>
				@elseif ($question->type == "multipleAnswers")
					<p>Выберите несколько вариантов ответа</p>
					<ul style="list-style-type: none" class="pl-0">
						@foreach (json_decode($question->answer, true) as $number => $answer)
							<li>
								<label>
									<input id="answer" type="checkbox" name="answer" number="{{ $number }}">
								{{ $answer }}
								</label>
							</li>
						@endforeach
					</ul>						
				@elseif ($question->type == "textAnswer")
					<p>Введите ответ</p>
					<input class="col-md-10" id="answer" type="text" name="answer">
				@elseif ($question->type == "numberAnswer")
					<p>Введите число</p>
					<input id="answer" type="number" name="answer">
				@endif
				<div class="mt-4">
					<button type="button" class="btn btn-light text-">Вернуться</button>
					<button type="button" class="btn btn-primary">Ответить</button>
				</div>
			</div>
		</div>
	</div>
@endsection