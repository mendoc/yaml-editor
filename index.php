<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 27/04/2017
 * Time: 21:03
 */

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Yaml Editor</title>
    <meta charset="utf-8">
    <!--Import Google Icon Font-->
    <link href="assets/css/material-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="assets/libs/materialize/css/materialize.min.css"
          media="screen,projection"/>

    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>

<body>

<nav style="position: relative;z-index: 9;">
    <div class="nav-wrapper">
        <a href="." class="brand-logo">
            &nbsp;&nbsp;&nbsp;Yaml Editor
        </a>
        <ul class="right hide-on-med-and-down">
            <li><a id="btn_voir_fichier" class="waves-effect waves-light btn hide">Vérifier</a></li>
        </ul>
    </div>
</nav>
<ul id="all_entites" class="side-nav fixed">
    <li style="padding: 15px 20px;">
        <input id="input_add_entite" type="text" autofocus placeholder="Nouvelle entité">
    </li>
    <!--li class="active"><a href="#!"><i class="material-icons red-text">delete</i>Specialite</a></li-->
</ul>
<!-- Page Layout here -->
<div class="">
    <div id="preview" class="row hide">
        <div class="row">
            <form class="col s12 m8 offset-l3">
                <div class="row">
                    <div class="input-field">
                        <pre id="text_content">Libellé</pre>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div id="content" class="row hide">
        <div class="col s12 m8 l6 offset-l4"> <!-- Note that "m8 l9" was added -->
            <br>
            <div class="input-field">
                <input id="input_nom_entite" type="text" value="Nom de l'entité">
                <label for="input_nom_entite">Nom de l'entité</label>
            </div>
            <br>
            <div class="col s12">
                <ul class="tabs tabs-fixed-width">
                    <li class="tab"><a class="active" href="#tab_proprietes">Propriétes</a></li>
                    <li class="tab"><a href="#tab_relations">Relations</a></li>
                    <li class="tab"><a href="#tab_options">Options</a></li>
                </ul>
            </div>
            <div id="tab_proprietes" class="col s12">
                <br>
                <div class="row">
                    <form id="form_proprietes" class="col s12">
                        <div id="static-prop" class="row">
                            <div class="input-field col s3">
                                <input id="libelle-prop" type="text" class="prop">
                                <label for="libelle-prop">Libellé</label>
                            </div>
                            <div class="input-field col s3 hide">
                                <select id="type-prop">
                                    <option value="string">String</option>
                                    <option value="integer">Integer</option>
                                    <option value="date">Date</option>
                                    <option value="datetime">Datetime</option>
                                </select>
                                <label for="type-prop">Type</label>
                            </div>
                            <div class="input-field col s2 hide">
                                <input type="number" id="taille-prop" class="prop">
                                <label for="taille-prop">Taille</label>
                            </div>
                            <div class="input-field col s3 hide">
                                <input type="checkbox" id="requis-prop" class="filled-in prop" checked/>
                                <label for="requis-prop">Obligatoire</label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div id="tab_relations" class="col s12">
                <br>
                <div class="row">
                    <br><a id="btn_add_relation" class="waves-effect waves-light btn-flat green white-text">Ajouter</a><br><br>
                    <form id="form_relations" class="col s12"></form>
                </div>
            </div>
            <div id="tab_options" class="col s12">
                <br><br>
                <form action=".">
                    <p>
                        <input type="checkbox" class="filled-in" id="check_timestampable"/>
                        <label for="check_timestampable">Ajouter les champs <strong
                                class="deep-orange-text">created_at</strong> et <strong class="deep-orange-text">updated_at</strong></label>
                    </p>
                    <p>
                        <input type="checkbox" class="filled-in" id="check_softdelete"/>
                        <label for="check_softdelete">Ajouter un champ <strong
                                class="deep-orange-text">deleted_at</strong></label>
                    </p>
                </form>
            </div>
        </div>
        <div class="row" style="position: absolute; bottom: 12px; right: 10px;">
            <div class="col offset-l3">
                <pre>
                    <code id="statut"></code>
                </pre>
            </div>
        </div>
    </div>
</div>
<!--Import jQuery before materialize.js-->
<script type="text/javascript" src="assets/js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="assets/libs/materialize/js/materialize.min.js"></script>
<script type="text/javascript" src="assets/js/app.js"></script>
</body>
</html>
