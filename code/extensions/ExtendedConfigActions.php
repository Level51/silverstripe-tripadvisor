<?php

/**
 * Created by PhpStorm.
 * User: Julian Scheuchenzuber <js@lvl51.de>
 * Date: 11.08.15
 * Time: 21:08
 */
class ExtendedConfigActions extends Extension {
    public function loadProfile($data, $form) {
        // Set update flag and save
        $sC = SiteConfig::current_site_config();
        $form->saveInto($sC);
        $sC->TripAdvisorLastUpdate = SS_Datetime::now()->getValue();
        $sC->write();

        // New TripAdvisor service
        $service = new TripAdvisorService();

        // Delete old data and fetch new
        $service->getLocationProfile(true, false);

        // Set response
        $this->owner->response->addHeader('X-Status', rawurlencode(_t('ExtendedConfigActions.SYNCED_PROFILE', 'Synced TripAdvisor profile "{location}".', null, array(
            'location' => TripAdvisorLocation::current()->Name
        ))));
        return $this->owner->getResponseNegotiator()->respond($this->owner->request);
    }
}