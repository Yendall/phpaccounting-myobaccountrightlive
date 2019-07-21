<?php

namespace PHPAccounting\MyobAccountRight\Message\TaxRates\Responses;

use Omnipay\Common\Message\AbstractResponse;
use PHPAccounting\MyobAccountRight\Helpers\IndexSanityCheckHelper;

/**
 * Get ContactGroup(s) Response
 * @package PHPAccounting\MyobAccountRight\Message\ContactGroups\Responses
 */
class GetTaxRateResponse extends AbstractResponse
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

    /**
     * Return all Accounts with Generic Schema Variable Assignment
     * @return array
     */
    public function getTaxRates(){
        $taxRates = [];

        foreach ($this->data['Items'] as $taxRate) {
            $newTaxRate = [];
            $newTaxRate['accounting_id'] =  IndexSanityCheckHelper::indexSanityCheck('UID', $taxRate);
            $newTaxRate['name'] = IndexSanityCheckHelper::indexSanityCheck('Description', $taxRate);
            $newTaxRate['tax_type'] = IndexSanityCheckHelper::indexSanityCheck('Type', $taxRate);
            $newTaxRate['rate'] = IndexSanityCheckHelper::indexSanityCheck('Rate', $taxRate);
            $newTaxRate['is_asset'] = true;
            $newTaxRate['is_equity'] = true;
            $newTaxRate['is_expense'] = true;
            $newTaxRate['is_liability'] = true;
            $newTaxRate['is_revenue'] = true;
            array_push($taxRates, $newTaxRate);
        }

        return $taxRates;
    }
}