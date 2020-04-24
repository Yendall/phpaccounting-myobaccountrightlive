<?php

namespace PHPAccounting\MyobAccountRightLive\Message\Contacts\Responses\NewEssentials;

use Omnipay\Common\Message\AbstractResponse;
use PHPAccounting\MyobAccountRightLive\Helpers\NewEssentials\ErrorResponseHelper;
use PHPAccounting\MyobAccountRightLive\Helpers\NewEssentials\IndexSanityCheckHelper;

/**
 * Create Contact(s) Response
 * @package PHPAccounting\MyobAccountRightLive\Message\Contacts\Responses\Essentials
 */
class CreateContactResponse extends AbstractResponse
{
    /**
     * Check Response for Error or Success
     * @return boolean
     */
    public function isSuccessful()
    {
        if ($this->data) {
            if(array_key_exists('errors', $this->data)){
                return false;
            }
        } else {
            return false;
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
            return ErrorResponseHelper::parseErrorResponse($this->data['Errors'][0]['Message'], 'Contact');
        } else {
            if (array_key_exists('Items', $this->data)) {
                if (count($this->data['Items']) == 0) {
                    return 'NULL Returned from API or End of Pagination';
                }
            }
        }
        return null;
    }


    /**
     * Return all Contacts with Generic Schema Variable Assignment
     * @return array
     */
    public function getContacts(){
        $contacts = [];
        $contact['accounting_id'] = IndexSanityCheckHelper::indexSanityCheck('UID', $this->data);
        $contact['sync_token'] = IndexSanityCheckHelper::indexSanityCheck('RowVersion', $this->data);
        array_push($contacts, $contact);
        return $contacts;
    }
}