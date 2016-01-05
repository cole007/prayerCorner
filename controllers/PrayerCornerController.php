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
        $email = craft()->request->getPost('fromEmail');
        $entryId = craft()->request->getPost('entryId');

        $PrayerCornerRecord = PrayerCornerRecord::model()->findByAttributes(array('entryId' => $entryId,'email'=>$email));

        $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i';
        if (preg_match( $regex, trim($email) ) == 0) {
            // check if valid email
            $errorMessage =  'Invalid email';
        } elseif (count(craft()->entries->getEntryById($entryId)) == 0) {
            // check if valid entry
            $errorMessage =  'Invalid entry';
        } elseif(!$PrayerCornerRecord) {
            // check if email/entry relationship exists
            $PrayerCornerRecord= new PrayerCornerRecord;
            $PrayerCornerRecord->setAttribute('entryId', $entryId);
            $PrayerCornerRecord->setAttribute('email', $email);
            // write to database
            $PrayerCornerRecord->save();
            $uid = $PrayerCornerRecord->uid;
            $unsubscribe = UrlHelper::getActionUrl('PrayerCorner/Unsubscribe',$uid);
            // send confirmation email?
//            echo entry;
            $entry = craft()->entries->getEntryById($entryId);
//            echo UrlHelper::getActionUrl('PrayerCorner/Unsubscribe',$uid);
        } else {
            $errorMessage =  'Already subscribed';
            // $id = $PrayerCornerRecord->id;
            // $entry = craft()->entries->getEntryById($entryId);
            $uid = $PrayerCornerRecord->uid;
        }

        if (isset($errorMessage)) {
            craft()->userSession->setError(Craft::t($errorMessage));

            $entry = craft()->entries->getEntryById($entryId);
            // log success (entry_id)
            PrayerCornerPlugin::log(
                'User (' . $email . ') unsuccessfully registered to ' . $entry->title . ' ('.$errorMessage.')',
                LogLevel::Info,
                true
            );

            craft()->urlManager->setRouteVariables(array(
                'email' => $email,
                'error' => $errorMessage
            ));
            // log error (entry_id)
        } else {
            craft()->userSession->setNotice(Craft::t('Subscribed!'));
            craft()->userSession->setFlash('message', 'Subscribed!');

            $entry = craft()->entries->getEntryById($entryId);
            // log success (entry_id)
            PrayerCornerPlugin::log(
                'User (' . $email . ') successfully registered to ' . $entry->title,
                LogLevel::Info,
                true
            );

            $this->redirect(craft()->request->getPost('redirect'));

        }
        // SetError
        // craft()->userSession->setError( Craft::t('There was a problem with your submission, please check the form and try again!') );


    }
    public function actionUnsubscribe()
    {
        // check if email/entry relationship exists
        // remove from database
//        print_r(craft()->request);
        $uid = craft()->request->getQueryStringWithoutPath();
        $uid = substr($uid,0,-1);
        $PrayerCornerRecord = PrayerCornerRecord::model()->findByAttributes(array('uid' => $uid));
        $entryId = $PrayerCornerRecord->entryId;
        $entry = craft()->entries->getEntryById($entryId);
        $PrayerCornerRecord->delete();
        craft()->userSession->setFlash('message', 'You have unsubscribed from notifications!');
        $this->redirect($entry->getUrl());
        // log unsubscribe (entry_id)
    }
}