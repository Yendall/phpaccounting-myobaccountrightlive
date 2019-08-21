<?php
namespace PHPAccounting\MyobAccountRight\Message\InventoryItems\Responses;

use Omnipay\Common\Message\AbstractResponse;
use PHPAccounting\MyobAccountRight\Helpers\IndexSanityCheckHelper;

/**
 * Get Inventory Item(s) Response
 * @package PHPAccounting\MyobAccountRight\Message\Contacts\Responses
 */
class GetInventoryItemResponse extends AbstractResponse
{

    /**
     * Check Response for Error or Success
     * @return boolean
     */
    public function isSuccessful()
    {
        if(array_key_exists('Errors', $this->data)){
            return !$this->data['Errors'][0]['Severity'] == 'Error';
        }
        return true;
    }

    /**
     * Fetch Error Message from Response
     * @return string
     */
    public function getErrorMessage()
    {
        if (array_key_exists('Errors', $this->data)) {
            if ($this->data['Errors'][0]['Message'] === 'The supplied OAuth token (Bearer) is not valid') {
                return 'The access token has expired';
            }
            else {
                return $this->data['Errors'][0]['Message'];
            }
        }
        return null;
    }

    public function parsePurchaseDetails($data, $item) {
        if ($data) {
            if (array_key_exists('CostOfSalesAccount', $data)) {
                if ($data['CostOfSalesAccount']) {
                    $item['buying_account_code'] = IndexSanityCheckHelper::indexSanityCheck('DisplayID', $data['CostOfSalesAccount']);
                }
            }
            if (array_key_exists('BuyingDetails', $data)) {
                if ($data['BuyingDetails']) {
                    if (array_key_exists('TaxCode', $data['BuyingDetails'])) {
                        if ($data['BuyingDetails']['TaxCode']) {
                            $item['buying_tax_type_code'] = IndexSanityCheckHelper::indexSanityCheck('Code', $data['BuyingDetails']['TaxCode']);
                        }
                    }
                    $item['buying_unit_price'] = IndexSanityCheckHelper::indexSanityCheck('LastPurchasePrice', $data['BuyingDetails']);
                }
            }
        }

        return $item;
    }

    public function parseSellingDetails($data, $item) {
        if ($data) {
            if (array_key_exists('IncomeAccount', $data)) {
                if ($data['IncomeAccount']) {
                    $item['selling_account_code'] = IndexSanityCheckHelper::indexSanityCheck('DisplayID', $data['IncomeAccount']);
                }
            }
            if (array_key_exists('SellingDetails', $data)) {
                if ($data['SellingDetails']) {
                    if (array_key_exists('TaxCode', $data['SellingDetails'])) {
                        if ($data['SellingDetails']['TaxCode']) {
                            $item['selling_tax_type_code'] = IndexSanityCheckHelper::indexSanityCheck('Code', $data['SellingDetails']['TaxCode']);
                        }
                    }
                    $item['selling_unit_price'] = IndexSanityCheckHelper::indexSanityCheck('BaseSellingPrice', $data['SellingDetails']);
                }
            }
        }

        return $item;
    }

    /**
     * Return all Contacts with Generic Schema Variable Assignment
     * @return array
     */
    public function getInventoryItems(){
        $items = [];
        foreach ($this->data['Items'] as $item) {
            $newItem = [];
            $newItem['accounting_id'] = IndexSanityCheckHelper::indexSanityCheck('UID', $item);
            $newItem['code'] = IndexSanityCheckHelper::indexSanityCheck('Number', $item);
            $newItem['name'] = IndexSanityCheckHelper::indexSanityCheck('Name', $item);
            $newItem['description'] = IndexSanityCheckHelper::indexSanityCheck('Description', $item);
            $newItem['type'] = 'UNSPECIFIED';
            $newItem['is_buying'] = IndexSanityCheckHelper::indexSanityCheck('IsBought', $item);
            $newItem['is_selling'] = IndexSanityCheckHelper::indexSanityCheck('IsSold', $item);
            $newItem['buying_description'] = IndexSanityCheckHelper::indexSanityCheck('Description', $item);
            $newItem['selling_description'] = IndexSanityCheckHelper::indexSanityCheck('Description', $item);
            $newItem['quantity'] = IndexSanityCheckHelper::indexSanityCheck('QuantityAvailable', $item);
            $newItem['cost_pool'] = IndexSanityCheckHelper::indexSanityCheck('AverageCost', $item);
            $newItem = $this->parsePurchaseDetails($item, $newItem);
            $newItem = $this->parseSellingDetails($item, $newItem);
            array_push($items, $newItem);
        }

        return $items;
    }
}