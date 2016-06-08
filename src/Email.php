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

    const tagBlock = [
        '<%',
        '%>'
    ];

    const tagVariable = [
        '<=',
        '>'
    ];

    const base64Regexp = "^([A-Za-z0-9+/]{4})*([A-Za-z0-9+/]{4}|[A-Za-z0-9+/]{3}=|[A-Za-z0-9+/]{2}==)$";

    /**
     * @var array Un tableau associatif des destinataires.
     */
    protected $recipients = [];

    protected $subject;

    /**
     * @var array Un tableau associatif de l'expéditeur.
     */
    protected $from;

    /**
     * @var array Un tableau associatif des données globales à passer au service.
     */
    protected $globalData = [];

    /**
     * @var array Un tableau associatif des données spécifiques aux destinataires à passer au service
     */
    protected $recipientsData = [];

    /**
     * @var array Un tableau associatif des pièces joints associées au message.
     */
    protected $attachments = [];

    /**
     * @var array Un tableau associatif des images inline associées au message.
     */
    protected $inlineImages = [];

    /**
     * @var string Le layout à utiliser pour générer le contenu de l'email.
     */
    protected $layout;

    /**
     * @var ServiceInterface Le fournisseur de service.
     */
    protected $service;

    /**
     * @var array Un tableau associatif de paramètres à passer au service pour l'envoi du message.
     */
    protected $serviceOptions = [];

    /**
     * @var \Joomla\Renderer\RendererInterface Le renderer pour le layout.
     */
    protected $renderer;

    /**
     * @var array Un tableau associatif des données à passer au renderer.
     */
    protected $rendererData = [];

    /**
     * Constructeur
     *
     * @param \Joomla\DI\Container               $container Le container DI
     * @param \Joomla\Renderer\RendererInterface $renderer  Le renderer pour le layout.
     * @param ServiceInterface                   $service   Le fournisseur de service. Facultatif. Si non donné, on prend celui par défaut dans la configuration.
     */
    public function __construct($container, $renderer, $service = null) {

        $this->setContainer($container);
        $this->setRenderer($renderer);
        $this->setService($service);

    }

    /**
     * Méthode pour envoyer l'email.
     *
     * @throws Exception\EmptyRecipientsException Si il n'y a aucun destinataire au message.
     *
     */
    public function send() {

        // On teste la présence d'au moins un destinataire.
        if (empty($this->recipients)) {
            throw new Exception\EmptyRecipientsException;
        }

        // On passe le sujet.
        $this->service->setSubject($this->getSubject());

        // On passe l'expéditeur.
        $this->service->setFrom($this->getFrom());

        // On passe les destinataires.
        $this->service->setRecipients($this->getRecipients());

        // On passe les données globales.
        $this->service->setGlobalData($this->getGlobalData());

        // On passe les données spécifiques aux destinataires.
        $this->service->setRecipientsData($this->getRecipientsData());

        // On passe les images inline.
        $this->service->setInlineImages($this->getInlineImages());

        // On passe les pièces jointes.
        $this->service->setAttachments($this->getAttachments());

        // On passe les options d'envoi au service.
        $this->service->setSendOptions($this->getServiceOptions());

        // On effectue le rendu du layout.
        $content = $this->render();

        // On passe le contenu au service.
        $this->service->setHTML($content);

        // On envoi
        return $this->service->send();

    }

    /**
     * @return mixed
     */
    public function getSubject() {

        return $this->subject;
    }

    /**
     * @param mixed $subject
     *
     * @return Email
     */
    public function setSubject($subject) {

        $this->subject = $subject;

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
     * @return bool true s'il y a des destinataires, false sinon.
     */
    public function hasRecipients() {

        return count($this->recipients) > 0;
    }

    /**
     * @param array $recipients
     *
     * @return Email
     */
    public function setRecipients($recipients = []) {

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

        $this->recipients[$email] = [
            'email' => $email,
            'name'  => $name,
            'bcc'   => $bcc
        ];

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
            return $this->getContainer()
                        ->get('config')
                        ->extract('email.from')
                        ->toArray();
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

    /**
     * On définit le service pour l'envoi des emails.
     * Si aucun paramètre n'est fourni, on instancie le service défini dans la configuration.
     *
     * @param  ServiceInterface|string $service Une instance du service à utiliser ou le nom de la classe (facultatif)
     *
     * @return  $this
     *
     * @throws  \RuntimeException
     */
    public function setService($service = null) {

        if (is_null($service)) {
            $service = $this->getContainer()
                            ->get('config')
                            ->get('email.service.name');
        }

        // Si on a passé une chaine de caractère, c'est le nom de la classe à instancier.
        if (is_string($service)) {

            $class = '\\EtdSolutions\\Email\\Service\\' . ucfirst(strtolower($service)) . 'Service';
            if (!class_exists($class)) {
                throw new \RuntimeException(sprintf('Impossible de charger le service "%s"', $service));
            }

            // On récupère les options du service depuis la configuration.
            $options = $this->getContainer()
                            ->get('config')
                            ->extract('email.service.options');

            // On instancie le service.
            $service = new $class(isset($options) ? $options->toArray() : null);

        }

        // Si le service est une instance de l'interface, on vérifie s'il est supporté.
        if ($service instanceof ServiceInterface) {
            if (!$service::isSupported()) {
                throw new \RuntimeException(sprintf('Le service "%s" n\'est pas supporté dans cet environnement.', get_class($service)));
            }
        }

        $this->service = $service;

        return $this;
    }

    /**
     * @return ServiceInterface
     */
    public function getService() {

        return $this->service;
    }

    /**
     * Donne les services disponibles.
     *
     * @return array Un tableau des services disponibles.
     */
    public static function getServices() {

        $services = [];

        // On récupère un iterator et on boucle sur les classes des services.
        $iterator = new \DirectoryIterator(__DIR__ . '/Service');

        foreach ($iterator as $file) {

            $fileName = $file->getFilename();

            // On charge seulement les fichiers PHP.
            if (!$file->isFile() || $file->getExtension() != 'php') {
                continue;
            }

            // On déduit le nom de la classe du nom du fichier.
            $class = str_ireplace('.php', '', '\\EtdSolutions\\Email\\Service\\' . ucfirst(trim($fileName)));

            // Si la classe n'existe pas, on passe au suivant.
            if (!class_exists($class)) {
                continue;
            }

            // Cool! La classe existe, on vérifie que le service est supporté sur le système.
            if ($class::isSupported()) {
                $services[] = str_ireplace('Service.php', '', $fileName);
            }
        }

        return $services;
    }

    /**
     * Retourne le renderer.
     *
     * @return \Joomla\Renderer\RendererInterface
     */
    public function getRenderer() {

        return $this->renderer;
    }

    /**
     * Défini le renderer.
     *
     * @param \Joomla\Renderer\RendererInterface $renderer
     *
     * @return Email
     */
    public function setRenderer($renderer) {

        $this->renderer = $renderer;

        return $this;
    }

    /**
     * @return array
     */
    public function getGlobalData() {

        return $this->globalData;
    }

    /**
     * @param array $globalData
     *
     * @return Email
     */
    public function setGlobalData($globalData = []) {

        $this->globalData = $globalData;

        return $this;
    }

    /**
     * Ajoute une donnée globale.
     *
     * @param string $key   Le nom de la variable
     * @param mixed  $value La valeur
     *
     * @return $this
     */
    public function addGlobalData($key, $value) {

        $this->globalData[$key] = $value;

        return $this;

    }

    /**
     * @return array
     */
    public function getRecipientsData() {

        return $this->recipientsData;
    }

    /**
     * @param array $recipientsData
     *
     * @return Email;
     */
    public function setRecipientsData($recipientsData = []) {

        $this->recipientsData = $recipientsData;

        return $this;
    }

    /**
     * Ajoute une donnée spécifique à un destinataire.
     *
     * @param string       $email L'adresse email du destinataire.
     * @param array|string $key   Le nom de la variable ou directement le tableau.
     * @param mixed        $value La valeur
     *
     * @return $this
     */
    public function addRecipientData($email, $key, $value = null) {

        if (!isset($this->recipientsData[$email])) {
            $this->recipientsData[$email] = [];
        }

        if (is_array($key)) {
            $this->recipientsData[$email] = $key;
        } else {
            $this->recipientsData[$email][$key] = $value;
        }

        return $this;

    }

    /**
     * @return array
     */
    public function getRendererData() {

        return $this->rendererData;
    }

    /**
     * @param array $rendererData
     *
     * @return Email;
     */
    public function setRendererData($rendererData = []) {

        $this->rendererData = $rendererData;

        return $this;
    }

    /**
     * Ajoute une variable passée au renderer.
     *
     * @param string $key   Le nom de la variable
     * @param mixed  $value La valeur
     *
     * @return $this
     */
    public function addRendererData($key, $value) {

        $this->rendererData[$key] = $value;

        return $this;

    }

    /**
     * @return array
     */
    public function getAttachments() {

        return $this->attachments;
    }

    /**
     * @param array $attachments
     *
     * @return Email
     */
    public function setAttachments($attachments = []) {

        $this->attachments = $attachments;

        return $this;
    }

    /**
     * Ajoute une pièce jointe.
     *
     * @param string $type Le type MIME.
     * @param string $name Le nom de la pièce jointe.
     * @param string $data Les données encodées en base64.
     *
     * @return $this
     */
    public function addAttachment($type, $name, $data) {

        // Si les données ne ressemblent pas à une chaine encodée en base64, on les encode.
        if (!preg_match(Email::base64Regexp, $data)) {
            $data = base64_encode($data);
        }

        $this->attachments[] = [
            'type' => $type,
            'name' => $name,
            'data' => $data
        ];

        return $this;

    }

    /**
     * @return array
     */
    public function getInlineImages() {

        return $this->inlineImages;
    }

    /**
     * @param array $inlineImages
     *
     * @return Email
     */
    public function setInlineImages($inlineImages = []) {

        $this->inlineImages = $inlineImages;

        return $this;
    }

    /**
     * Ajoute une pièce jointe.
     *
     * @param string $type Le type MIME.
     * @param string $name Le nom de la pièce jointe.
     * @param string $data Les données encodées en base64.
     *
     * @return $this
     */
    public function addInlineImage($type, $name, $data) {

        // Si les données ne ressemblent pas à une chaine encodée en base64, on les encode.
        if (!preg_match(chr(1) . Email::base64Regexp . chr(1), $data)) {
            $data = base64_encode($data);
        }

        $this->inlineImages[] = [
            'type' => $type,
            'name' => $name,
            'data' => $data
        ];

        return $this;

    }

    /**
     * @return array
     */
    public function getServiceOptions() {

        return $this->serviceOptions;
    }

    /**
     * @param array $serviceOptions
     *
     * @return Email
     */
    public function setServiceOptions($serviceOptions = []) {

        $this->serviceOptions = $serviceOptions;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function addServiceOption($key, $value) {

        $this->serviceOptions[$key] = $value;
    }

    public function clear() {

        return $this->setRecipientsData()
                    ->setRecipients()
                    ->setAttachments()
                    ->setGlobalData()
                    ->setInlineImages()
                    ->setRendererData()
                    ->setServiceOptions();

    }

    public function getResults() {

        return $this->service->getResults();

    }

    /**
     * Méthode pour effectuer le rendu du layout.
     *
     * @return string Le rendu.
     */
    protected function render() {

        $this->renderer
                 ->getRenderer()
                 ->getLoader()
                 ->addPath(JPATH_TEMPLATES . '/emails');

        return $this->renderer->render($this->layout, $this->rendererData);

    }

}