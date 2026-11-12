<?php

namespace Hubleto\App\External\WaiBlue\OhsCore;

/**
 * Base of the Ohs app loaders.
 */
abstract class OhsApp extends \Hubleto\Erp\App
{
  /**
   * Each Ohs app registers the Ohs translator as soon as it is created: apps start in alphabetical order,
   * and "php hubleto init" installs an app before its init() runs.
   */
  public function __construct()
  {
    parent::__construct();
    Services\Translator::register();
  }

  // Whether the signed-in user may open the app, for buttons and links to it.
  public function isAppPermittedForActiveUser(string $appNamespace): bool
  {
    $app = $this->appManager()->getApp($appNamespace);
    if ($app === null) return false;
    return $this->permissionsManager()->isAppPermittedForActiveUser($app);
  }

  /**
   * Hubleto calls generateDemoData() of the apps in alphabetical order, but the Ohs demo data needs
   * the order of the app dependencies, so the first call generates the demo data of all Ohs apps.
   */
  public function generateDemoData(): void
  {
    $this->getService(Services\DemoData::class)->generateForAllOhsApps();
  }

  // FAKE demo data of this app only; DemoData::generateForAllOhsApps() calls it in the right order.
  public function generateOhsDemoData(): void
  {
  }
}
