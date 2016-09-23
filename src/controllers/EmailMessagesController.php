<?php
/**
 * @link      https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license   https://craftcms.com/license
 */

namespace craft\app\controllers;

use Craft;
use craft\app\models\RebrandEmail;
use craft\app\web\Controller;
use yii\web\Response;

Craft::$app->requireEdition(Craft::Client);

/**
 * The EmailMessagesController class is a controller that handles various email message tasks such as saving email
 * messages.
 *
 * Note that all actions in the controller require an authenticated Craft session via [[Controller::allowAnonymous]].
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since  3.0
 */
class EmailMessagesController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        // All email message actions require an admin
        $this->requireAdmin();
    }

    /**
     * Returns the HTML for an email message modal.
     *
     * @return Response
     */
    public function actionGetMessageModal()
    {
        $this->requireAjaxRequest();

        $request = Craft::$app->getRequest();
        $key = $request->getRequiredBodyParam('key');
        $localeId = $request->getBodyParam('locale');
        $message = Craft::$app->getEmailMessages()->getMessage($key, $localeId);

        return $this->renderTemplate('settings/email/_message_modal', [
            'message' => $message,
        ]);
    }

    /**
     * Saves an email message.
     *
     * @return Response
     */
    public function actionSaveMessage()
    {
        $this->requirePostRequest();
        $this->requireAjaxRequest();

        $message = new RebrandEmail();
        $message->key = Craft::$app->getRequest()->getRequiredBodyParam('key');
        $message->subject = Craft::$app->getRequest()->getRequiredBodyParam('subject');
        $message->body = Craft::$app->getRequest()->getRequiredBodyParam('body');

        if (Craft::$app->getIsLocalized()) {
            $message->locale = Craft::$app->getRequest()->getBodyParam('locale');
        } else {
            $message->locale = Craft::$app->language;
        }

        if (Craft::$app->getEmailMessages()->saveMessage($message)) {
            return $this->asJson(['success' => true]);
        }

        return $this->asErrorJson(Craft::t('app', 'There was a problem saving your message.'));
    }
}
