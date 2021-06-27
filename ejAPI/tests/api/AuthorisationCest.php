<?php


class AuthorisationCest
{
    public static function setAuthorisationTokenCustomer(ApiTester $I) {
        $cookie = new Symfony\Component\BrowserKit\Cookie('auth_token', '4BAC27393BDD9777CE02453256C5577CD02275510B2227F473D03F533924F877');
        $I->getClient()->getCookieJar()->set($cookie);
    }
    
    public static function setAuthorisationTokenCustomerRep(ApiTester $I) {
        $cookie = new Symfony\Component\BrowserKit\Cookie('auth_token', '7A130FDF73064886BF6F6ECBB92A1E4850E252759478107D30E2DDEF0D6D7766');
        $I->getClient()->getCookieJar()->set($cookie);
    }
    
    public static function setAuthorisationTokenStorekeeper(ApiTester $I) {
        $cookie = new Symfony\Component\BrowserKit\Cookie('auth_token', 'CA7E3F3F3391B594650E7BA0FA4787C90BCD4A3ABE5224C50C1D255A0A67A891');
        $I->getClient()->getCookieJar()->set($cookie);
    }

    public static function setAuthorisationTokenProductionPlanner(ApiTester $I) {
        $cookie = new Symfony\Component\BrowserKit\Cookie('auth_token', '50AD41624C25E493AA1DC7F4AB32BDC5A3B0B78ECC35B539936E3FEA7C565AF7');
        $I->getClient()->getCookieJar()->set($cookie);
    }
    
    public static function setAuthorisationTokenTransporter(ApiTester $I) {
        $cookie = new Symfony\Component\BrowserKit\Cookie('auth_token', 'AD0A5EECDAD5BC4B6102A8CED84CBCA4CD664BFCFDFC65D7B53B46CB6362AD42');
        $I->getClient()->getCookieJar()->set($cookie);
    }

}
