<?php

namespace Hubleto\App\External\WaiBlue\OhsCore\Models;

use Hubleto\Framework\Db\Column\Varchar;

class Car extends \Hubleto\Erp\Model
{
  public string $table = 'ohs_cars';
  public string $recordManagerClass = RecordManagers\Car::class;
  public ?string $lookupSqlValue = 'concat({%TABLE%}.name, " (", {%TABLE%}.license_plate, ")")';
  public ?string $lookupUrlDetail = 'ohs/core/cars/{%ID%}';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setDefaultVisible(),
      'license_plate' => (new Varchar($this, $this->translate('License plate')))->setRequired()->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate('Add car');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    return parent::describeForm();
  }
}
