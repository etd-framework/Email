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
 * Interface pour définir un fournisseur de service.
 */
interface ServiceInterface {

    /**
     * Teste si le service est supporté sur le système.
     *
     * @return  boolean  True en cas de succès, false sinon.
     */
    public static function isSupported();


    /**
     * Définit les destinataires.
     *
     * @param array $recipients
     *
     * @return ServiceInterface
     */
    public function setRecipients($recipients);

    /**
     * Définit les données globales au message.
     *
     * @param array $globalData
     *
     * @return ServiceInterface
     */
    public function setGlobalData($globalData);

    /**
     * Définit les méta-données au message.
     *
     * @param array $metaData
     *
     * @return ServiceInterface
     */
    public function setMetaData($metaData);

    /**
     * Définit les données spécifiques aux destinataires.
     *
     * @param array $recipientsData
     *
     * @return ServiceInterface
     */
    public function setRecipientsData($recipientsData);

    /**
     * Définit les méta-données spécifiques aux destinataires.
     *
     * @param array $recipientsMetaData
     *
     * @return ServiceInterface
     */
    public function setRecipientsMetaData($recipientsMetaData);

    /**
     * Définit les pièces joites.
     *
     * @param array $attachments
     *
     * @return ServiceInterface
     */
    public function setAttachments($attachments);

    /**
     * Définit les images inline.
     *
     * @param array $images
     *
     * @return ServiceInterface
     */
    public function setInlineImages($images);

    /**
     * Définit l'expéditeur.
     *
     * @param array $from
     *
     * @return ServiceInterface
     */
    public function setFrom($from);

    /**
     * Définit le sujet.
     *
     * @param string $subject
     *
     * @return ServiceInterface
     */
    public function setSubject($subject);

    /**
     * Définit les options d'envoi.
     *
     * @param array $options
     *
     * @return ServiceInterface
     */
    public function setSendOptions($options);

    /**
     * Définit une option d'envoi.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return ServiceInterface
     */
    public function setSendOption($key, $value);

    /**
     * Définit le contenu HTML de l'email.
     *
     * @param string $html
     *
     * @return ServiceInterface
     */
    public function setHTML($html);

    /**
     * Définit le contenu Texte de l'email.
     *
     * @param string $text
     *
     * @return ServiceInterface
     */
    public function setText($text);

    /**
     * Renvoi les résultats du dernier envoi.
     *
     * @return mixed Les résultats du dernier envoi.
     */
    public function getResults();

    /**
     * Envoi le message
     *
     * @return boolean True en cas de succès, false sinon.
     */
    public function send();

}