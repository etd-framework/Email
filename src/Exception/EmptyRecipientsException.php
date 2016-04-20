<?php
/**
 * Part of the ETD Framework Email Package
 *
 * @copyright   Copyright (C) 2016 ETD Solutions. Tous droits réservés.
 * @license     Apache License 2.0; see LICENSE
 * @author      ETD Solutions http://etd-solutions.com
 */

namespace EtdSolutions\Email\Exception;

/**
 * Aucun destinataire n'a été trouvé dans l'email.
 */
class EmptyRecipientsException extends \InvalidArgumentException {}
