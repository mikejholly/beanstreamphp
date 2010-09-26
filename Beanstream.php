<?php

class BeanstreamBilling {

  protected $name;
  protected $email;
  protected $phone;
  protected $address;
  protected $postalCode;
  protected $province;
  protected $city;
  protected $country;
  
  public function getName() {
    return $this->name;
  }
  
  public function setName($name) {
    $this->name = $name;
  }
  
  public function getEmail() {
    return $this->email;
  }
  
  public function setEmail($email) {
    $this->email = $email;
  }
  
  public function getPhone() {
    return $this->phone;
  }
  
  public function setPhone($phone) {
    $this->phone = $phone;
  }
  
  public function getAddress() {
    return $this->address;
  }
  
  public function setAddress($address) {
    $this->address = $address;
  }
  
  public function getPostalCode() {
    return $this->postalCode;
  }
  
  public function setPostalCode($postalCode) {
    $this->postalCode = $postalCode;
  }
  
  public function getCity() {
    return $this->city;
  }
  
  public function setCity($city) {
    $this->city = $city;
  }
  
  public function getProvince() {
    return $this->province;
  }
  
  public function setProvince($province) {
    $this->province = $province;
  }
  
  public function getCountry() {
    return $this->country;
  }
  
  public function setCountry($country) {
    $this->country = $country;
  }
  
  public function toArray() {
    return array(
      'ordName' => $this->name,
      'ordEmailAddress' => $this->email,
      'ordPhoneNumber' => $this->phone,
      'ordAddress1' => $this->address,
      'ordPostalCode' => $this->postalCode,
      'ordCity' => $this->city,
      'ordProvince' => $this->province,
      'ordCountry' => $this->country,
    );
  }
  
  public function fromArray($map) {
    $this->setName($map['ordName']);
    $this->setEmail($map['ordEmailAddress']);
    $this->setPhone($map['ordPhoneNumber']);
    $this->setAddress($map['ordAddress1']);
    $this->setPostalCode($map['ordPostalCode']);
    $this->setCity($map['ordCity']);
    $this->setProvince($map['ordProvince']);
    $this->setCountry($map['ordCountry']);
  }
}

class BeanstreamCard {
  
  protected $owner;
  protected $number;
  protected $expiryMonth;
  protected $expiryYear;
  protected $cvd;
  
  public function getOwner() {
    return $this->owner;
  }
  
  public function setOwner($owner) {
    $this->owner = $owner;
  }
  
  public function getNumber() {
    return $this->number;
  }
  
  public function setNumber($number) {
    $this->number = $number;
  }
  
  public function getExpiryMonth() {
    return $this->expiryMonth;
  }
  
  public function setExpiryMonth($expiryMonth) {
    $this->expiryMonth = $expiryMonth;
  }
  
  public function getExpiryYear() {
    return $this->expiryYear;
  }
  
  public function setExpiryYear($expiryYear) {
    $this->expiryYear = $expiryYear;
  }
  
  public function getCvd() {
    return $this->cvd;
  }
  
  public function setCvd($cvd) {
    $this->cvd = $cvd;
  }
  
  public function toArray() {
    return array(
      'trnCardOwner' => $this->owner,
      'trnCardNumber' => $this->number,
      'trnExpMonth' => $this->expiryMonth,
      'trnExpYear' => $this->expiryYear,
      'trnCardCvd' => $this->cvd,
    );
  }
  
  public function fromArray($map) {
    $this->setOwner($map['trnCardOwner']);
    $this->setNumber($map['trnCardNumber']);
    $this->setExpiryMonth($map['trnExpMonth']);
    $this->setExpiryYear($map['trnExpYear']);
    $this->setCvd($map['trnCardCvd']);
  }
}

class BeanstreamProfile {
  
  protected $customerCode;
  protected $merchantId;
  protected $passCode;
  
  const STATUS_NEW      = 'N';
  const STATUS_CLOSE    = 'C';
  const STATUS_DISABLE  = 'D';
  const STATUS_ENABLE   = 'A';
  
  public function __construct($merchantId, $passCode) {
    $this->merchantId = $merchantId;
    $this->passCode = $passCode;
  }
  
  public function getMerchantId() {
    return $this->merchantId;
  }
  
  public function setMerchantId($merchantId) {
    $this->merchantId = $merchantId;
  }
  
  public function getPassCode() {
    return $this->passCode;
  }
  
  public function setPassCode($passCode) {
    $this->passCode = $passCode;
  }
  
  public function getCustomerCode() {
    return $this->customerCode;
  }
  
  public function setCustomerCode($customerCode) {
    $this->customerCode = $customerCode;
  }
  
  public function getBilling() {
    return $this->billing;
  }
  
  public function setBilling($billing) {
    $this->billing = $billing;
  }
  
  public function getCard() {
    return $card;
  }
  
  public function setCard($card) {
    $this->card = $card;
  }
  
  public static function load($merchantId, $passCode, $customerCode) {
    $params = array(
      'serviceVersion' => '1.1',
      'responseFormat' => 'QS',
      'operationType'  => 'Q',
      'merchantId'     => $merchantId,
      'passCode'       => $passCode,
      'customerCode'   => $customerCode,
    );
    
    $request  = new BeanstreamRequest($params, Beanstream::URL_PROFILE);
    $response = $request->makeRequest();
    
    throw new BeanstreamInvalidProfileException();
  }
  
