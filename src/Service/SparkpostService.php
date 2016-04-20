<?php
/**
 * Part of the ETD Framework Email Package
 *
 * @copyright   Copyright (C) 2016 ETD Solutions. Tous droits réservés.
 * @license     Apache License 2.0; see LICENSE
 * @author      ETD Solutions http://etd-solutions.com
 */

namespace EtdSolutions\Email\Service;

use EtdSolutions\Email\AbstractService;

class SparkPostService extends AbstractService {

    /**
     * @var \SparkPost\SparkPost
     */
    private $sparky;

    /**
     * @var array Inline recipient objects
     */
    protected $recipients = [];

    /**
     * @var array Key/value pairs that are provided to the substitution engine
     */
    protected $substitution_data = [];

    /**
     * @var array
     */
    protected $from = [];

    /**
     * @var array
     */
    protected $images = [];

    /**
     * @var array
     */
    protected $attachments = [];

    /**
     * @var array Options Attributes
     */
    protected $transmission_options = [
        "transactional" => true,
        "trackOpens"    => true,
        "trackClicks"   => true,
        "inlineCss"     => false
    ];

    public static function isSupported() {

        return (
            class_exists("\\SparkPost\\SparkPost") &&
            class_exists("\\GuzzleHttp\\Client") &&
            class_exists("\\Ivory\\HttpAdapter\\Guzzle6HttpAdapter")
        );

    }

    public function __construct($options) {

        parent::__construct($options);

        $httpAdapter  = new \Ivory\HttpAdapter\Guzzle6HttpAdapter(new \GuzzleHttp\Client());
        $this->sparky = new \SparkPost\SparkPost($httpAdapter, [
            'key' => $options['key']
        ]);

    }

    public function setRecipients($recipients) {

        $this->recipients = [];

        foreach ($recipients as $recipient) {
            $this->recipients[$recipient["email"]] = [
                "address" => [
                    "email"     => $recipient["email"],
                    "name"      => $recipient["name"],
                    "header_to" => $recipient["bcc"] ? "undisclosed-recipients:;" : null
                ]
            ];

        }

        return $this;

    }

    public function setGlobalData($globalData) {

        $this->substitution_data = $globalData;

        return $this;
    }

    public function setRecipientsData($recipientsData) {

        foreach ($recipientsData as $email => $data) {

            if (!isset($this->recipients[$email])) {
                $this->recipients[$email] = [];
            }

            $this->recipients[$email]["substitution_data"] = $data;

        }

        return $this;

    }

    public function setFrom($from) {

        $this->from = $from["name"] . " <" . $from["email"] . ">";

        return $this;

    }

    public function setInlineImages($images) {

        $this->images = $images;

        return $this;

    }

    public function setAttachments($attachments) {

        $this->attachments = $attachments;

        return $this;

    }

    public function setSendOptions($options) {

        if (!empty($options)){
            $this->transmission_options = $options;
        }

        return $this;

    }

    public function setSendOption($key, $value) {

        $this->transmission_options[$key] = $value;

        return $this;
    }

    public function send() {

        $message = [
            "from"       => $this->from,
            "subject"    => $this->subject,
            "recipients" => array_values($this->recipients)
        ];

        if (!empty($this->html)) {
            $message["html"] = $this->html;
        }

        if (!empty($this->text)) {
            $message["text"] = $this->text;
        }

        $message = array_merge($message, $this->transmission_options);

        if (!empty($this->attachments)) {
            $message["attachments"] = $this->attachments;
        }

        if (!empty($this->images)) {
            $message["inlineImages"] = $this->images;
        }

        if (!empty($this->attachments)) {
            $message["attachments"] = $this->attachments;
        }

        if (!empty($this->substitution_data)) {
            $message["substitutionData"] = $this->substitution_data;
        }

        try {
            $this->results = $this->sparky->transmission->send($message);
        } catch (\Exception $e) {
            return false;
        }

        return true;

    }

}