<?php
/**
 * @copyright Copyright (c) 2015 Orba Sp. z o.o. (http://orba.pl)
 */

namespace Orba\Payupl\Model\Client;

class Order
{
    /**
     * @var Order\DataValidator
     */
    protected $_dataValidator;

    /**
     * @var Order\DataAdder
     */
    protected $_dataAdder;

    /**
     * @var Sdk
     */
    protected $_sdk;

    /**
     * @var \Orba\Payupl\Logger\Logger
     */
    protected $_logger;

    /**
     * @param Order\DataValidator $dataValidator
     * @param Order\DataAdder $dataAdder
     * @param Sdk $sdk
     * @param \Orba\Payupl\Logger\Logger $logger
     */
    public function __construct(
        Order\DataValidator $dataValidator,
        Order\DataAdder $dataAdder,
        Sdk $sdk,
        \Orba\Payupl\Logger\Logger $logger
    )
    {
        $this->_dataValidator = $dataValidator;
        $this->_dataAdder = $dataAdder;
        $this->_sdk = $sdk;
        $this->_logger = $logger;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function validate(array $data = [])
    {
        return
            $this->_dataValidator->validateEmpty($data) &&
            $this->_dataValidator->validateBasicData($data) &&
            $this->_dataValidator->validateProductsData($data);
    }

    /**
     * @param array $data
     * @return array
     */
    public function addSpecialData(array $data = [])
    {
        return array_merge($data, [
            'continueUrl' => $this->_dataAdder->getContinueUrl(),
            'notifyUrl' => $this->_dataAdder->getNotifyUrl(),
            'customerIp' => $this->_dataAdder->getCustomerIp(),
            'merchantPosId' => $this->_dataAdder->getMerchantPosId()
        ]);
    }

    /**
     * @param array $data
     * @return \OpenPayU_Result|bool
     */
    public function create(array $data)
    {
        try {
            return $this->_sdk->orderCreate($data);
        } catch (\Exception $e) {
            $this->_logger->critical($e);
            return false;
        }
    }
}