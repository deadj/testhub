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
					<div class="row">
						<small class="col-md-6">Название</small>
						<small class="col-md-3">Сдавало / прошло</small>
					</div>
					<div class="row">
						<div class="col-md-11 text-left">
							<hr class="mt-2 mb-1">
						</div>
					</div>
				</div>
				@foreach ($tests as $test)
					<div class="container mt-3">
						<div class="row">
							<div class="col-md-6">
								<h3 class="testName mb-0">{{ $test->name }}</h3>	
								<small>
									@foreach (json_decode($test->tags, true) as $tag)
										<a class="mr-2" href=""> {{ $tag }}</a>
									@endforeach
								</small>
							</div>
							<div class="col-md-3">
								<p>{{ $test->countOfPassed }} / {{ $test->countOfParticipants }}</p>
							</div>
							<div class="col-md-3">
								<a class="btn btn-outline-primary" href="">Перейти</a>
							</div>
						</div>
					</div>
				@endforeach
				<h5 class="ml-3 mt-5"><a href="/tests">Смотреть все тесты</a></h5>
			</div>
			@include('templates.aboutSite')
		</div>
	</div>
</body>
</html>