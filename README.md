##Summary
This module was created to support the creation of a Facebook
media type with the new authenticated oEmbed endpoints.

The [Media entity Facebook module](https://www.drupal.org/project/media_entity_facebook) is not supported by Drupal's
security advisory policy.

##Required Drupal Core Patches
This was originally created in Drupal 8.9.6.

The following patches were required. Review the issues mentioned
to see if there are updated patches or if they have been merged.

The patch for Issue #3008119 may be the exception because the ticket
changed direction. The patch mentioned here is likely the best option.

```
"2966043-6: Allow OEmbed resources without height": "https://www.drupal.org/files/issues/2018-12-11/2966043-6.patch",
"3042423-9: Add a hook to modify oEmbed resource data": "https://www.drupal.org/files/issues/2019-03-25/media_hook-oembed-resource-data-alter__example.patch",
"3103774-2: Thumbnail URI from URL without parameters": "https://www.drupal.org/files/issues/2020-01-16/media-thumbnail_uri_no_params-3103774-2.patch",
"3008119-22: Provide hook_oembed_providers_alter()": "https://www.drupal.org/files/issues/2019-11-27/drupal-allow_alter_oembed_providers-3008119-22.patch",
"3168301-13: oEmbed validator should use the urlResolver to get the resource URL": "https://www.drupal.org/files/issues/2020-10-02/3168301-13.patch"
```

##Environment Variables
This solution requires the following environment variables:
```
FACEBOOK_APP_ID
FACEBOOK_APP_SECRET
FACEBOOK_APP_CLIENT_TOKEN
```
These variables must be available in all environments.