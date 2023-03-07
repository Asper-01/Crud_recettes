<?php
session_start(); // Démarrage de la session
require_once 'config.php'; // On inclut la connexion à la base de données

if (!empty($_POST['email']) && !empty($_POST['password'])) // Si il existe les champs email, password et qu'il sont pas vides
{
    // Patch XSS
    $email = htmlspecialchars($_POST['email']);   //eviter la faille Xss
    $password = htmlspecialchars($_POST['password']);  //faille Xss

    $email = strtolower($email); // email transformé en minuscule

    // On check si l'utilisateur est inscrit dans la table utilisateurs
    $check = $bdd->prepare('SELECT pseudo, email, password, token FROM utilisateurs WHERE email = ?');
    $check->execute(array($email));
    $data = $check->fetch();
    $row = $check->rowCount();



    // Si > à 0 alors l'utilisateur existe
    if ($row > 0) {
        // Si le mail est bon niveau format
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Si le mot de passe est le bon
            if (password_verify($password, $data['password'])) {
                // On crée la session et on redirige sur landing.php
                //on crée un booléen pour le boutton connexion déconnexion sur la navabar du header L14
                $_SESSION['connexion'] = true;
                $_SESSION['user'] = $data['token'];
                header('Location: landing.php');
                die();
            } else {
                header('Location: index.php?login_err=password');
                die();
            }
        } else {
            header('Location: index.php?login_err=email');
            die();
        }
    } else {
        header('Location: index.php?login_err=already');
        die();
    }
} else {
    header('Location: index.php');
    die();
} // si le formulaire est envoyé sans aucune données

?>

