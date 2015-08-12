<?php

/**
 * Created by PhpStorm.
 * User: Julian Scheuchenzuber <js@lvl51.de>
 * Date: 10.08.15
 * Time: 21:03
 */
class TripAdvisorRefreshTask extends BuildTask {

    protected $title = "TripAdvisor refresh/sync task";

    protected $description = "Drops the current TripAdvisor location data and fetches it newly.";

    public function run($request) {
        // New TripAdvisor service
        $service = new TripAdvisorService();

        // Delete old data and fetch new
        $response = $service->getLocationProfile(true);

        // Inform user
        echo "Created location profile \"" . $response->Name . "\" and the ". count($response->Reviews()->count()) ." latest review record(s)." . PHP_EOL;
    }
}