@extends('templates.mainTemplate')

@section('content')
</head>
<body id="body">
    <div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm">
        <a class="my-0 mr-md-auto font-weight-normal" href="/"><h5 >TestHub</h5></a>
        <a class="btn btn-outline-primary" href="#">Войти</a>
    </div>	
	<div class="container">
		@csrf
		<div class="col-md-12">
			@if ($result->balls >= $test->minBalls)
				<h2>Поздравляем!</h2>
				<p>Вы успешно прошли тест {{ $test->name }}, набрав</p>
			@else 
				<h2>Сожалеем :(</h2>
				<p>Вы не прошли тест {{ $test->name }}, набрав</p>
			@endif
			<h2 class="text-center mb-4">{{ $result->balls }} из {{ $test->maxBalls }} баллов</h2>
			<p>Пожалуйста, укажите ваше имя, под которым вы будете отображаться в таблице результатов у преродавателя. Вы также можете зарегистрироваться на сайте, введя пароль и e-mail.</p>
			<div id="userNameField" class="mb-4 mt-4">
				<input id="userName" type="text" class="col-md-6">
				<button type="button" class="col-md-2 btn btn-sm btn-outline-primary" onclick="setUserName()">Сохранить</button>
			</div>
			<button type="button" class="btn btn-link">Зарегистрироваться</button>
			<br>
			<a class="btn btn-link" href="">Посмотреть ответы</a>
		</div>
	</div>

    <script type="text/x-template" id="errorPopup">
        <div class="alert alert-danger" role="alert">
            [[message]]
        </div>        
    </script>

    <script src="{{ URL::asset('js/testResult.js') }}"></script>
@endsection