@extends('templates.mainTemplate')

@section('content')
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
								<a class="btn btn-outline-primary" href="{{ $test->id }}/preface">Перейти</a>
							</div>
						</div>
					</div>
				@endforeach
				<h5 class="ml-3 mt-5"><a href="/tests">Смотреть все тесты</a></h5>
			</div>
			@include('templates.aboutSite')
		</div>
	</div>
@endsection