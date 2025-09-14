<?php
// Paramètres de connexion
$host = "localhost";
$dbname = "astiom_photography_cms";
$user = "root";      // <-- Mets ton user MySQL
$pass = "";          // <-- Mets ton mot de passe MySQL

try {
    // Connexion PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Exemple de données (à remplacer par un formulaire ou autre source)
    $username = "admin";
    $email = "admin@example.com";
    $password = "admin"; // Mot de passe en clair
    $is_active = 1;

    // Hash du mot de passe
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Date actuelle
    $created_at = date("Y-m-d H:i:s");

    // Requête d'insertion
    $sql = "INSERT INTO admin_users (username, email, password_hash, is_active, created_at, last_login)
            VALUES (:username, :email, :password_hash, :is_active, :created_at, NULL)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":username" => $username,
        ":email" => $email,
        ":password_hash" => $password_hash,
        ":is_active" => $is_active,
        ":created_at" => $created_at
    ]);

    echo "✅ Utilisateur ajouté avec succès !";

} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>