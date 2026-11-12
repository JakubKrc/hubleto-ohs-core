<?php

namespace Hubleto\App\External\WaiBlue\OhsCore\Models\Migrations;

use Hubleto\Framework\Migration;

// Hazard names are unique, as in both legacy apps (models and lookups review B8).
class Hazard_0002 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `ohs_hazards` ADD UNIQUE INDEX `name` (`name`)");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `ohs_hazards` DROP INDEX `name`");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}
