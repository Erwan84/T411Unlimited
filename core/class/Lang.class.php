<?php 
abstract class Lang
{
    public static $api = array
    (
        "api_wrong_id" => array
        (
            "fr" => "Le nom de compte et/ou le mot de passe est incorrect !"
        ),
        "api_t411server_error" => array(
            "fr" => "Impossible se connecter au serveurs de T411"
        ),
        "api_access_granted" => array(
            "fr" => "Vous êtes connecté"
        ),
        "api_account_banned" => array(
            "fr" => "Votre compte est bannit !"
        ),
        "api_error_unknown" => array(
            "fr" => "Erreur inconnu"
        ),
        "api_torrent_not_found" => array(
            "fr" => "Ce torrent n'existe pas !"
        )
    );
    
    public static $uploadTorrent = array
    (
        "upload_wrong_extension" => array(
            "fr" => "L'extension du fichier est incorrect"
        ),
        "upload_wrong_mime" => array(
            "fr" => "Le MIME du fichier est incorrect"
        ),
        "upload_file_too_heavy" => array(
            "fr" => "Le fichier torrent ne doit pas dépasser les 10ko"
        ),
        "upload_file_error" => array(
            "fr" => "Erreur durant l'upload"
        ),
        "upload_filename_too_long" => array(
            "fr" => "Le nom du fichier est un peu long non ?"
        )
    );
    
}