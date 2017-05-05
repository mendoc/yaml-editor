<?php

/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 4/28/2017
 * Time: 10:53 AM
 */

define('OUTPUT_PATH', "");
define('FILENAME',    "schema.yml");
define('TAB', "  ");

class YamlEditor
{
    private $data;

    function __construct($data_in_string = "")
    {
        $this->data = array();
        if (!empty($data_in_string)) $this->parseJSON($data_in_string);
    }

    public function parseJSON($json)
    {
        $data = json_decode($json, true);
        for ($i = 0; $i<count($data); $i++){
            $params = $data[$i];
            array_push($this->data, new Entite($params));
        }
    }

    public function getData()
    {
        return $this->data;
    }

    public function getEntites()
    {
        $out = array();
        for ($i = 0; $i<count($this->data); $i++){
            $entite = $this->data[$i];
            array_push($out, $entite->getNom());
        }
        return $out;
    }
    
    public function getContenu()
    {
        $file = OUTPUT_PATH . FILENAME;
        return file_get_contents($file);
    }

    public function creerFichier()
    {
        $file = OUTPUT_PATH . FILENAME;
        $content = "";

        for ($i = 0; $i<count($this->data); $i++){
            $entite = $this->data[$i];

            // On insère le nom de l'entité
            if ($i > 0) $content .= PHP_EOL . PHP_EOL;
            $content .= $entite->getNom() . ':';

            // On insère les potentielles options
            if ($entite->isSoftdelete() or $entite->isTimestampable())
            {
                $content .= PHP_EOL . TAB . 'actAs: [';

                if ($entite->isTimestampable())
                {
                    $content .= 'Timestampable';
                }
                if ($entite->isSoftdelete())
                {
                    if ($entite->isTimestampable()) $content .= ', ';
                    $content .= 'SoftDelete';
                }

                $content .= ']';

            }

            // On insère les entités
            $proprietes = $entite->getProprietes();
            if (count($proprietes) > 0)
            {
                $content .= PHP_EOL . TAB . 'columns:';

                for ($j = 0; $j < count($proprietes); $j++)
                {
                    $propriete = $proprietes[$j];
                    $content .= PHP_EOL . TAB . TAB . $propriete->getNom() . ':';
                    $content .= PHP_EOL . TAB . TAB . TAB . 'type: ' . $propriete->getType();
                    if ($propriete->getTaille())
                        $content .= '(' . $propriete->getTaille() . ')';
                    if ($propriete->isObligatoire())
                        $content .= PHP_EOL . TAB . TAB . TAB . 'notnull: true';
                }

            }

            // On insère les potentielles relations
            $relations = $entite->getRelations();
            if (count($relations) > 0)
            {
                $content .= PHP_EOL . TAB . 'relations:';

                for ($j = 0; $j < count($relations); $j++)
                {
                    $relation = $relations[$j];
                    $content .= PHP_EOL . TAB . TAB . $relation->getNom() . ':';
                    $content .= PHP_EOL . TAB . TAB . TAB . 'class: ' . $relation->getClasse();
                    $content .= PHP_EOL . TAB . TAB . TAB . 'local: ' . $relation->getLocal();
                    $content .= PHP_EOL . TAB . TAB . TAB . 'foreign: ' . $relation->getForeign();
                }

            }
        }

        return file_put_contents($file, $content);
    }
}

class Entite{

    private $nom;
    private $timestampable;
    private $softdelete;
    private $proprietes;
    private $relations;

    function __construct($data){

        // On récupère le nom de l'entité
        if (isset($data['nom'])) $this->nom = $data['nom'];

        // On récupère les options de l'entité
        if (isset($data['options']))
        {
            if (isset($data['options']['timestampable']))
                $this->timestampable = $data['options']['timestampable'];
            else
                $this->timestampable = true;

            if (isset($data['options']['softdelete']))
                $this->softdelete = $data['options']['softdelete'];
            else
                $this->softdelete = true;
        }

        // On récupère les propriétés de l'entité
        $this->proprietes = array();
        if (isset($data['proprietes']))
        {
            $props = $data['proprietes'];
            for ($i = 0; $i < count($props); $i++)
            {
                $prop = $props[$i];
                array_push($this->proprietes, new Propriete($prop));
            }
        }

        // On récupère les relations de l'entité
        $this->relations = array();
        if (isset($data['relations']))
        {
            $rels = $data['relations'];
            for ($i = 0; $i < count($rels); $i++)
            {
                $rel = $rels[$i];
                array_push($this->relations, new Relation($rel));
            }
        }

    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @return boolean
     */
    public function isTimestampable()
    {
        return $this->timestampable;
    }

    /**
     * @param boolean $timestampable
     */
    public function setTimestampable($timestampable)
    {
        $this->timestampable = $timestampable;
    }

    /**
     * @return boolean
     */
    public function isSoftdelete()
    {
        return $this->softdelete;
    }

    /**
     * @param boolean $softdelete
     */
    public function setSoftdelete($softdelete)
    {
        $this->softdelete = $softdelete;
    }

    /**
     * @return array
     */
    public function getProprietes()
    {
        return $this->proprietes;
    }

    /**
     * @param array $proprietes
     */
    public function setProprietes($proprietes)
    {
        $this->proprietes = $proprietes;
    }

    /**
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * @param array $relations
     */
    public function setRelations($relations)
    {
        $this->relations = $relations;
    }


}

class Propriete{

    private $nom;
    private $type;
    private $taille;
    private $obligatoire;

    function __construct($data){

        // On récupère le nom de la propriété
        if (isset($data['nom'])) $this->nom = $data['nom'];

        // On récupère le type de la propriété
        if (isset($data['type']))
            $this->type = $data['type'];
        else
            $this->type = 'string';

        // On récupère la taille de la propriété
        if (isset($data['taille']))
            $this->taille = $data['taille'];
        else
            $this->taille = null;

        // On récupère la propiété notnull
        if (isset($data['obligatoire']))
            $this->obligatoire = $data['obligatoire'];
        else
            $this->obligatoire = false;

    }

    /**
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return null
     */
    public function getTaille()
    {
        return $this->taille;
    }

    /**
     * @param null $taille
     */
    public function setTaille($taille)
    {
        $this->taille = $taille;
    }

    /**
     * @return boolean
     */
    public function isObligatoire()
    {
        return $this->obligatoire;
    }

    /**
     * @param boolean $obligatoire
     */
    public function setObligatoire($obligatoire)
    {
        $this->obligatoire = $obligatoire;
    }


}

class Relation{

    private $nom;
    private $classe;
    private $local;
    private $foreign;

    function __construct($data){

        // On récupère le nom de la relation
        if (isset($data['classe'])) $this->nom = strtolower($data['classe']);

        // On récupère la classe de la relation
        if (isset($data['classe'])) $this->classe = ucfirst($data['classe']);

        // On récupère la clé étrangère de la relation
        if (isset($data['cle'])) $this->local = $data['cle'];

        $this->foreign = "id";
    }

    /**
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @return string
     */
    public function getClasse()
    {
        return $this->classe;
    }

    /**
     * @param string $classe
     */
    public function setClasse($classe)
    {
        $this->classe = $classe;
    }

    /**
     * @return string
     */
    public function getLocal()
    {
        return $this->local;
    }

    /**
     * @param string $local
     */
    public function setLocal($local)
    {
        $this->local = $local;
    }

    /**
     * @return string
     */
    public function getForeign()
    {
        return $this->foreign;
    }

    /**
     * @param string $foreign
     */
    public function setForeign($foreign)
    {
        $this->foreign = $foreign;
    }

}

