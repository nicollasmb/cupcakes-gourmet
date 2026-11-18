<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Erro <?= (int) $codigo ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height:100vh">
<div class="text-center">
    <div style="font-size:4rem">🧁</div>
    <h1 class="h3"><?= htmlspecialchars($mensagem) ?></h1>
    <a href="index.php" class="btn btn-primary mt-3">Voltar para a vitrine</a>
</div>
</body>
</html>


