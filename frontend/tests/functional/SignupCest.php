<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;

class SignupCest
{
    protected $formId = '#form-signup';


    public function _before(FunctionalTester $I)
    {
        $I->amOnRoute('site/signup');
    }

    public function signupWithEmptyFields(FunctionalTester $I)
    {
        $I->see('Create Account', 'h3');
        $I->see('Fill in the details below to create your account');
        $I->submitForm($this->formId, []);
        $I->seeValidationError('Username cannot be blank.');
        $I->seeValidationError('Email cannot be blank.');
        $I->seeValidationError('Password cannot be blank.');
        $I->seeValidationError('First name cannot be blank.');
        $I->seeValidationError('Last name cannot be blank.');
        $I->seeValidationError('Confirm Password cannot be blank.');

    }

    public function signupWithWrongEmail(FunctionalTester $I)
    {
        $I->submitForm(
            $this->formId, [
            'SignupForm[first_name]'  => 'Test',
            'SignupForm[last_name]'   => 'User',
            'SignupForm[username]'    => 'tester',
            'SignupForm[email]'       => 'ttttt',
            'SignupForm[password]'    => 'tester_password',
            'SignupForm[confirm_password]' => 'tester_password',
        ]
        );
        $I->dontSee('Username cannot be blank.', '.invalid-feedback');
        $I->dontSee('Password cannot be blank.', '.invalid-feedback');
        $I->see('Email is not a valid email address.', '.invalid-feedback');
    }

    public function signupSuccessfully(FunctionalTester $I)
    {
        $I->submitForm($this->formId, [
            'SignupForm[first_name]' => 'Test',
            'SignupForm[last_name]' => 'User',
            'SignupForm[username]' => 'tester',
            'SignupForm[email]' => 'tester.email@example.com',
            'SignupForm[password]' => 'tester_password',
            'SignupForm[confirm_password]' => 'tester_password',
        ]);

        $I->seeRecord('common\models\User', [
            'username' => 'tester',
            'email' => 'tester.email@example.com',
            'status' => \common\models\User::STATUS_INACTIVE
        ]);

        $I->seeEmailIsSent();
        $I->see('Thank you for registration. Please check your inbox for verification email.');
    }
}
