<?php

namespace Hubleto\App\External\WaiBlue\OhsCore\Services;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Auth\Models\UserHasRole;

/**
 * Apps a user with an Ohs role gets. Every Ohs role gets the same apps.
 */
class RoleApps extends \Hubleto\Erp\Core
{
  // Hubleto's Documents app is not given: work documents are its documents, shown and changed through OhsDocuments,
  // and its templates are managed by administrators.
  const APPS_OF_OHS_ROLES = [
    'Hubleto\\App\\Community\\Desktop',
    'Hubleto\\App\\Community\\Dashboards',
    'Hubleto\\App\\Community\\Notifications',
    'Hubleto\\App\\Community\\Customers',
    'Hubleto\\App\\Community\\Contacts',
    'Hubleto\\App\\Community\\Calendar',
    'Hubleto\\App\\Community\\Workflow',
    'Hubleto\\App\\External\\WaiBlue\\OhsCore',
    'Hubleto\\App\\External\\WaiBlue\\OhsHazards',
    'Hubleto\\App\\External\\WaiBlue\\OhsClinics',
    'Hubleto\\App\\External\\WaiBlue\\OhsClients',
    'Hubleto\\App\\External\\WaiBlue\\OhsBranches',
    'Hubleto\\App\\External\\WaiBlue\\OhsEmployees',
    'Hubleto\\App\\External\\WaiBlue\\OhsMedicalExams',
    'Hubleto\\App\\External\\WaiBlue\\OhsAudits',
    'Hubleto\\App\\External\\WaiBlue\\OhsDocuments',
    'Hubleto\\App\\External\\WaiBlue\\OhsEvents',
    // Vehicles and their bookings (wai-blue/hubleto-vehicle-log), installed next to the Ohs apps.
    'Hubleto\\App\\External\\WaiBlue\\VehicleLog',
  ];

  /**
   * The apps of the Ohs roles, or none when the user has no Ohs role.
   */
  public function getAppsForUser(int $idUser): array
  {
    /** @var Roles */
    $roles = $this->getService(Roles::class);
    $userRoleIds = $this->getModel(UserHasRole::class)->record->where('id_user', $idUser)->pluck('id_role')->toArray();

    foreach (array_keys($roles->getRoleDefinitions()) as $roleKey) {
      $hasOhsRole = in_array($roles->getRoleId($roleKey), $userRoleIds);
      if ($hasOhsRole) return self::APPS_OF_OHS_ROLES;
    }
    return [];
  }

  /**
   * Adds the apps of the user's roles to the user's allowed apps. Never removes an app an administrator granted.
   * With $replace the allowed apps become exactly those of the roles (used for demo users).
   */
  public function grantAppsOfRoles(int $idUser, bool $replace = false): void
  {
    $appsOfRoles = $this->getAppsForUser($idUser);
    if (count($appsOfRoles) == 0) return;

    $user = $this->getModel(User::class)->record->where('id', $idUser)->first();
    if (!$user) return;

    $allowedApps = $appsOfRoles;
    if (!$replace) {
      $currentApps = json_decode((string) $user->apps, true);
      if (is_array($currentApps)) $allowedApps = array_values(array_unique(array_merge($currentApps, $appsOfRoles)));
    }

    $user->update(['apps' => json_encode($allowedApps)]);
  }
}
