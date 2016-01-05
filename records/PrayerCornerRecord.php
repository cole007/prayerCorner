<?php
namespace Craft;
/**
 * Created by PhpStorm.
 * User: colehenley
 * Date: 04/01/2016
 * Time: 13:53
 */
class PrayerCornerRecord extends BaseRecord
{
    /* get table name */
    public function getTableName()
    {
        return 'prayercorner';
    }
    /* define table cols */
    public function defineAttributes()
    {
        return array(
            'email' => array(AttributeType::Email, 'default' => '')
        );
    }
    /* define relationships with other tables */
    public function defineRelations()
    {
        return array(
            'entry' => array(static::BELONGS_TO, 'EntryRecord', 'required' => true, 'onDelete' => static::CASCADE)
        );
    }
    /* define table indexes */
    public function defineIndexes()
    {
        return array(
            array('columns' => array('entryId'))
        );
    }
}