<?php
namespace Craft;
/**
 * Created by PhpStorm.
 * User: colehenley
 * Date: 04/01/2016
 * Time: 14:38
 */
class PrayerCornerController extends BaseController
{
    protected $allowAnonymous = true;
    public function actionSubscribe()
    {
        // get POST variables
        $email = craft()->request->getPost('fromEmail');
        $entryId = craft()->request->getPost('entryId');

        // get PC record from above
        $PrayerCornerRecord = PrayerCornerRecord::model()->findByAttributes(array('entryId' => $entryId,'email'=>$email));
        $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i';
        // check if valid email
        if (preg_match( $regex, trim($email) ) == 0) {
            $errorMessage =  'Invalid email';
        // check if valid entry
        } elseif (count(craft()->entries->getEntryById($entryId)) == 0) {
            $errorMessage =  'Invalid entry';
        // check if email/entry relationship exists
        } elseif(!$PrayerCornerRecord) {
            // create new PC Record
            $PrayerCornerRecord= new PrayerCornerRecord;
            $PrayerCornerRecord->setAttribute('entryId', $entryId);
            $PrayerCornerRecord->setAttribute('email', $email);
            // save PC record
            $PrayerCornerRecord->save();
            // get UID (for Unsubscribe)
            $uid = $PrayerCornerRecord->uid;
            // generate unsubscribe link
            $unsubscribe = UrlHelper::getActionUrl('PrayerCorner/Unsubscribe',$uid);
            $entry = craft()->entries->getEntryById($entryId);
        } else {
            $errorMessage =  'Already subscribed';
            $uid = $PrayerCornerRecord->uid;
        }

        if (isset($errorMessage)) {
            // set error message (Flash)
            craft()->userSession->setError(Craft::t($errorMessage));
            $entry = craft()->entries->getEntryById($entryId);
            // log error
            PrayerCornerPlugin::log(
                'User (' . $email . ') unsuccessfully subscribed to ' . $entry->title . ' ('.$errorMessage.')',
                LogLevel::Info,
                true
            );
            // redirect user and define variables sent
            craft()->urlManager->setRouteVariables(array(
                'email' => $email,
                'error' => $errorMessage
            ));
        } else {
            // set success messages
            craft()->userSession->setNotice(Craft::t('Subscribed!'));
            craft()->userSession->setFlash('message', 'Subscribed!');

            $recipient = $email;
            // define Email model
            $email = new EmailModel();
            $email->subject = 'You have subscribe to ' . $entry->title;
            $email->body = 'You have signed up to receive updates for ' . $entry->title . '<br />
To unsubscribe from these updates at any time please click on this link: ' . $unsubscribe;
            $email->toEmail = trim( $recipient );
            // Send the email
            craft()->email->sendEmail( $email );

            // log success
            PrayerCornerPlugin::log(
                'User (' . $recipient . ') has successfully subscribed to ' . $entry->title,
                LogLevel::Info,
                true
            );

            // redirect user
            $this->redirect(craft()->request->getPost('redirect'));

        }

    }
    public function actionUnsubscribe()
    {
        // get UID from URL
        $uid = craft()->request->getQueryStringWithoutPath();
        $uid = substr($uid,0,-1);
        // get PC model based on string
        $PrayerCornerRecord = PrayerCornerRecord::model()->findByAttributes(array('uid' => $uid));
        // if PC record exists
        if (count($PrayerCornerRecord)) {

            $entryId = $PrayerCornerRecord->entryId;
            // get entry record
            $entry = craft()->entries->getEntryById($entryId);
            // delete record
            $PrayerCornerRecord->delete();
            craft()->userSession->setFlash('message', 'You have unsubscribed from notifications!');
            // log unsubscribe
            PrayerCornerPlugin::log(
                'User (' . $PrayerCornerRecord->email . ') has successfully unsubscribed from ' . $entry->title,
                LogLevel::Info,
                true
            );
            // redirect user
            $this->redirect($entry->getUrl());
        } else {
            // log error
            // PrayerCornerPlugin::log(
                'Erroneous unsubscribe link followed (' . $uid. ')',
                LogLevel::Info,
                true
            );
            // redirect user to site root
            $this->redirect( craft()->getSiteUrl() );
        }

    }
}