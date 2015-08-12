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

    public function getLocationProfile($forceRefresh = false, $setRefresh = true) {
        if($forceRefresh) {
            TripAdvisorLocation::get()->removeAll();
            TripAdvisorReview::get()->removeAll();
            return $this->fetchProfile($setRefresh);
        } else
            return TripAdvisorLocation::get()->first();
    }

    public function getReviews($forceRefresh = false, $setRefresh = true) {
        if($forceRefresh) {
            TripAdvisorLocation::get()->removeAll();
            TripAdvisorReview::get()->removeAll();
            $this->fetchProfile();
        }

        return TripAdvisorReview::get($setRefresh);
    }

    private function fetchProfile($setRefresh = true) {
        // Set key as query param
        $this->setQueryString(array(
            'key' => SiteConfig::current_site_config()->TripAdvisorApiKey
        ));

        // Request location endpoint
        $response = $this->request('location/' . SiteConfig::current_site_config()->TripAdvisorLocationID);

        // Check if there was an error
        if($response->getStatusCode() < 400) {
            // Process response
            $response = json_decode($response->getBody());

            // Set update flag and save
            if($setRefresh) {
                $sC = SiteConfig::current_site_config();
                $sC->TripAdvisorLastUpdate = SS_Datetime::now()->getValue();
                $sC->write();
            }

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
            user_error("TripAdvisor Service Error : $err_msg", E_USER_ERROR);
        }

        return $response;
    }
}