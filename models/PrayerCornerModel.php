<?php
namespace Craft;
/**
 * Created by PhpStorm.
 * User: colehenley
 * Date: 04/01/2016
 * Time: 14:08
 */
class PrayerCornerModel
{
    /* Define what is returned when model is converted to string */

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->email;
    }

    /* Define model attributes */

    /**
     * @return array
     */
    public function defineAttributes()
    {
        return array(
            'id' => AttributeType::Number,
            'entryId' => AttributeType::Number,
            'email' => array(AttributeType::Email, default => ''),
            'dateCreated' => AttributeType::DateTime,
            'dateUpdated' => AttributeType::DateTime
        )
    }
}