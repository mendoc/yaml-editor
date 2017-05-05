<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 29/04/2017
 * Time: 18:14
 */

require_once "YamlEditor.php";

//echo '<pre>';
//print_r($editor->getData());
//echo '</pre>';

if (isset($_POST['data'])){

    //$data = '[{"nom":"User","proprietes":[{"nom":"login","type":"string","taille":30,"obligatoire":true},{"nom":"pass","type":"string"}]},{"nom":"Patient","options":{"timestampable":true,"softdelete":false},"proprietes":[{"nom":"nom","type":"string","taille":30,"obligatoire":true},{"nom":"prenom","type":"string","taille":30,"obligatoire":false}],"relations":[{"cle":"id_user","classe":"User"},{"cle":"id_type","classe":"Type"}]}]';

    $data = $_POST['data'];

    $editor = new YamlEditor($data);

    if (isset($_POST['action'])){

        $action = $_POST['action'];

        switch ($action){
            case 'creer':
                if ($editor->creerFichier()){
                    echo "Cool ! Fichier bien créé";
                }else{
                    echo "Erreur : Fichier non créé";
                }
                break;
            case 'voir':
                if ($editor->creerFichier()){
                    echo $editor->getContenu();
                }else{
                    echo "Erreur : Fichier non créé";
                }
                break;
            default:
                echo 'Opération inconnue';
                break;
        }
    }

}
