<?php

namespace Hubleto\App\External\WaiBlue\OhsCore\Controllers\Api;

use Hubleto\App\Community\Customers\Models\Customer;
use Hubleto\App\External\WaiBlue\OhsCore\Services\AccessScope;
use Hubleto\App\External\WaiBlue\OhsCore\Services\DropdownRecords;

/**
 * Client (Customer) dropdown limited to visible clients, also when OhsClients (which scopes the Customer
 * model itself) is not installed. Same output format as api/record/lookup.
 */
class CustomerLookup extends \Hubleto\Erp\Controllers\ApiController
{
  const ENDPOINT = 'ohs/core/api/customer-lookup';

  public function renderJson(): array
  {
    $search = $this->router()->urlParamAsString('search');
    /** @var Customer */
    $mCustomer = $this->getModel(Customer::class);

    $query = $mCustomer->record->prepareLookupQuery($search);
    $query = $this->getService(AccessScope::class)->restrictToLinkableCustomers($query, 'customers.id');
    if (method_exists($mCustomer->record, 'restrictToOfferedRecords')) {
      $query = $mCustomer->record->restrictToOfferedRecords($query);
    }

    // A list, as Hubleto's own lookup returns since Hubleto 2.
    return $this->getService(DropdownRecords::class)->load($mCustomer, $query);
  }
}
