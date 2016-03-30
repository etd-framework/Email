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

}