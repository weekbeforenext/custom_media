<?php

namespace Drupal\custom_media;

use Drupal\Core\Logger\LoggerChannelFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Provides a 'CustomMediaFacebookGraphService' service.
 *
 * Methods for accessing the Facebook Graph API.
 */
class CustomMediaFacebookGraphService extends CustomMediaServiceBase {

  /**
   * The base endpoint.
   *
   * @var string
   */
  protected $baseEndpoint = 'https://graph.facebook.com/v8.0/';

  /**
   * The authentication token URL.
   *
   * @var string
   */
  protected $authTokenUrl = 'https://graph.facebook.com/oauth/access_token';

  /**
   * The Facebook App ID.
   *
   * @var string
   */
  protected $appId = '';

  /**
   * The Facebook App Secret.
   *
   * @var string
   */
  protected $appSecret = '';

  /**
   * The authentication headers.
   *
   * @var array
   */
  protected $headers = [];

  /**
   * {@inheritdoc}
   */
  public function __construct(Client $http_client, LoggerChannelFactory $logger) {
    parent::__construct($http_client, $logger);
    $this->appId = getenv('FACEBOOK_APP_ID');
    $this->appSecret = getenv('FACEBOOK_APP_SECRET');
    // If the environment variables are not available, log an error.
    if (!$this->appId || !$this->appSecret) {
      $this->logger->get('media')->error(t('Cannot add a Facebook post or video thumbnail because credentials are missing on the server.'));
      return;
    }
    $config = [
      'token_url' => $this->authTokenUrl,
      'client_id' => $this->appId,
      'client_secret' => $this->appSecret,
    ];
    $this->headers = $this->getOauth2Headers($this->baseEndpoint, 'client_credentials', $config);
  }

  /**
   * Retrieve thumbnail image for a given video ID.
   *
   * @param string $video_id
   *   The Facebook video ID.
   *
   * @return array|null
   *   Response data from API call or null.
   *
   * @throws \Exception
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getVideoImage($video_id) {
    // Set the URI for the API call.
    $uri = $this->baseEndpoint . $video_id . '/picture?redirect=0';
    try {
      // Create the request with authentication headers.
      $request = $this->httpClient->request('GET', $uri, $this->headers);
      // Convert the response to an array.
      $response = \GuzzleHttp\json_decode($request->getBody()->getContents(), TRUE);
      return $response;
    }
    catch (RequestException $e) {
      $this->logger->get('media')->warning($e->getMessage());
    }
    return NULL;
  }

}
