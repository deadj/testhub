<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>TestHub</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</head>
<body>
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
					Количество вопросов - {{ $questionsCount }}. За которое можно набрать от 0 до {{ $test->maxBalls }} баллов.
					@if ($test->minutesLimit != NULL)
						На тест даётся {{ $test->minutesLimit }} минут.
					@else 
						Время прохождения не ограничено.
					@endif
				</p>
				<p>
					Тест сдало {{ $test->countOfParticipants }} ({{ $test->countOfParticipants / 100 * $test->countOfPassed }}%) из {{ $test->countOfParticipants }} участников.
				</p>
				<div class="text-center mt-5">
					<a href="/{{ $test->id }}/quesionts" class="text-center btn btn-primary">Начать теста</a>
				</div>
				</div>
			</div>
			@include('templates.aboutSite')
		</div>
	</div>
</body>
</html>