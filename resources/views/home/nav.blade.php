<!DOCTYPE html>
<html lang="en">
<head>
  <title>Controle de Materiais</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<nav class="navbar navbar-expand-sm navbar-light bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="{{route('home')}}">
     <img src="https://intranet.6cta.eb.mil.br/images/6cta_logo5.png#joomlaImage://local-images/6cta_logo5.png?width=54&height=75" alt="Avatar Logo" style="width:40px;">
    </a>
     <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mynavbar">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="{{route('listar_materiais')}}">Meus materiais</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{route('cadastrar_materiais')}}">Cadastrar materiais</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="">Link</a>
        </li>
      </ul>
      <form class="d-flex">
        <input class="form-control me-2" type="text" placeholder="Search">
        <button class="btn btn-primary" type="button">Pesquisar</button>
      </form>
    </div>
  </div>
</nav>