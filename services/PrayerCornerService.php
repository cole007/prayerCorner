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
    public function subscribe()
    {
        print_r(craft()->request);
    }



    /**
     * Fires an 'onBeforeSend' event.
     *
     * @param ContactFormEvent $event
     */
    public function onBeforeSend(ContactFormEvent $event)
    {
        $this->raiseEvent('onBeforeSend', $event);
    }
}