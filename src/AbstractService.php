<?php
/**
 * Part of the ETD Framework Email Package
 *
 * @copyright   Copyright (C) 2016 ETD Solutions. Tous droits réservés.
 * @license     Apache License 2.0; see LICENSE
 * @author      ETD Solutions http://etd-solutions.com
 */

namespace EtdSolutions\Email;

/**
 * Classe de base pour définir un fournisseur de service.
 */
abstract class AbstractService implements ServiceInterface {

    protected $subject;

    /**
     * @var string Le contenu HTML.
     */
    protected $html;

    /**
     * @var string Le contenu Texte.
     */
    protected $text;

    protected $results;

    /**
     * @var array Un tableau d'options
     */
    protected $options = [];

    public function __construct($options) {
        $this->options = $options;
    }

    /**
     * Définit le contenu HTML de l'email.
     *
     * @param string $html
     *
     * @return ServiceInterface
     */
    public function setHTML($html) {

        $this->html = $html;

        return $this;

    }

    /**
     * Définit le contenu Texte de l'email.
     *
     * @param string $text
     *
     * @return ServiceInterface
     */
    public function setText($text) {

        $this->text = $text;

        return $this;

    }

    public function setSubject($subject) {

        $this->subject = $subject;

        return $this;
    }

    public function getResults() {
        return $this->results;
    }

}