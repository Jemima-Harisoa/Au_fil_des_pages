<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Erreur Upload</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #fdf2f2;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .container {
      background: white;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      text-align: center;
      max-width: 400px;
    }
    h1 {
      color: #c62828;
      margin-bottom: 20px;
    }
    p {
      color: #333;
      font-size: 16px;
      margin-bottom: 30px;
    }
    .btn {
      display: inline-block;
      background: #1976d2;
      color: white;
      padding: 12px 20px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      transition: background 0.3s ease;
    }
    .btn:hover {
      background: #1565c0;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>❌ Erreur lors de l’upload</h1>
    <p>Nous avons rencontré un problème lors du téléchargement de votre image.<br>
       Merci de réessayer ou de vérifier le format du fichier.</p>
    <a href="/retourFill" class="btn">⬅ Réessayer</a>
  </div>
</body>
</html>
