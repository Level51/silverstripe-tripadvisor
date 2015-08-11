# SilverStripe TripAdvisor Integration
This module provides a simple integration of TripAdvisor location profiles including awards, reviews, etc.
Basically it is a wrapper for the *Content API* of TripAdvisor.

Official API documentation: https://developer-tripadvisor.com/content-api/

## Maintainer
* Julian Scheuchenzuber <js@lvl51.de>

## Installation
```
composer require level51/silverstripe-tripadvisor
```

If you don't like composer you can just download and unpack it to the root of your SilverStripe project.

## Prerequisites
1. Obtain a valid TripAdvisor API key at: https://developer-tripadvisor.com/content-api/request-api-access/
2. Get the ID of your location. You won't find it anywhere on the location profile's page, just check the URL.

## Setup
1. Provide your API key and location profile ID in the "Trip Advisor" tab of systems settings section.
2. Execute the "Load TripAdvisor profile" action. This will create one <code>TripAdvisorLocationProfile</code> and three <code>TripAdvisorReview</code> records. **Caution:** This will drop the current profile data if already loaded.
3. Go ahead and use it in your code (example snippets):
```php
// Fetch location profile and output number of visits
$profile = TripAdvisorLocationProfile::current();
echo $profile->NumVisits;

// Fetch reviews and render with custom template
return TripAdvisorReview::get()->renderWith('ReviewGrid');
```

## Notes
The logic uses the <code>RestfulService</code> class and hence does API calls via cURL. It follows the API guidelines in terms of caching so you don't have to worry about that.
