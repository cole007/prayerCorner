<?php
namespace Craft;
/**
 * Created by PhpStorm.
 * User: colehenley
 * Date: 05/01/2016
 * Time: 13:45
 *
 *
 */
class PrayerCornerVariable
{
    /**
     * Returns counted entries
     *
     * @return ElementCriteriaModel
     */
    public function getEntries()
    {
        return craft()->prayerCorner->getEntries();
    }
}