<?php

/**
 * Created by PhpStorm.
 * User: Julian Scheuchenzuber <js@lvl51.de>
 * Date: 10.08.15
 * Time: 20:30
 */
class TripAdvisorConfig extends DataExtension {
    private static $db = array(
        'TripAdvisorApiKey' => 'Varchar(255)',
        'TripAdvisorLocationID' => 'Int',
        'TripAdvisorLastUpdate' => 'SS_Datetime'
    );

    function updateCMSFields(FieldList $fields) {
        $keyField = TextField::create('TripAdvisorApiKey', _t('TripAdvisorConfig.API_KEY', "API key"))->setDescription(_t('TripAdvisorConfig.API_KEY_DESCRIPTION', "You need to enter a valid API key to use the TripAdvisor module functionality.<br>Check out this page: <a href='https://developer-tripadvisor.com/content-api/request-api-access/' target='_blank'>https://developer-tripadvisor.com/content-api/request-api-access/</a>"));

        if(!$this->owner->TripAdvisorApiKey) {
            $fields->addFieldToTab('Root.TripAdvisor', $keyField);
        } else {
            $fields->addFieldsToTab('Root.TripAdvisor', array(
                $keyField,
                TextField::create('TripAdvisorLocationID', _t('TripAdvisorConfig.LOCATION_ID', "Location ID")),
                ReadonlyField::create('TripAdvisorLastUpdate', _t('TripAdvisorConfig.LAST_UPDATE', "Last API update"))
            ));
        }

        if($l = TripAdvisorLocation::current())
            $fields->addFieldsToTab('Root.TripAdvisor', array(
                ReadonlyField::create('Name', _t('TripAdvisorConfig.LOCATION_NAME', "Name"), $l->Name),
                HtmlEditorField_Readonly::create('URL', _t('TripAdvisorConfig.LOCATION_URL', "URL"), '<a href="' . $l->URL . '" target="_blank">' . $l->URL . '</a>'),
                ReadonlyField::create('PriceLevel', _t('TripAdvisorConfig.PRICE_LEVEL', "Price level"), $l->PriceLevel),
                ReadonlyField::create('Rating', _t('TripAdvisorConfig.RATING', "Rating"), $l->Rating),
                ReadonlyField::create('NumReviews', _t('TripAdvisorConfig.NUM_VIEWS', "Number of views"), $l->NumReviews)
            ));
    }

    function updateCMSActions(FieldList $actions) {
        if( $this->owner->TripAdvisorApiKey &&
            $this->owner->TripAdvisorLocationID)
            $actions->push(
                FormAction::create('loadProfile', _t('TripAdvisorConfig.LOAD_PROFILE', "Sync TripAdvisor profile"))->setAttribute('data-icon', 'accept')
            );
    }
}