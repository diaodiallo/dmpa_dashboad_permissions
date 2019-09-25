<?php
//
//namespace Drupal\dmpa_dashboard_permissions;
//
//
//use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
//use Drupal\Core\StringTranslation\StringTranslationTrait;
//use Symfony\Component\DependencyInjection\ContainerInterface;
//
//class DashboardEditPermissions {
//
//  use StringTranslationTrait;
//
//  /**
//   * Get permissions for Country Dashboards.
//   *
//   * @return array
//   *   Permissions array.
//   */
//  public function permissions() {
//    $countries = [
//      'burkina-faso',
//      'drc',
//      'ghana',
//      'kenya',
//      'madagascar',
//      'malawi',
//      'mozambique',
//      'niger',
//      'nigeria',
//      'senegal',
//      'uganda',
//      'zambia',
//    ];
//
//    $permissions = [];
//
//    foreach ($countries as $country) {
//      $permissions += [
//        'dashboard edit ' . $country => [
//          'title' => $this->t('Gives permissions to edit ' . $country . ' dashboard'),
//        ],
//      ];
//    }
//    return $permissions;
//  }
//
//}