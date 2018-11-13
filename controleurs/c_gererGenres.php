<?php
require_once("modele/GenreDal.class.php");
require_once("include/_reference.lib.php");

if(!isset($_REQUEST['action']))
{
    $action = 'listerGenres';
}
else
{
    $action = $_REQUEST['action'];
}

// varaiables pour la gestion des messages
$msg = ''; //message passé à la vue v_afficherMessage
$lien = ''; //message passé à la vue v_afficherErreurs

$tabErreurs = array();
$hasErrors = false;
$titrePage = 'Gestion des Genres';

                    
switch ($action) 
{
    case 'listerGenres' : 
    {        
        $lesGenres = GenreDal::loadGenres(1);
        $nbGenres = count($lesGenres);
        include ("vues/v_listeGenres.php");
    }
    break;
    case 'consulterGenre' : 
    {       
        if (isset($_GET["id"])){
            $strCode = strtoupper(htmlentities($_GET["id"]));
            // appel de la methode du modèle
            $leGenre = GenreDal::loadGenreByID($strCode);
            if ($leGenre == NULL)
            {
                $tabErreurs[] = 'Ce genre n\'existe pas ! ';
                $hasErrors = true;
            }
        }
        else  
        {
            $tabErreurs[] = "Aucun genre n'a été transmis pour consultation !";
            $hasErrors = true;
        }
        
        if ($hasErrors)
        {
            include 'vues/_v_afficherErreurs.php';
        }
        else
        {
            include 'vues/v_consulterGenre.php';
        }
    }
    break;
    case 'ajouterGenre' : 
    {        
        // initialisation des variables
                $strCode = '';
                $strLibelle = '';
        // traitement de l'option : saisie ou validation ?
            if (isset($_GET["option"])) {
                $option = htmlentities($_GET["option"]);
            } else {
                $option = 'saisirGenre';
            }
        switch ($option) {
            case 'saisirGenre' : 
            {
                include 'vues/v_ajouterGenre.php';
            } break;
            case 'validerGenre' : 
            {             
                    // tests de gestion du formulaire
                    if (isset($_POST["cmdValider"])) {
                        // récupération du libellé
                        if (!empty($_POST["txtLibelle"])) {
                            $strLibelle = ucfirst(htmlentities($_POST["txtLibelle"]));
                        }
                        if (!empty($_POST["txtCode"])) {
                            $strCode = strtoupper(htmlentities($_POST["txtCode"]));
                        }
                        // test zones obligatoires
                        if (!empty($strCode) and !empty($strLibelle)) {
                            // les zones obligatoires sont présentes
                            // tests de cohérence 
                            // contrôle d'existence d'un genre avec le même code
                            $doublon = GenreDal::loadGenreByID($strCode);
                            if ($doublon != NULL) 
                            {
                                $tabErreurs[] = 'Il existe déjà un genre avec ce code !';
                                $hasErrors = true;  
                            }
                        }
                             else 
                             {
                                // une ou plusieurs valeurs n'ont pas été saisies
                                if (empty($strCode)) {                                
                                    $tabErreurs[] = "Le code doit être renseigné !";
                                }
                                if (empty($strLibelle)) {
                                    $tabErreurs[] = "Le libellé doit être renseigné !";$hasErrors = true;
                                }
                                $hasErrors = true;
                             }
                             if(!$hasErrors)
                             {
                                 $res = GenreDal::addGenre($strCode,$strLibelle);
                                 if($res > 0)
                                     {
                                        $msg = '<span class="info">Le genre '
                                        .$strCode.'-'
                                        .$strLibelle.' a été ajouté</span>';
                                        include 'vues/_v_afficherMessage.php';
                                        // include 'vues/v_consulterGenre.php'';
                                        // $leGenre = new Genre($strCode, $strLibelle);
                                     }
                                     else 
                                     {
                                         $tabErreurs[]='Une erreur s\'est produite dans l\'operation d\'ajout !';
                                         $hasErrors = true;
                                     }
                             }
                             if($hasErrors)
                                 {
                                    $msg = "L'operation d'ajout n'a pas pu être menée a terme en raison des erreurs suivantes : ";
                                    $lien='<a href="index.php?uc=gererGenres&action=ajouterGenre">Retour à la saisie</a>';
                                    include 'vues\_v_afficherErreurs.php';
                                 }
                                 
            } break;
        }
        }
    }
    break;
    case 'modifierGenre' : 
    {        
    }
    break;
    case 'supprimerGenre' : 
    {        
         if (isset($_GET["id"])) {
                    $strCode = strtoupper(htmlentities($_GET["id"]));
                    $leGenre = GenreDal::loadGenreByID($strCode);
            if ($leGenre == null)
            {
                $tabErreurs[] = 'Ce genre n\'existe pas !';
                $hasErrors = true;
            }
            else
            {
                if (GenreDal::countOuvragesGenre($leGenre->getCode()) > 0)
                {
                    $tabErreurs[] = 'Il existe des ouvrages qui référencent ce genre, suppresion impossible';
                    $hesErrors = true;
                }
            }
         }
         
         else
         {
             $tabErreurs[] = "Aucun genre n'a été transmis pour suppresion ! ";
             $hesErrors = true;
         }
         if (!$hasErrors)
         {
             $res = GenreDal::delGenre($leGenre->getCode());
             if ($res > 0)
             {
                 $msg = 'Le genre'. $leGenre->getCode() . 'a été supprimé ';
                 //include 'vues/_v_afficherMessage.php';
                 $lesGenres = GenreDal::loadGenres(1);
                 $nbGenres = count($lesGenres);
                 include 'vues/v_listeGenres.php';
             }
             else 
             {
                 $tabErreurs[] = "Une erreur s\'est produite dans l\'opération de suppresion !";
                 $hesErrors = true;
             }
         }
         if($hasErrors)
         {
                $msg = "L'operation de suppression n'a pas pu être menée a terme en raison des erreurs suivantes : ";
                $lien='<a href="index.php?uc=gererGenres">Retour à la saisie</a>';
                include 'vues\_v_afficherErreurs.php';
         }
         
         
                 
        
    }
    break;
    default : include "vues/_v_home.php";
      
}