  public function save($statusCode = 'A', $validateCard = false) {
    $isNew = empty($this->customerCode);
        
    $params = array(
      'serviceVersion' => '1.1',
      'responseFormat' => 'QS',
      'operationType'  => $isNew ? 'N' : 'M',
      'cardValidation' => (int)$validateCard,
      'merchantId'     => $this->merchantId,
      'passCode'       => $this->passCode,
      'status'         => $statusCode,
    );
    
    $params += $this->billing->toArray();
   
    if ($isNew) {
      if (empty($this->card)) {
        throw new InvalidArgumentException('No credit card data provided.');
      }
      if (empty($this->billing)) {
        throw new InvalidArgumentException('No billing data provided.');
      }
      $params += $this->card->toArray();
    }
    else {
      $params['customerCode'] = $this->customerCode;
    }
    $request = new BeanstreamRequest($params, Beanstream::URL_PROFILE);
    $response = $request->makeRequest();
    if ($response->getValue('responseCode') == 1) {
      $this->customerCode = $response->getValue('customerCode');
      return TRUE;
    }
    else {
      throw new BeanstreamException($response->getValue('responseCode'), $response->getValue('responseMessage'));
    }
  }
  
  public function enable() {
    return $this->save(self::STATUS_ENABLE);
  }
  
  public function disable() {
    return $this->save(self::STATUS_DISABLE);
  }
  
  public function close() {
    return $this->save(self::STATUS_CLOSE);
  }
}

class Beanstream {
  const URL_PROCESS = 'https://www.beanstream.com/scripts/process_transaction.asp';
  const URL_PROFILE = 'https://www.beanstream.com/scripts/payment_profile.asp';
  const URL_RECUR   = 'https://www.beanstream.com/scripts/recurring_billing.asp';
}

class BeanstreamRequest {
  
  protected $params;
  protected $url;

  public function __construct($params, $url) {
    $this->url = $url; 
    $this->params = $params;
  }

  public function makeRequest() {
    $ch = curl_init();
    $data = http_build_query($this->params, NULL, '&');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_URL, $this->url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($ch);
    return new BeanstreamResponse($response);
  }
}

class BeanstreamResponse {
  protected $map;
  
  public function __construct($data) {
    $this->_parse($data);
  }
  
  protected function _parse($data) {
    parse_str($data, $this->map);
  }
  
  public function getValue($name) {
    return !empty($this->map[$name]) ? $this->map[$name] : NULL;
  }
}

class BeanstreamTransaction {
  
  const TYPE_PURCHASE       = 'P';
  const TYPE_REFUND         = 'R';
  const TYPE_VOID_PURCHASE  = 'VP';
  const TYPE_VOID_REFUND    = 'VR';
  const TYPE_PURCHASE_AUTH  = 'PA';
  
  protected $card;
  protected $billing;
  protected $amount;
  protected $description;
  protected $requestType = 'BACKEND';
  protected $type;
  protected $url;
  protected $username;
  protected $password;
  
  public function __construct($merchantId) {
    $this->merchantId = $merchantId;
    $this->url = Beanstream::URL_PROCESS;
    $this->type = self::TYPE_PURCHASE;
  }
  
  public function getType() {
    return $this->type;
  }
  
  public function setType($type) {
    $this->type = $type;
  }
  
  public function getCard() {
    return $this->card;
  }
  
  public function setCard($card) {
    $this->card = $card;
  }
  
  public function getBilling() {
    return $this->billing;
  }
  
  public function setBilling($billing) {
    $this->billing = $billing;
  }
  
  public function getUsername() {
    return $this->username;
  }
  
  public function setUsername($username) {
    $this->username = $username;
  }
  
  public function getPassword() {
    return $this->password;
  }
  
  public function setPassword($password) {
    $this->password = $password;
  }
  
  public function getAmount() {
    return $this->amount;
  }
  
  public function setAmount($amount) {
    if (!is_numeric($amount)) {
      throw new InvalidArgumentException('Invalid amount.');
    }
    $this->amount = $amount;
  }
  
  public function getDescription() {
    return $this->description;
  }
  
  public function setDescription($description) {
    $this->description = $description;
  }
  
  public function getOrderNumber() {
    return $this->orderNumber;
  }
  
  public function setOrderNumber($orderNumber) {
    $this->orderNumber = $orderNumber;
  }
  
  public function getRef() {
    return $this->ref;
  }
  
  public function setRef($ref) {
    $this->ref = $ref;
  }
  
  public function getUrl() {
    return $this->url;
  }
  
  public function setUrl($url) {
    $this->url = $url;
  }
  
  public function getCustomerCode() {
    return $this->customerCode;
  }
  
  public function setCustomerCode($customerCode) {
    $this->customerCode = $customerCode;
  }
  
  protected function getParams() {
    return array(
      'requestType' => $this->requestType,
      'merchant_id' => $this->merchantId,
      'trnType' => $this->type,
      'trnOrderNumber' => $this->orderNumber,
      'trnAmount' => $this->amount,
      'ref1' => $this->ref,
      'username' => $this->username,
      'password' => $this->password,
    );
  }
  
  public function process() {
    $params = $this->getParams(); 
    if (!empty($this->customerCode)) {
      $params['customerCode'] = $this->customerCode;
    }
    else {
      if (!empty($this->billing)) {
        $params += $this->billing->toArray();
      }
      if (!empty($this->card)) {
        $params += $this->card->toArray();
      }
    }
    $request = new BeanstreamRequest($params, Beanstream::URL_PROCESS);
    return $request->makeRequest();
  }
}

class BeanstreamException extends Exception {

  protected $code;
  protected $message;

  public function __construct($code, $message) {
    $this->code = $code;
    $this->message = $message;
  }

  public function __toString() {
    return sprintf('%d - %s', $this->code, $this->message);
  }
}