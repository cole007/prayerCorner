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

    protected function defineSettings()
    {
        return array(
            'showCountOnEntryIndex' => array(AttributeType::Bool, 'default' => 0),
            'ignoreLoggedInUsers' => array(AttributeType::Bool, 'default' => 0),
            'ignoreIpAddresses' => array(AttributeType::Mixed, 'default' => '')
        );
    }
    public function getSettingsHtml()
    {
        return craft()->templates->render('prayercorner/settings', array(
            'settings' => $this->getSettings()
        ));
    }


    // Hooks/events

    public function init()
    {
        parent::init();

        //Event: onBeforeSaveEntry
        craft()->on('entries.onBeforeSaveEntry', function(Event $event) {


            // check entry_id
            $entry = $event->params['entry'];
            $title= $entry->title;
            $id = $entry->id;
            $sectionId = $entry->sectionId;
            $enabled = $entry->enabled;
            // relevant field
            $relatedTo = $entry->relatedTo;

            $parent = craft()->entries->getEntryById($relatedTo);

            $body = $entry->body . '
===
You have signed up to receive updates to ' . $parent->title . '<br />
To unsubscribe from these updates please click on this link: ';


            // check status
            if ($sectionId == 2 && $enabled == 1) {
                // check against rels for entry_id
                $PrayerCornerRecord = PrayerCornerRecord::model()->findAllByAttributes(array('entryId' => $relatedTo));
                foreach($PrayerCornerRecord AS $prayer) {
                    $temp = array();
                    $temp['recipient'] = $prayer->email;
                    $temp['uid'] = $prayer->uid;
                    $emails[] = $temp;
                }

                $email = new EmailModel();
                $email->subject = 'An update to ' . $parent->title;
                // loop through emails for entry_id and collect
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
//
            }
//            exit;
//
//
//            print_r('wibble');
//            //Is the entry in the section 'parts'?
//            if ($event->params['entry']->section == 'parts') {
//
//                //Check if field is a number
//                if (is_numeric($event->params['entry']->number_field_handle)) {
//                    //Do your JSON stuff
//
//                    //Replace values
//                    $event->params['entry']->getContent()->number_field_handle = 'something_else';
//                }
//
//            }
        });
    }


}