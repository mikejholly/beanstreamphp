### About

Basic PHP Beanstream API wrapper that supports most of the API operations. Supports basic transactions and Beanstream profile transactions.

### Example Usage

    // Create a credit card object
    $card = new BeanstreamCard();
    $card->setOwner('Michael Holly');
    $card->setNumber('4030000010001234');
    $card->setExpiryMonth(9);
    $card->setExpiryYear(19);
    $card->setCvd(123);

    // Account billing info
    $billing = new BeanstreamBilling();
    $billing->setEmail('mikejholly@gmail.com');
    $billing->setPhone('555-5555');
    $billing->setName('Michael Holly');
    $billing->setAddress('987 Cardero Street');
    $billing->setPostalCode('V6G2G8');
    $billing->setProvince('BC');
    $billing->setCountry('CA');
    $billing->setCity('Vancouver');

    // Create a billing profile with the card and billing info
    $profile = new BeanstreamProfile('<merchant-id>, '<profile-passcode>');
    $profile->setCard($card);
    $profile->setBilling($billing);
    $profile->save();

    // Create and process a new transaction using the passcode
    $trans = new BeanstreamTransaction($merchantId);
    $trans->setCustomerCode($profile->getCustomerCode());
    $trans->setAmount('1.00');
    $trans->setOrderNumber(time());
    $trans->setRef('My test charge');
    $trans->setUsername('<username>');
    $trans->setPassword('<password>');
    $result = $trans->process();