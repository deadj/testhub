@extends('templates.mainTemplate')

@section('content')
</head>
<body id="body">
    <div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm">
        <a class="my-0 mr-md-auto font-weight-normal" href="/"><h5 >TestHub</h5></a>
        <a class="btn btn-outline-primary" href="#">Войти</a>
    </div>	
	<div class="container">
		<div class="row">
			<div class="col-md-9">
				<div class="container">
					<h1 class="mb-4">{{ $test->name }}</h1>
					<p>{{ $test->foreword }}</p>
					<p>
						Количество вопросов - {{ $questionsCount }}. За которое можно набрать от 0 до {{ $maxBalls }} баллов.
						@if ($test->minutesLimit != NULL)
							На тест даётся {{ $test->minutesLimit }} минут.
						@else 
							Время прохождения не ограничено.
						@endif
					</p>
					@if ($test->countOfParticipants == 0)
						<p>Текст пока никто не сдал. Вы можете стать первым.</p>
					@else
						<p>
							Тест сдало {{ $test->countOfPassed }} ({{ $test->countOfPassed * 100 / $test->countOfParticipants }}%) из {{ $test->countOfParticipants }} участников.
						</p>					
					@endif
					<div class="text-center mt-5">
						<a href="/{{ $test->id }}/question" class="text-center btn btn-primary">Начать теста</a>
					</div>
				</div>
			</div>
			@include('templates.aboutSite')
		</div>
	</div>
@endsection