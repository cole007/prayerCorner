<?php
    namespace Craft;

/**
 * Entry Count Plugin
 */
class PrayerCornerPlugin extends BasePlugin
{
    public function getName()
    {
        return Craft::t('Prayer Corner');
    }

    public function getVersion()
    {
        return '1.0.0';
    }

    public function getDeveloper()
    {
        return '@cole007 (Cole Henley)';
    }

    public function getDeveloperUrl()
    {
        return 'http://ournameismud.co.uk';
    }

    public function hasCpSection()
    {
        return true;
    }
//    protected function defineSettings()
//    {
//        return array(
//            'showCountOnEntryIndex' => array(AttributeType::Bool, 'default' => 0),
//            'ignoreLoggedInUsers' => array(AttributeType::Bool, 'default' => 0),
//            'ignoreIpAddresses' => array(AttributeType::Mixed, 'default' => '')
//        );
//    }
//    public function getSettingsHtml()
//    {
//        return craft()->templates->render('prayercorner/settings', array(
//            'settings' => $this->getSettings()
//        ));
//    }


    // Hooks/events

    public function init()
    {
        parent::init();

        // Event: onBeforeSaveEntry
        craft()->on('entries.onBeforeSaveEntry', function(Event $event) {

            // check entry and declare relevant variables
            $entry = $event->params['entry'];
            $title= $entry->title;
            $id = $entry->id;
            $sectionId = $entry->sectionId;
            $enabled = $entry->enabled;

            // custom field to check against
            $relatedTo = $entry->relatedTo;

            // get parent entry from relationship
            $parent = craft()->entries->getEntryById($relatedTo);

            // declare body of email message
            $body = $entry->body . '
===
You have signed up to receive updates for ' . $parent->title . '<br />
To unsubscribe from these updates please click on this link: ';


            // check status (section and enabled)
            if ($sectionId == 2 && $enabled == 1) {
                // check against PC records for subscriptions
                $PrayerCornerRecord = PrayerCornerRecord::model()->findAllByAttributes(array('entryId' => $relatedTo));
                foreach($PrayerCornerRecord AS $prayer) {
                    $temp = array();
                    $temp['recipient'] = $prayer->email;
                    $temp['uid'] = $prayer->uid;
                    $emails[] = $temp;
                }

                $email = new EmailModel();
                $email->subject = 'An update to ' . $parent->title;
                // loop through emails for recipients and unsubscribe refs
                foreach ($emails AS $value) {
                    $recipient = $value['recipient'];
                    $uid= $value['uid'];
                    try
                    {
                        // Add a specific recipient to the email model
                        $email->toEmail = $recipient;
                        // append UID to message
                        $email->body = $body . UrlHelper::getActionUrl('PrayerCorner/Unsubscribe',$uid);
                        // Send the email
                        craft()->email->sendEmail( $email );
                    }
                    catch ( \Exception $e )
                    {
                        // Do nothing
                        return false;
                    }

                }

                // log emails sent
                PrayerCornerPlugin::log(
                    count($emails) . ' emails sent regarding ' . $parent->title . '('.$relatedTo.')',
                    LogLevel::Info,
                    true
                );
            }
        });
    }

}