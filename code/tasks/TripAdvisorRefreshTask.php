<?php

/**
 * Created by PhpStorm.
 * User: Julian Scheuchenzuber <js@lvl51.de>
 * Date: 10.08.15
 * Time: 21:03
 */
class TripAdvisorRefreshTask extends BuildTask {

    public function run($request) {
        // New TripAdvisor service
        $service = new TripAdvisorService();

        // Delete old data and fetch new
        $response = $service->getLocationProfile(true);

        // Inform user
        echo "Created " . count($response->reviews) . " reviews for location \"" . $response->name . "\"";
    }
}