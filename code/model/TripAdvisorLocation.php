<?php

/**
 * Created by PhpStorm.
 * User: Julian Scheuchenzuber <js@lvl51.de>
 * Date: 10.08.15
 * Time: 21:44
 */
class TripAdvisorLocation extends DataObject {
    private static $db = array(
        'Name' => 'Varchar(255)',
        'URL' => 'Varchar(255)',
        'PriceLevel' => 'Varchar(5)',
        'Rating' => 'Decimal(2,1)',
        'RatingImageURL' => 'Varchar(255)',
        'NumReviews' => 'Int'
    );

    private static $has_many = array(
        'Reviews' => 'TripAdvisorReview'
    );

    /**
     * Returns in the currently synced profile record.
     * @return DataObject
     */
    public static function current() {
        return TripAdvisorLocation::get()->first();
    }

    /**
     * Ensures that there can only be one record at a time.
     * @param null $member
     * @return bool
     */
    public function canCreate($member = null) {
        return !TripAdvisorLocation::get()->first();
    }
}