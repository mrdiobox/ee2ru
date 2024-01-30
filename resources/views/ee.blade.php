@extends('layouts.app', ['title' => 'Очередь на границе с Эстонией Нарва, Койдула, Шумилкино сейчас'])

@section('content')

<div class="container my-0">
    <div class="row">
        <div class="col-md-4 col-sm-12 text-center">
        <div class="alert alert-warning" role="alert">
  Внимание! Сайт обновлен! Для корректной работы (<b>если не отображаются очереди</b>), очистите кэш вашего браузера. Ctrl-F5
</div>
        <a class="btn btn-primary" href="./" role="button">Нарва</a>
        <a class="btn btn-primary" href="koidula" role="button">Койдула</a>
        <a class="btn btn-primary" href="luhamaa" role="button">Лухамаа</a>
            <h1>Очередь {{ $border_title }}</h1>
            <vue-ellipse-progress-b :border_id='@json($border_id)' class="w-100"></vue-ellipse-progress-b>
            <telegram></telegram>
            <h2>Список вызванных</h2>
            <car-numbers :border_id='@json($border_id)' class="mb-10"></car-numbers>
        </div>
        <div class="col-md-8 col-sm-12 text-center">
            <h2>Статистика задержки очереди</h2>
            <chartline-component :border_id='@json($border_id)'></chartline-component>
            
        </div>
    </div>
</div>

@endsection