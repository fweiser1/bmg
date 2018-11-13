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

// initialisation des variables
$strCode = '';
$strLibelle = ''; 
                    
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
    }
    break;
    case 'ajouterGenre' : 
    {        
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
                                $tabErreurs["Erreur"] = 'Il existe déjà un genre avec ce code !';
                                $hasErrors = true;  
                            }
                             else {
                                // une ou plusieurs valeurs n'ont pas été saisies
                                if (empty($strCode)) {                                
                                    $tabErreurs["Code"] = "Le code doit être renseigné !";
                                }
                                if (empty($strLibelle)) {
                                    $tabErreurs["Libellé"] = "Le libellé doit être renseigné !";
                                }
                                $hasErrors = true;
                             }
                             if(!$hasErrros)
                             {
                                 $code = GenreDal::addGenre($strCode,$strLibelle);
                                 if($code != NULL)
                                     {
                                        $msg = '<span class="info">Le genre '
                                        .$strCode.'-'
                                        .$strLibelle.' a été ajouté</span>';
                                        include 'vues/v_afficherMessage.php';
                                        // include 'vues/v_consulterGenre.php'';
                                     
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
                                    $lien='<a href="index.php?uc=c_gererGenres&action=ajouterGenre">Retours a la saisie</a>';
                                    include 'vues\_v_afficherErreurs.php';
                                 }
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
    }
    break;
    default : include "vues/_v_home.php";
      
}


