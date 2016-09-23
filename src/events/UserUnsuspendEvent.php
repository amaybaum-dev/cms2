<?php
/**
 * @link      https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license   https://craftcms.com/license
 */

namespace craft\app\events;

use craft\app\elements\User;

/**
 * User unsuspend event class.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since  3.0
 */
class UserUnsuspendEvent extends Event
{
    // Properties
    // =========================================================================

    /**
     * @var User The user model associated with the event.
     */
    public $user;
}
