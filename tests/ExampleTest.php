<?php

class ExampleTest extends TestCase {

    
    protected $baseUrl = 'http://127.0.0.1';
    
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample() {
        $this->visit('/')
                ->click('Home')
                ->see('ABOUT US');
        $this->visit('/')
                ->click('Contact')
                ->see('CONTACT FORM');
        $this->visit('/')
                ->click('Services')
                ->see('Start');
        $this->visit('/')
                ->click('Login')
                ->see('LOG IN');
    }
    
    public function testContact() {
        $this->visit('/contact/create')
         ->type('TestUser', 'name')
         ->type('346193643@qq.com','email')
         ->type('test a contact','message')
         ->Press('Send')
         ->seePageIs('/');
    }
    
    public function testUserRegister() {
        $this->visit('/auth/register')
         ->type('TestUser', 'username')
         ->type('346193643@qq.com','email')
         ->type('11111111','password')
         ->type('11111111','password_confirmation')
         ->select('pro','role')
         ->Press('Send')
         ->see('check your email');
    }
    
    public function testProviderLogin() {
        $this->visit('/auth/login')
         ->type('FirstProvider', 'log')
         ->type('123','password')
         ->select('por','role')
         ->Press('Send')
         ->see('Contribution');
    }

    
    public function testManagerCreateUser() {
        $this->visit('/auth/login')
         ->type('FirstManager', 'log')
         ->type('123','password')
         ->select('use','role')
         ->press('Send')
         ->click('Team')
         ->see('Staff Management')
         ->visit('/user/create')
         ->type('teststaff','name')
         ->type('as@df.df','email')
         ->type('22222222','password')
         ->type('22222222','password_confirmation')
         ->select('role','technical staff')
         ->Press('Send');

    }
    
    

}
