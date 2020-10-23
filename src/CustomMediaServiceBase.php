<?php

namespace Drupal\custom_media;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\Core\Logger\LoggerChannelFactory;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Sainsburys\Guzzle\Oauth2\GrantType\AuthorizationCode;
use Sainsburys\Guzzle\Oauth2\GrantType\ClientCredentials;
use Sainsburys\Guzzle\Oauth2\GrantType\JwtBearer;
use Sainsburys\Guzzle\Oauth2\GrantType\RefreshToken;
use Sainsburys\Guzzle\Oauth2\GrantType\PasswordCredentials;
use Sainsburys\Guzzle\Oauth2\Middleware\OAuthMiddleware;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'CustomMediaServiceBase' service.
 *
 * Base methods for Media API consumption services.
 */
class CustomMediaServiceBase extends ServiceProviderBase implements ContainerInjectionInterface {

  /**
   * Guzzle\Client instance.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * The logger channel for media.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * The Oauth2 middleware object.
   *
   * @var \Sainsburys\Guzzle\Oauth2\Middleware\OAuthMiddleware
   */
  protected $oAuthMiddleware;

  /**
   * {@inheritdoc}
   */
  public function __construct(Client $http_client, LoggerChannelFactory $logger) {
    $this->httpClient = $http_client;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client'),
      $container->get('logger.factory')
    );
  }

  /**
   * Function to retrieve oauth2 authentication header options.
   *
   * Based on migrate_plus/src/Plugin/migrate_plus/authentication/OAuth2.php.
   *
   * @param string $base_uri
   *   The API endpoint URI.
   * @param string $grant_type
   *   The grant type for the given API.
   * @param array $configuration
   *   Configuration values for retrieving an oauth2 token.
   *   Example:
   *     $configuration = [
   *       'token_url' => '',
   *       'client_id' => '',
   *       'client_secret' => '',
   *     ];.
   *
   * @throws \Exception
   *
   * @return array
   *   Authorization header options with oauth2 token.
   */
  public function getOauth2Headers($base_uri, $grant_type, array $configuration) {
    $handlerStack = HandlerStack::create();
    $client = new Client([
      'handler' => $handlerStack,
      'base_uri' => $base_uri,
      'auth' => 'oauth2',
    ]);

    switch ($grant_type) {
      case 'authorization_code':
        $grant_type = new AuthorizationCode($client, $configuration);
        break;

      case 'client_credentials':
        $grant_type = new ClientCredentials($client, $configuration);
        break;

      case 'urn:ietf:params:oauth:grant-type:jwt-bearer':
        $grant_type = new JwtBearer($client, $configuration);
        break;

      case 'password':
        $grant_type = new PasswordCredentials($client, $configuration);
        break;

      case 'refresh_token':
        $grant_type = new RefreshToken($client, $configuration);
        break;

      default:
        throw new \Exception("Unrecognized grant_type {$grant_type}.");

    }
    $middleware = new OAuthMiddleware($client, $grant_type);

    $headers = [
      'headers' => [
        'Authorization' => 'Bearer ' . $middleware->getAccessToken()->getToken(),
      ],
    ];
    return $headers;
  }

}
