<?php

namespace Hubleto\App\External\WaiBlue\OhsCore\Services;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Auth\Models\UserHasRole;

/**
 * Which apps (pages) each role may open. Records inside the apps are limited by the scope rules.
 * Medical staff see only clients, branches, employees and exams in both legacy apps; the other roles differ, so they get every Ohs app.
 */
class RoleApps extends \Hubleto\Erp\Core
{
  const APPS_FOR_EVERY_ROLE = [
    'Hubleto\\App\\Community\\Desktop',
    'Hubleto\\App\\Community\\Dashboards',
    'Hubleto\\App\\Community\\Notifications',
    'Hubleto\\App\\Community\\Customers',
    'Hubleto\\App\\External\\WaiBlue\\OhsBranches',
    'Hubleto\\App\\External\\WaiBlue\\OhsEmployees',
    'Hubleto\\App\\External\\WaiBlue\\OhsMedicalExams',
  ];

  // Manager, hygienist and nurse: every internal Ohs app, the calendar and workflows.
  // Hubleto's Documents app is not given: work documents are its documents, shown and changed through OhsDocuments,
  // and its templates are managed by administrators.
  // Contact persons too: both legacy apps hide them from medical staff.
  const APPS_FOR_INTERNAL_ROLES = [
    'Hubleto\\App\\Community\\Contacts',
    'Hubleto\\App\\Community\\Calendar',
    'Hubleto\\App\\Community\\Workflow',
    'Hubleto\\App\\External\\WaiBlue\\OhsCore',
    'Hubleto\\App\\External\\WaiBlue\\OhsClients',
    'Hubleto\\App\\External\\WaiBlue\\OhsAudits',
    'Hubleto\\App\\External\\WaiBlue\\OhsDocuments',
    'Hubleto\\App\\External\\WaiBlue\\OhsEvents',
  ];

  const APPS_FOR_MEDICAL_STAFF = [];

  public function getAppsForRole(string $roleKey): array
  {
    if ($roleKey == Roles::MEDICAL_STAFF) return self::APPS_FOR_MEDICAL_STAFF;
    return self::APPS_FOR_INTERNAL_ROLES;
  }

  /**
   * Apps of all Ohs roles the user has. Empty when the user has no Ohs role.
   */
  public function getAppsForUser(int $idUser): array
  {
    /** @var Roles */
    $roles = $this->getService(Roles::class);
    $userRoleIds = $this->getModel(UserHasRole::class)->record->where('id_user', $idUser)->pluck('id_role')->toArray();

    $apps = [];
    $hasOhsRole = false;
    foreach (array_keys($roles->getRoleDefinitions()) as $roleKey) {
      if (!in_array($roles->getRoleId($roleKey), $userRoleIds)) continue;
      $hasOhsRole = true;
      $apps = array_merge($apps, $this->getAppsForRole($roleKey));
    }

    if (!$hasOhsRole) return [];
    return array_values(array_unique(array_merge(self::APPS_FOR_EVERY_ROLE, $apps)));
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
