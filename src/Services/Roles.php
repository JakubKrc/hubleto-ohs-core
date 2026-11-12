<?php

namespace Hubleto\App\External\WaiBlue\OhsCore\Services;

use Hubleto\App\Community\Auth\Models\UserRole;

/**
 * The Ohs user roles, created as standard Hubleto user roles.
 * Role ids are stored in the OhsCore app config, so the code never depends on role names.
 */
class Roles extends \Hubleto\Erp\Core
{
  const MANAGER = 'manager';
  const HYGIENIST = 'hygienist';
  const NURSE = 'nurse';
  const MEDICAL_STAFF = 'medical-staff';

  // Same place as the OhsCore app config. The app object is not available during install.
  const ROLE_IDS_CONFIG_PATH = 'apps/Hubleto\\App\\External\\WaiBlue\\OhsCore/roleIds/';

  public string $translationContext = 'hubleto-app-external-waiblue-ohscore-loader';
  public string $translationContextInner = 'Services\\Roles';

  public function getRoleDefinitions(): array
  {
    return [
      self::MANAGER => [
        'role' => $this->translate('Manager'),
        'description' => $this->translate('Manages the service.'),
      ],
      self::HYGIENIST => [
        'role' => $this->translate('Hygienist'),
        'description' => $this->translate('Works with own clients and branches: audits, documents, job categorisation.'),
      ],
      self::NURSE => [
        'role' => $this->translate('Nurse'),
        'description' => $this->translate('Plans and records medical exams of client employees.'),
      ],
      self::MEDICAL_STAFF => [
        'role' => $this->translate('Medical staff'),
        'description' => $this->translate('Clinic staff who perform medical exams.'),
      ],
    ];
  }

  /**
   * Creates missing roles. Safe to run repeatedly (app re-install).
   */
  public function installRoles(): void
  {
    /** @var UserRole */
    $mUserRole = $this->getModel(UserRole::class);

    foreach ($this->getRoleDefinitions() as $roleKey => $roleDefinition) {
      $existingRoleId = $this->getRoleId($roleKey);
      $roleExists = $existingRoleId > 0 && $mUserRole->record->where('id', $existingRoleId)->exists();
      if ($roleExists) continue;

      $createdRole = $mUserRole->record->recordCreate([
        'role' => $roleDefinition['role'],
        'description' => $roleDefinition['description'],
        'grant_all' => false,
        'is_default' => false,
      ]);
      $this->config()->save(self::ROLE_IDS_CONFIG_PATH . $roleKey, (string) $createdRole['id']);
    }
  }

  public function getRoleId(string $roleKey): int
  {
    return $this->config()->getAsInteger(self::ROLE_IDS_CONFIG_PATH . $roleKey);
  }

  // For whereIn(): the ids of the users who have the role.
  public function userIdsWithRole(string $roleKey): \Closure
  {
    $idRole = $this->getRoleId($roleKey);
    return function ($userIdsWithRole) use ($idRole) {
      $userIdsWithRole->select('id_user')->from('user_has_roles')->where('id_role', $idRole);
    };
  }
}
