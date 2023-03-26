<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-P2WQ6LRZ9W"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-P2WQ6LRZ9W');
</script>
    <meta name="yandex-verification" content="edd5013949f75dcc" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Заголовок'}}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
 <style>
 .navbar-nav {
            margin-left: auto;
        }
        
/* Modify the background color */
 .navbar {
    background-color: #292929 !important;;
}
  </style>
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();
   for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
   k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(91855766, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/91855766" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
</head>
<body class="d-flex flex-column min-vh-100">

<div id="app">
<nav class="navbar navbar-expand-md navbar-dark bg-dark">
  <div class="container">
    <a href="./" class="navbar-brand"><img src="{{asset('img/logo.svg')}}" alt="" width=270 title='е-е-ту-ру-ру'></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="true">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav mb-2">
        <li class="nav-item">
          <a href="./" class="nav-link">Очередь сейчас</a>
        </li>
        <li class="nav-item">
          <a href="/about" class="nav-link">О сервисе</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <footer class="pt-2 my-2 my-md-2 border-top mt-auto">
      <div class="row">
        <div class="col-12 col-md">
          <h6 class="ms-3">&copy; 2022-2023 ee2ru.ru - очередь на границе Нарва-Ивангород (информационный сервис)</h6>
        </div>
      </div>
    </footer>
</body>
</html>
