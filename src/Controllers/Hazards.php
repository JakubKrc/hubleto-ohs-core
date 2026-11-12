<?php

namespace Hubleto\App\External\WaiBlue\OhsCore\Controllers;

class Hazards extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:External:WaiBlue:OhsCore/Hazards.twig');
  }
}
