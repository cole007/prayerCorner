<?php
namespace Craft;
/**
 * Created by PhpStorm.
 * User: colehenley
 * Date: 04/01/2016
 * Time: 14:13
 */
class PrayerCornerService extends BaseController
{
    protected $allowAnonymous = true;

    /**
     * Returns counted entries
     *
     * @return ElementCriteriaModel
     */
    public function getEntries()
    {
        // get all records from DB ordered by count descending
        $PrayerCornerRecords = PrayerCornerRecord::model()->findAll();

        return $PrayerCornerRecords;
//        return true;
    }

//    public function subscribe()
//    {
//        print_r(craft()->request);
//    }

    public function reset($uid)
    {
        // get record from DB
        $prayerCornerRecord = PrayerCornerRecord::model()->findByAttributes(array('uid' => $uid));

        // if record exists then delete
        if ($prayerCornerRecord)
        {
            // delete record from DB
            $prayerCornerRecord->delete();
        }

        // log reset
        PrayerCornerPlugin::log(
            'Prayer Corner subscription with UID' . $uid . ' has been deleted via the Control Panel by '.craft()->userSession->getUser()->username,
            LogLevel::Info,
            true
        );

//        // fire an onResetCount event
//        $this->onResetCount(new Event($this, array('entryId' => $entryId)));
    }

    function __construct(){
        parent::__construct($this->id, $this->module);
    }

    /**
     * Fires an 'onBeforeSend' event.
     *
     * @param ContactFormEvent $event
     */
//    public function onBeforeSend(ContactFormEvent $event)
//    {
//        $this->raiseEvent('onBeforeSend', $event);
//    }
}