<?php

namespace Hubleto\App\External\WaiBlue\OhsCore;

class Loader extends OhsApp
{
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^ohs\/core\/?$/' => Controllers\Home::class,
      '/^ohs\/core\/settings\/?$/' => Controllers\Settings::class,
      '/^ohs\/core\/api\/user-lookup\/?$/' => Controllers\Api\UserLookup::class,
    ]);
    $this->router()->crud('ohs/core/cars', Controllers\Cars::class);

    // Users who get a role also get the apps and the home dashboard panels of that role.
    $this->eventManager()->addEventListener('onModelAfterCreate', $this->getService(EventListeners\UserRoleChanges::class));
  }

  public function installApp(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Car::class)->upgradeSchema();
      // "php hubleto app install" runs only round 1, so foreign keys are created here too (Hubleto skips them later).
      $this->installForeignKeys();

      // Round 1, because "php hubleto app install" runs only round 1. Auth is always installed before custom apps.
      $this->getService(Services\Roles::class)->installRoles();
    }
  }

  public function renderSecondSidebar(): string
  {
    return '
      ' . $this->secondSidebarTitle() . '
      <div class="app-sidebar-buttons">
        ' . $this->secondSidebarButton('ohs/core/cars', 'fas fa-car', $this->translate('Cars')) . '
        ' . $this->secondSidebarButton('ohs/core/settings', 'fas fa-cog', $this->translate('Settings')) . '
      </div>
    ';
  }

  public function renderAlerts(): string
  {
    return '';
  }

  public function getSidebarBadgeNumber(): int
  {
    return 0;
  }

  public function generateOhsDemoData(): void
  {
    $this->getService(Services\DemoData::class)->ensureDemoUsers();
    $this->getService(Services\HomeDashboard::class)->ensureForAllUsers();

    /** @var Models\Car */
    $mCar = $this->getModel(Models\Car::class);
    $demoCars = [
      ['name' => 'Škoda Octavia', 'license_plate' => 'BA000AA'],
      ['name' => 'Hyundai i20', 'license_plate' => 'BA000BB'],
    ];
    foreach ($demoCars as $demoCar) {
      $carExists = $mCar->record->where('license_plate', $demoCar['license_plate'])->exists();
      if ($carExists) continue;
      $mCar->record->recordCreate($demoCar);
    }
  }
}
