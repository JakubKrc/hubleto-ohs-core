<?php

namespace Hubleto\App\External\WaiBlue\OhsCore\EventListeners;

use Hubleto\App\Community\Auth\Models\UserHasRole;
use Hubleto\App\External\WaiBlue\OhsCore\Services\HomeDashboard;
use Hubleto\App\External\WaiBlue\OhsCore\Services\RoleApps;
use Hubleto\Framework\Interfaces\ModelInterface;

/**
 * A user who gets an Ohs role also gets the apps and the home dashboard panels of that role.
 */
class UserRoleChanges extends \Hubleto\Framework\EventListener implements \Hubleto\Framework\Interfaces\EventListenerInterface
{
  public function onModelAfterCreate(ModelInterface $model, array $savedRecord): void
  {
    if (!($model instanceof UserHasRole)) return;

    $idUser = (int) ($savedRecord['id_user'] ?? 0);
    $this->getService(RoleApps::class)->grantAppsOfRoles($idUser);
    $this->getService(HomeDashboard::class)->ensureForUser($idUser);
  }
}
