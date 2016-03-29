<?php
/**
 * Part of the ETD Framework Email Package
 *
 * @copyright   Copyright (C) 2016 ETD Solutions. Tous droits réservés.
 * @license     Apache License 2.0; see LICENSE
 * @author      ETD Solutions http://etd-solutions.com
 */

namespace EtdSolutions\Email;

use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;

class Email implements ContainerAwareInterface {

    use ContainerAwareTrait;

    /**
     * @var array Un tableau associatif des destinataires.
     */
    protected $recipients;

    /**
     * @var array Un tableau associatif de l'expéditeur.
     */
    protected $from;

    /**
     * @var string Le layout à utiliser pour générer le contenu de l'email.
     */
    protected $layout;

    /**
     * @var ServiceInterface Le fournisseur de service.
     */
    protected $service;

    /**
     * Constructeur
     *
     * @param \Joomla\DI\Container $container Le container DI
     * @param ServiceInterface     $service   Le fournisseur de service. Facultatif. Si non donné, on prend celui par défaut dans la configuration.
     */
    public function __construct($container, $service = null) {

        $this->setContainer($container);

        if (isset($service)) {
            $this->service = $service;
        } else {

        }

    }

    /**
     * Efface les destinataires.
     *
     * @return  Email
     */
    public function clearRecipients() {

        $this->recipients = [];

        return $this;
    }

    /**
     * Donne les destinataires.
     *
     * @return array
     */
    public function getRecipients() {

        return $this->recipients;
    }

    /**
     * @param array $recipients
     *
     * @return Email
     */
    public function setRecipients($recipients) {

        $this->recipients = $recipients;

        return $this;
    }

    /**
     * Ajoute un destinataire
     *
     * @param string $email L'adresse Email
     * @param string $name  Le nom du destinataire
     * @param bool   $bcc   Si true, le destinataire sera en copie cachée.
     *
     * @return $this
     */
    public function addRecipient($email, $name, $bcc = false) {

        if (!array_key_exists($email, $this->recipients)) {
            $this->recipients[$email] = [
                'email' => $email,
                'name'  => $name,
                'bcc'   => $bcc
            ];
        }

        return $this;

    }

    /**
     * Spéficie l'expéditeur du message.
     * Si aucune expéditeur n'est défini, on prendra celui de la configuration.
     *
     * @param string $email L'adresse email de l'expéditeur.
     * @param string $name  Le nom (facultatif) de l'expéditeur.
     *
     * @return Email
     */
    public function setFrom($email, $name = null) {

        $this->from = [
            'email' => $email,
            'name'  => $name
        ];

        return $this;

    }

    /**
     * Donne l'expéditeur du message.
     * Si aucun expéditeur n'a été défini on renvoi celui de la configuration.
     *
     * @return array L'expéditeur
     */
    public function getFrom() {

        if (!isset($this->from)) {
            return $this->getContainer()->get('config')->extract('email.from')->toArray();
        }

        return $this->from;
    }

    /**
     * Donne le layout utilisé pour le contenu de l'email.
     *
     * @return string
     */
    public function getLayout() {

        return $this->layout;
    }

    /**
     * Défini le layout pour le contenu de l'email.
     *
     * @param string $layout
     *
     * @return $this
     */
    public function setLayout($layout) {

        $this->layout = $layout;

        return $this;
    }





}