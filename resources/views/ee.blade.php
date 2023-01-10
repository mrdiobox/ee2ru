@extends('layouts.app', ['title' => 'Очередь на границе с Эстонией Нарва - Ивангород сейчас'])

@section('content')

<div class="container my-0">
    <div class="row">
        <div class="col-md-4 col-sm-12 text-center">
            <h1>Очередь Нарва - Ивангород</h1>
            <vue-ellipse-progress-b class="w-100"></vue-ellipse-progress-b>
            <telegram></telegram>
            <h2>Список вызванных</h2>
            <car-numbers class="mb-10"></car-numbers>
        </div>
        <div class="col-md-8 col-sm-12 text-center">
            <h2>Статистика задержки очереди</h2>
            <chartline-component></chartline-component>
            
        </div>
    </div>
</div>

@endsection