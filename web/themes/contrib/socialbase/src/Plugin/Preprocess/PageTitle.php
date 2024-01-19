<?php

namespace Drupal\socialbase\Plugin\Preprocess;

use Drupal\bootstrap\Plugin\Preprocess\PreprocessBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Pre-processes variables for the "page_title" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @BootstrapPreprocess("page_title")
 */
class PageTitle extends PreprocessBase implements ContainerFactoryPluginInterface {

  /**
   * Route Match service.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected RouteMatchInterface $routeMatch;

  /**
   * {@inheritDoc}
   */
  public function __construct(
    array $configuration,
          $plugin_id,
          $plugin_definition,
    RouteMatchInterface $route_match
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function preprocess(array &$variables, $hook, array $info): void {
    parent::preprocess($variables, $hook, $info);

    // Get the current path and if is it stream return a variable.
    $current_url = Url::fromRoute('<current>');
    $current_path = $current_url->toString();
    $route_name = $this->routeMatch->getRouteName();

    if ($route_name === 'profile.user_page.single') {
      if ($variables['title'] instanceof TranslatableMarkup) {
        $profile_type = $variables['title']->getArguments();
      }

      if (!empty($profile_type['@label'])) {
        $variables['title'] = $this->t('Edit @label', ['@label' => $profile_type['@label']]);
      }
    }

    if ($route_name === 'entity.user.edit_form' && isset($variables['title']['#markup'])) {
      $variables['title'] = $this->t('<em>Configure account settings:</em> @label', ['@label' => $variables['title']['#markup']]);
    }

    if (strpos($current_path, 'stream') !== FALSE || strpos($current_path, 'explore') !== FALSE) {
      $variables['stream'] = TRUE;
    }

    // Check if it is a node.
    if (strpos($current_path, 'node') !== FALSE || $route_name === 'social_album.add') {
      $variables['node'] = TRUE;
    }

    // Check if it is the edit/add/delete.
    if (in_array($route_name, [
      'entity.node.edit_form',
      'entity.node.delete_form',
      'entity.node.add_form',
    ])) {
      $variables['edit'] = TRUE;
    }

  }

}