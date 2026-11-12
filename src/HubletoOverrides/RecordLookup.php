<?php

namespace Hubleto\App\External\WaiBlue\OhsCore\HubletoOverrides;

use Hubleto\App\External\WaiBlue\OhsCore\Services\DropdownRecords;

/**
 * Hubleto's record lookup without its 5-minute cache, because the Ohs dropdowns offer only active records
 * plus the record the form already has (restrictToOfferedRecords).
 */
class RecordLookup extends \Hubleto\Framework\Controllers\Api\Record\Lookup
{
  public function response(): array
  {
    $search = $this->router()->urlParamAsString('search');
    $query = $this->model->record->prepareLookupQuery($search);

    // Some dropdowns offer fewer records than they can show (for example only active ones).
    if (method_exists($this->model->record, 'restrictToOfferedRecords')) {
      $query = $this->model->record->restrictToOfferedRecords($query);
    }

    return $this->getService(DropdownRecords::class)->load($this->model, $query);
  }
}
