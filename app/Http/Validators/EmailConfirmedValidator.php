<?php namespace App\Http\Validators;

use App;
use App\Services\Settings;
use App\User;
use Hash;
use Illuminate\Validation\Validator;

class EmailConfirmedValidator extends Validator {

    /**
     * Check if user with specified email has confirmed his email address.
     *
     * @param string $attribute
     * @param string $value
     * @param array $parameters
     * @return bool
     */
    public function validateEmailConfirmed($attribute, $value, $parameters) {
        $settings = App::make(Settings::class);

        //don't need to validate email, bail
        if ( ! $settings->get('require_email_confirmation')) return true;

        //if email address is not taken yet, bail
        if ( ! $user = User::where('email', $value)->first()) return true;

        //check if specified email is confirmed
        return (bool) $user->confirmed;
    }
}