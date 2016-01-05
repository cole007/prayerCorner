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
    protected function defineSettings()
    {
        return array(
            'parentSection' => array(AttributeType::Mixed, 'default' => ''),
            'fieldType' => array(AttributeType::Mixed, 'default' => '')
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
        // Event: onBeforeSaveEntry
        craft()->on('entries.onBeforeSaveEntry', function(Event $event) {

            // check entry and declare relevant variables
            $entry = $event->params['entry'];
            $title= $entry->title;
            $id = $entry->id;
            $sectionId = $entry->sectionId;
            $enabled = $entry->enabled;

            // get settings
            $settings = craft()->plugins->getPlugin('prayerCorner')->getSettings();
            $section = $settings->parentSection;
            $field = $settings->fieldType;

            // custom field to check against
            $relatedTo = $entry[$field];

            // get parent entry from relationship
            $parent = craft()->entries->getEntryById($relatedTo);

            // declare body of email message
            $body = $entry->body . '
===
You have signed up to receive updates for ' . $parent->title . '<br />
To unsubscribe from these updates please click on this link: ';


            // check status (section and enabled)
            if ($sectionId == $section && $enabled == 1) {
                // check against PC records for subscriptions
                $PrayerCornerRecord = PrayerCornerRecord::model()->findAllByAttributes(array('entryId' => $relatedTo));
                $emails = array();
                foreach($PrayerCornerRecord AS $prayer) {
                    $temp = array();
                    $temp['recipient'] = $prayer->email;
                    $temp['uid'] = $prayer->uid;
                    $emails[] = $temp;
                }

                $email = new EmailModel();
                $email->subject = 'An update to ' . $parent->title;
                // loop through emails for recipients and unsubscribe refs
                if (count($PrayerCornerRecord) > 0) {
                    foreach ($emails AS $value) {
                        $recipient = $value['recipient'];
                        $uid = $value['uid'];
                        try {
                            // Add a specific recipient to the email model
                            $email->toEmail = $recipient;
                            // append UID to message
                            $email->body = $body . UrlHelper::getActionUrl('PrayerCorner/Unsubscribe', $uid);
                            // Send the email
                            craft()->email->sendEmail($email);
                            // log emails sent
                            PrayerCornerPlugin::log(
                                'email sent to ' . $recipient . ' ('.$relatedTo.')',
                                LogLevel::Info,
                                true
                            );
                        } catch (\Exception $e) {
                            // Do nothing
                            return false;
                        }

                    }
                }

                // log emails sent
                PrayerCornerPlugin::log(
                    count($PrayerCornerRecord) . ' emails sent regarding ' . $parent->title . ' ('.$relatedTo.')',
                    LogLevel::Info,
                    true
                );
            }
        });
    }

}