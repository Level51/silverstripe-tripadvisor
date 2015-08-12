<?php

/**
 * Created by PhpStorm.
 * User: Julian Scheuchenzuber <js@lvl51.de>
 * Date: 10.08.15
 * Time: 22:09
 */
class TripAdvisorService extends RestfulService {
    /**
     * Creates an instance of the TripAdvisor RESTful API service.
     * Uses the standard v2.0 API endpoint and the recommended caching value (24 hours):
     *  https://developer-tripadvisor.com/content-api/technical-overview/#caching
     */
    public function __construct() {
        parent::__construct(
            Config::inst()->get('TripAdvisorAPI', 'Endpoint'),
            60 * 60 * 24);
    }

    /**
     * Return the location profile record.
     * @param bool|false $forceRefresh
     * @return DataObject
     */
    public function getLocationProfile($forceRefresh = false) {
        if($forceRefresh)
            $this->dropProfile(true);

        return TripAdvisorLocation::current();
    }

    /**
     * Return the (three) latest reviews.
     * The TripAdvisor API is designed to return the three most recent reviews in detail.
     * @param bool|false $forceRefresh
     * @return DataList
     */
    public function getReviews($forceRefresh = false) {
        if($forceRefresh)
            $this->dropProfile(true);

        return TripAdvisorReview::get();
    }

    /**
     * Drops all current TripAdvisor payload data (not API key, not location ID).
     * @param bool|false $refetch
     */
    private function dropProfile($refetch = false) {
        TripAdvisorLocation::get()->removeAll();
        TripAdvisorReview::get()->removeAll();

        if($refetch) $this->fetchProfile();
    }

    /**
     * Calls the TripAdvisor API via cURL and creates payload records.
     * @return mixed|RestfulService_Response
     * @throws ValidationException
     * @throws null
     */
    private function fetchProfile() {
        // Set key as query param
        $this->setQueryString(array(
            'key' => SiteConfig::current_site_config()->TripAdvisorApiKey
        ));

        // Request location endpoint due to API specification
        $response = $this->request('location/' . SiteConfig::current_site_config()->TripAdvisorLocationID);

        // Check if there was an error
        if($response->getStatusCode() < 400) {
            // Process response
            $response = json_decode($response->getBody());

            // Set update flag and save
            $sC = SiteConfig::current_site_config();
            $sC->TripAdvisorLastUpdate = SS_Datetime::now()->getValue();
            $sC->write();

            // Create profile
            $l = new TripAdvisorLocation();
            $l->ID = $response->location_id;
            $l->Created = SS_Datetime::now()->getValue();
            $l->Name = $response->name;
            $l->URL = $response->web_url;
            $l->PriceLevel = $response->price_level;
            $l->Rating = $response->rating;
            $l->RatingImageURL = $response->rating_image_url;
            $l->NumReviews = $response->num_reviews;
            $l->write();

            // Loop over review array
            foreach($response->reviews as $review) {
                // Create review record
                $r = new TripAdvisorReview();
                $r->ID = $review->id;
                $r->Created = SS_Datetime::now()->getValue();
                $r->Lang = $review->lang;
                $r->Published = $review->published_date;
                $r->TravelDate = $review->travel_date;
                $r->Rating = $review->rating;
                $r->RatingImageURL = $review->rating_image_url;
                $r->URL = $review->url;
                $r->TripType = $review->trip_type;
                $r->Text = $review->text;
                $r->User = $review->user->username;
                $r->Title = $review->title;
                $r->write();

                // Add to profile
                $l->Reviews()->add($r);
            }
        } else {
            $r = json_decode($response->getBody());
            Debug::friendlyError($r->error->code, 'There was an error processing your request against the TripAdvisor API: ' . $r->error->message);
        }

        return $response;
    }

    /**
     * "Best Practice" implementation due to documentation:
     *      https://docs.silverstripe.org/en/3.1/developer_guides/integration/restfulservice#handling-errors
     * @param $response
     * @return mixed
     */
    public function errorCatch($response) {
        $err_msg = $response;

        if(strpos($err_msg, '<') === false) {
            Debug::friendlyError(500, "TripAdvisor Service Error : $err_msg");
        }

        return $response;
    }
}