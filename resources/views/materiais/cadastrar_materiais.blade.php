<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
@include('home.nav')
<div class="container mt-3">
  <h2>Cadastrar novo material</h2>
  <form action="/action_page.php">
    <div class="mb-3 mt-3">
      <label for="email">Nome do material: </label>
      <input type="text" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="comment">Descrição do material:</label>
        <textarea class="form-control" rows="3" id="comment" name="text" required></textarea>
    </div>
    
    <label for="sel1" class="form-label">Selecione a seção:</label>
    <select class="form-select" id="sel1" name="sellist1" required>
      <option>Seção de Informática</option>
      <option>Cabeamento</option>
      <option>SSGIE</option>
      <option>SGO</option>
    </select>

    <br>

    <button type="submit" class="btn btn-primary">Cadastrar</button>
  </form>
</div>

</body>
</html>
