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
        <div id="publishBlock" class="col-9 pr-5">
                <h2 class="mb-4">Создание теста</h2>
                <p>Тест успешно создан. Вы можете ввести свой e-mail, чтобы получать на него уведомления о прохождении тестов, а также придумать себе пароль для входа на сайт.</p> 
                <div class="row">
                    <div class="col-10">
                        <input type="text" class="form-control" placeholder="E-mail">
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-outline-primary">Сохранить</button>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-12">
                        <p class="mb-1">Ссылка для прохождения теста</p>
                        <div class="row">
                            <div class="col-10">
                                <input id="testLink" type="text" class="form-control" value="http://localhost/{{ $id }}/preface">
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-light" onclick="copyLink()">Копировать</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-12">
                        <p>Поделиться ссылкой</p>
                        <button type="button" class="btn btn-secondary mr-1">ВК</button>
                        <button type="button" class="btn btn-secondary mr-1 ml-1">Tw</button>
                        <button type="button" class="btn btn-secondary mr-1 ml-1">Fb</button>
                        <button type="button" class="btn btn-secondary mr-1 ml-1">Ok</button>
                    </div>
                </div>
<!--                 <div class="row mt-5">
                    <div class="col-12">
                        <p class="mb-1">Ссылка для просмотра результатов тестов</p>
                        <div class="row">
                            <div class="col-10">
                                <input id="testLink" type="text" class="form-control" value="http://localhost/star/result/{{ $id }}">
                            </div>
                            <div class="col-2">                        
                                <button type="button" class="btn btn-light">Копировать</button>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
            <div id="rightBlock" class="col-md-3"></div>
        </div>
    </div>
    
    <script src="{{ URL::asset('js/publish.js') }}"></script>
    <script src="{{ URL::asset('js/main.js') }}"></script>
@endsection