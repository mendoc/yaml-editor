/**
 * Created by hp on 29/04/2017.
 */

$(document).ready(function () {
    $('select').material_select();

    var data = [];
    var current_entite = {};
    var current_propriete = {};
    var current_entite_tag;
    var debug = false;

    $('#input_add_entite').keydown(function (e) {
        if (e.keyCode == 13){
            var nom_entite = $(this).val();
            nom_entite.trim();
            nom_entite = nom_entite.substring(0, 1).toUpperCase() + nom_entite.substring(1);

            if (nom_entite && nom_entite.indexOf(' ') == -1){

                var entite = {};
                entite['nom']     = nom_entite;
                entite['options'] = {"timestampable":true,"softdelete":false};
                entite['proprietes'] = [];
                entite['relations'] = [];
                data.push(entite);

                $("#all_entites").append('<li class="entite"><a><i class="material-icons hide">delete</i><span>'+nom_entite+'</span></a></li>');
                $('#btn_voir_fichier').removeClass('hide');
                //Materialize.toast(JSON.stringify(data));
                //Materialize.toast("Entité " + nom_entite + " ajoutée",2000);
                $(this).val('');

                var rels = $('.div_relation');
                rels.find('select:last').append('<option>'+nom_entite+'</option>');
                $('select').material_select();

                $('.entite').off('click');
                $('.entite').click(function () {

                    $('.entite').removeClass('active');
                    current_entite_tag = $(this).addClass('active');
                    current_entite = getEntite(current_entite_tag.find('span').text());

                    $('#input_nom_entite').val(current_entite.nom);
                    $('#check_timestampable').prop('checked', current_entite.options.timestampable);
                    $('#check_softdelete').prop('checked', current_entite.options.softdelete);

                    $('.div-propriete').remove();
                    $('.div_relation').remove();

                    var props = current_entite.proprietes;
                    for (i = 0; i < props.length; i++){
                        var prop = props[i];
                        ajouterPropriete(prop);
                    }
                    var rels = current_entite.relations;
                    for (i = 0; i < rels.length; i++){
                        var rel = rels[i];
                        ajouterRelation(rel);
                    }
                    
                    $('#content').removeClass('hide');
                    $('#libelle-prop').focus();

                    statut(current_entite);

                    //Materialize.toast(JSON.stringify(current_entite), 5000);
                });
            }
        }
    });

    $('.item_entite').click(function () {
        Materialize.toast("Frangin", 5000);
    });

    $('#type-prop').change(function () {
        message($(this).val());
        ajouterPropriete();
    });

    /*$('#libelle-prop').keydown(function () {
        ajouterPropriete();
    });*/

    $('.prop').change(function () {
        message($(this).val());
        ajouterPropriete();
    });

    $('#input_nom_entite').blur(function () {
        modifierNomEntite($(this).val());
    });

    $('#input_nom_entite').keydown(function (e) {
        if (e.keyCode == 13) modifierNomEntite($(this).val());
    });

    $('#check_timestampable').click(function () {
        current_entite.options.timestampable = $(this).prop('checked');
        statut(current_entite);
    });
    $('#check_softdelete').click(function () {
        current_entite.options.softdelete = $(this).prop('checked');
        statut(current_entite);
    });

    $('#btn_add_relation').click(function () {
        ajouterRelation();
        statut(current_entite);
    });
    
    $('#btn_voir_fichier').click(function () {
        if($(this).hasClass('retour')) {
            $(this).removeClass('retour');
            $(this).text('Vérifier');
            cacher(['preview']);
            afficher(['statut', 'content', 'all_entites']);
            $('.brand-logo').prop('href', '.');
            $('#libelle-prop').focus();
            return;
        }
        $.post("app/handler.php", {data : JSON.stringify(data), action : 'voir'}, function (rep, statut) {
            if (statut == 'success'){
                $('#text_content').text(rep);
                cacher(['statut', 'content', 'all_entites']);
                afficher(['preview']);
                $('#btn_voir_fichier').text('Retour');
                $('#btn_voir_fichier').addClass('retour');
                //Materialize.toast(rep,2000);
                $('.brand-logo').removeAttr('href');
            }
        });
    });

    function getEntite(nom) {
        var curr;
        for (i = 0; i < data.length; i++){
            curr = data[i];
            if (curr.nom == nom){
                return curr;
            }
        }
        return null;
    }

    function statut(msg) {
        if (!debug) return;
        $('#statut').text(JSON.stringify(msg));
    }
    function message(msg) {
        if (!debug) return;
        $('#statut').text(msg);
    }

    function modifierNomEntite(nom) {
        nom.trim();
        nom = nom.substring(0, 1).toUpperCase() + nom.substring(1);
        current_entite.nom = nom;
        current_entite_tag.find('span').text(nom);
        $('#input_nom_entite').val(nom);
        statut(current_entite);
    }

    function cacher(ids) {
        for (i = 0; i < ids.length; i++){
            $('#' + ids[i]).addClass('hide');
        }
    }

    function afficher(ids) {
        for (i = 0; i < ids.length; i++){
            $('#' + ids[i]).removeClass('hide');
        }
    }

    function ajouterPropriete(prop) {
        var token = Date.now();

        if (prop){
            $('#form_proprietes').append(
                '<div id="div-propriete-'+token+'" class="row div-propriete"><div class="input-field col s3"><input id="libelle-prop-'+token+'" type="text" class="prop"><label for="libelle-prop-'+token+'">Libellé</label></div><div class="input-field col s3"><select id="type-prop-'+token+'"><option value="string">String</option><option value="integer">Integer</option><option value="date">Date</option><option value="datetime">Datetime</option></select><label for="type-prop-'+token+'">Type</label></div><div class="input-field col s2"><input type="number" id="taille-prop-'+token+'" class="prop"><label for="taille-prop-'+token+'">Taille</label></div><div class="input-field col s3"><input id="requis-prop-'+token+'" type="checkbox" class="filled-in prop" checked/><label for="requis-prop-'+token+'">Obligatoire</label></div><div class="input-field col s1"><a id="supp-prop-'+token+'" class="btn-flat"><i class="material-icons red-text">delete</i></a></div></div>'
            );
        }else{
            $('#form_proprietes').append(
                '<div id="div-propriete-'+token+'" class="row div-propriete"><div class="input-field col s3"><input id="libelle-prop-'+token+'" type="text" class="prop"><label for="libelle-prop-'+token+'">Libellé</label></div><div class="input-field col s3"><select id="type-prop-'+token+'"><option value="string">String</option><option value="integer">Integer</option><option value="date">Date</option><option value="datetime">Datetime</option></select><label for="type-prop-'+token+'">Type</label></div><div class="input-field col s2"><input type="number" id="taille-prop-'+token+'" class="prop"><label for="taille-prop-'+token+'">Taille</label></div><div class="input-field col s3"><input id="requis-prop-'+token+'" type="checkbox" class="filled-in prop" checked/><label for="requis-prop-'+token+'">Obligatoire</label></div><div class="input-field col s1"><a id="supp-prop-'+token+'" class="btn-flat"><i class="material-icons red-text">delete</i></a></div></div>'
            );
        }
        var champ_libelle = $('#libelle-prop-'+token);
        var champ_taille  = $('#taille-prop-'+token);
        var champ_type    = $('#type-prop-'+token);
        var btn_check     = $('#requis-prop-'+token);

        if (!prop){

            var nom_prop = $('#libelle-prop').val();

            champ_libelle.val(nom_prop);
            champ_taille.val($('#taille-prop').val());
            champ_type.val($('#type-prop').val());
            //champ_libelle.focus();
            champ_libelle.parent().find('label').prop('class', 'active');
            if(champ_taille.val()) champ_taille.parent().find('label').prop('class', 'active');

            var propriete = {};
            propriete.nom         = nom_prop;
            propriete.type        = 'string';
            propriete.taille      = null;
            propriete.obligatoire = true;
            current_entite.proprietes.push(propriete);

            var props = $('.div_relation');
            props.find('select:first').append('<option>'+nom_prop+'</option>');

            statut(current_entite);

            $('#libelle-prop').val('');
            $('#taille-prop').val('');
            $('#type-prop').val('string');
        }else{
            champ_libelle.val(prop.nom);
            champ_taille.val(prop.taille);
            champ_type.val(prop.type);
            if (prop.obligatoire) btn_check.prop('checked', 'true');
            else btn_check.removeAttr('checked');
            champ_libelle.parent().find('label').prop('class', 'active');
            if(champ_taille.val()) champ_taille.parent().find('label').prop('class', 'active');
        }

        $('select').material_select();

        champ_libelle.change(function () {
            recupererProprietes();
        });

        champ_type.change(function () {
            recupererProprietes();
        });
        champ_taille.change(function () {
            recupererProprietes();
        });
        btn_check.click(function () {
            recupererProprietes();
        });
        $('#supp-prop-'+token).click(function () {
            $('#div-propriete-'+token).addClass('red lighten-5');
            setTimeout(function () {
                $('#div-propriete-'+token).remove();
                recupererProprietes();
            }, 800);
        });
    }

    function ajouterRelation(rel) {
        var token = Date.now();

        $('#form_relations').append(
            '<div id="div_relation-'+token+'" class="row div_relation"><div class="input-field col s5"><select id="cle-'+token+'"><option value="" disabled selected>Choisir la clé étrangère</option></select><label for="cle-'+token+'">Clé étrangère</label></div><div class="input-field col s5"><select id="classe-'+token+'"><option disabled selected>Choisir l\'entité</option></select><label for="classe-'+token+'">En relation avec</label></div><div class="input-field col s1 offset-s1"><a id="btn_supp_relation-'+token+'" class="btn-flat"><i class="material-icons">delete</i></a></div></div>'
        );

        var champ_cle    = $('#cle-'+token);
        var champ_classe = $('#classe-'+token);

        champ_cle.change(function () {
            recupererRelations();
        });
        champ_classe.change(function () {
            recupererRelations();
        });

        for (i = 0; i < current_entite.proprietes.length; i++){
            var curr_prop = current_entite.proprietes[i];
            champ_cle.append('<option>'+curr_prop.nom+'</option>');
        }

        for (i = 0; i < data.length; i++){
            var curr_ent = data[i];
            if (current_entite.nom != curr_ent.nom)
                champ_classe.append('<option>'+curr_ent.nom+'</option>');
        }

        if (rel){
            champ_cle.val(rel.cle);
            champ_classe.val(rel.classe);
        }
        $('select').material_select();

        $('#btn_supp_relation-'+token).click(function () {
            $('#div_relation-'+token).addClass('red lighten-5');
            setTimeout(function () {
                $('#div_relation-'+token).remove();
                recupererRelations();
            }, 800);
        });

    }

    function recupererProprietes() {
        var props = $('.div-propriete');
        var curr, vals;
        var out = "noms : ";
        current_entite.proprietes = [];
        for (i = 0; i < props.length; i++){
            curr = props.eq(i);
            vals = curr.find('.prop');
            var propriete = {};
            propriete.nom         = vals.eq(0).val();
            propriete.type        = curr.find('select').val();
            propriete.taille      = vals.eq(1).val();
            propriete.obligatoire = vals.eq(2).prop("checked");
            current_entite.proprietes.push(propriete);
        }
        statut(current_entite);
    }

    function recupererRelations() {
        var rels = $('.div_relation');
        var curr, vals;
        var out = "noms : ";
        current_entite.relations = [];
        for (i = 0; i < rels.length; i++){
            curr = rels.eq(i);
            vals = curr.find('select');
            var relation = {};
            relation.cle    = vals.eq(0).val();
            relation.classe = vals.eq(1).val();
            if (relation.cle && relation.classe)
                current_entite.relations.push(relation);
        }
        statut(current_entite);
    }

});