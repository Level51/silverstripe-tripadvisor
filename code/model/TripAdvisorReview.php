<?php
/**
 * Created by PhpStorm.
 * User: Julian Scheuchenzuber <js@lvl51.de>
 * Date: 10.08.15
 * Time: 20:40
 */


class TripAdvisorReview extends DataObject {
    private static $db = array(
        'Lang' => 'Varchar(2)',
        'Published' => 'SS_Datetime',
        'TravelDate' => 'SS_Datetime',
        'Rating' => 'Int',
        'RatingImageURL' => 'Varchar(255)',
        'URL' => 'Varchar(255)',
        'TripType' => 'Varchar',
        'Text' => 'Text',
        'User' => 'Varchar',
        'Title' => 'Varchar'
    );
}