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
@include('home.nav')
<div class="container mt-3">
  <h2>Lista de materiais da seção</h2>
  <table class="table">
        <thead class="table-primary">
            <tr>
                <th>Material</th>
                <th>Descrição</th>
                <th>Seção</th>
                <th>Quantidade</th>
            </tr>
        </thead>
    </table>
</div>

</body>
</html>