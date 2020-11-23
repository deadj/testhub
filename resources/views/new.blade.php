@extends('templates.mainTemplate')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm">
        <a class="my-0 mr-md-auto font-weight-normal" href="/"><h5 >TestHub</h5></a>
        <a class="btn btn-outline-primary" href="#">Войти</a>
    </div>	
    <div class="container">
        <div class="row">
            <div id="newTest" class="col-9 pr-5">
                <h2 class="mb-4">Создание теста</h2>
                <div  id="testInfo">
                    @csrf
                    <div class="form-group">
                        <label for="testName"><span class="text-danger pr-2">*</span>Название</label>
                        <input class="form-control" type="text" id="testName">		
                    </div>
                    <div class="form-group">
                        <label for="tags">Теги</label>
                        <input class="form-control" type="text" id="tags">		
                    </div>
                    <div class="form-group">
                        <label for="testForeword">Предисловие</label>
                        <textarea class="form-control" id="testForeword" rows="5"></textarea>
                    </div> 
                    <div class="mb-3">
                        <span class="text-danger pr-2">*</span>
                        Проходной балл
                        <input id="passingScore" style="width: 80px;" class="ml-1 form-control d-inline-block" type="number" min="0">
                    </div>
                    <div class="mb-0">
                        <label>
                            <input type="checkbox" id="timeLimitCheckbox" onchange="openTimeLimitField()">
                            Ограничение по времени
                        </label>
                        <div class="d-none" id="timeLimitField">
                            <input id="timeLimit" class="ml-1 form-control d-inline-block col-8" type="number" min="5" max="300">
                            мин.
                        </div> 
                    </div>
                    <label>
                        <input id="showWrongAnswers" class="mr-1" type="checkbox">
                        Разрешить смотреть список неправильных ответов после теста
                    </label>
                    <label>
                        <input id="publicResults" class="mr-1" type="checkbox">
                        Сделать все результаты прохождения публичными
                    </label>
                    <br>
                    <input type="submit" class="btn btn-primary mt-4 float-right" value="Продолжить" onclick="addTest(this)">
                </div>
            </div>
            <div id="rightBlock" class="col-md-3">
                <p>Вы можете создавать 
                тесты без регистрации, но если вы зарегистрируетесь, то легко сможете управлять своими тестами и просматривать результаты.</p>
                <p>Если вы сейчас перейдёте к регистрации, то введённые вами данные не потеряются.</p>
                <p>Также, после создания теста вы сможете указать e-mail, чтобы получать уведомления о сдаче тестов и получите ссылку для просмотра результатов.</p>
                <a class="btn btn-link btn-block" href="">Зарегистрироваться</a>
                <hr>
                <div id="testData"></div>
            </div>
        </div>
    </div>
    <!-- <footer style="height: 500px"></footer> -->
    <script type="text/x-template" id="oneAnswerPopup">
        <div class="container answer mt-2">
            <div class="row">
                <div class="col-md-1">
                    <input type="radio" name="answerRadio" class="form-control" answerid="0">
                </div>
                <div class="col-md-9">
                    <input type="text" class="form-control">
                </div>
                <div class="col-md-2 p-0">
                    <button type="button" class="btn btn-outline-danger btn-block" onclick="deleteAnswer(this)">Удалить</button>
                </div>
            </div>
        </div>
        <div class="container answer mt-2">
            <div class="row">
                <div class="col-md-1">
                    <input type="radio" name="answerRadio" class="form-control" answerid="1">
                </div>
                <div class="col-md-9">
                    <input type="text" class="form-control">
                </div>
                <div class="col-md-2 p-0">
                    <button type="button" class="btn btn-outline-danger btn-block" onclick="deleteAnswer(this)">Удалить</button>
                </div>
            </div>
        </div>
        <div class="container mt-2">
            <div class="row">
                <div class="col-md-1">
                </div>
                <div class="col-md-9">
                    <button type="button" class="btn btn-link btn-block" onclick="addNewAnswer(this)">Добавить ещё вариант</button>
                </div>
                <div class="col-md-2">
                </div>
            </div>
        </div>
    </script>
    <script type="text/x-template" id="multipleAnswersPopup">
        <div class="container answer mt-2">
            <div class="row">
                <div class="col-md-1">
                    <input type="checkbox" class="form-control" answerid="0">
                </div>
                <div class="col-md-9">
                    <input type="text" class="form-control">
                </div>
                <div class="col-md-2 p-0">
                    <button type="button" class="btn btn-outline-danger btn-block" onclick="deleteAnswer(this)">Удалить</button>
                </div>
            </div>
        </div>
        <div class="container answer mt-2">
            <div class="row">
                <div class="col-md-1">
                    <input type="checkbox" class="form-control" answerid="1">
                </div>
                <div class="col-md-9">
                    <input type="text" class="form-control">
                </div>
                <div class="col-md-2 p-0">
                    <button type="button" class="btn btn-outline-danger btn-block" onclick="deleteAnswer(this)">Удалить</button>
                </div>
            </div>
        </div>
        <div class="container mt-2">
            <div class="row">
                <div class="col-md-1">
                </div>
                <div class="col-md-9">
                    <button type="button" class="btn btn-link btn-block" onclick="addNewAnswer(this)">Добавить ещё вариант</button>
                </div>
                <div class="col-md-2">
                </div>
            </div>
        </div>
    </script>
    <script type="text/x-template" id="publishTemplate">
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
                <a href="publishLink">publishLink</a>
                <button type="button" class="btn btn-light">Копировать</button>
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
        <div class="row mt-5">
            <div class="col-12">
                <p class="mb-1">Ссылка для просмотра результатов тестов</p>
                <a href="resultsLink">resultsLink</a>
                <button type="button" class="btn btn-light">Копировать</button>
                <br>
                <a href="myTestsLink">Перейти к списку моих тестов</a>
            </div>
        </div>
    </script>
    <script type="text/x-template" id="textAnswerPopup">
        <p class="col-md-1 d-inline-block pl-0">Ответ:</p>
        <input type="text" id="textAnswerField" class="form-control col-md-10 d-inline-block">
    </script>
    <script type="text/x-template" id="numberAnswerPopup">
        <p class="col-md-2 d-inline-block pl-0">Ответ:</p>
        <input type="number" id="numberAnswerField" class="form-control col-md-3 d-inline-block">
        <br>
        <p class="col-md-2 d-inline-block pl-0">Погрешность:</p>
        <input type="number" id="errorAnswerField"class="form-control col-md-3 d-inline-block">
    </script>
    <script type="text/x-template" id="questionPopup">
        <form>
            <div class="form-group">
                <label for="question">Вопрос:</label>
                <textarea class="form-control" id="question" rows="5" required></textarea>
            </div>
            <div class="mb-3">
                Число баллов:  
                <input id="ballsCount" class="ml-1 form-control d-inline-block col-md-2" type="number" required>
            </div>
            <div id="answersTypes">
                Тип ответа: 
                <button type="button" class="pl-2 btn btn-link text-success disabled" id="oneAnswer" onclick="changeAnswerType(this)">Один ответ</button>
                <button type="button" class="pl-2 btn btn-link" id="multipleAnswers" onclick="changeAnswerType(this)">Несколько ответов</button>
                <button type="button" class="pl-2 btn btn-link" id="numberAnswer" onclick="changeAnswerType(this)">Число</button>
                <button type="button" class="pl-2 btn btn-link" id="textAnswer" onclick="changeAnswerType(this)">Текст</button>
            </div>
        </form>
    </script>
    <script type="text/x-template" id="errorPopup">
        <div class="alert alert-danger" role="alert">
            message
        </div>        
    </script>
    <script type="text/x-template" id="fieldErrorsPopup">

    </script>
    <script src="{{ URL::asset('js/new.js') }}"></script>
@endsection