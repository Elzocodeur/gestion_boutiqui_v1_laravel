<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Carte de fidélité</title>
</head>
<body>
    <h1>Carte de fidélité</h1>
    <p>Nom: {{ $user->nom }}</p>
    <p>Prénom: {{ $user->prenom }}</p>
    <p>Login: {{ $user->login }}</p>
    {{-- <img src="{{ $qrCodeBase64 }}" alt="QR Code"> --}}
</body>
</html>
