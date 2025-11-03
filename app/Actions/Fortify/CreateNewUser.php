<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use App\Rules\MobileNumber;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'role_id' => ['required', 'integer'],
            'gender_id' => ['required', 'integer'],
            'sub_admin_type_id' => ['nullable', 'integer'],
            'user_type_id' => ['nullable', 'integer'],
            'department_id' => ['nullable', 'integer'],
            'address' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:12',  new MobileNumber],
            'birthday' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return User::create([
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'middle_name' => $input['middle_name'],
            'role_id' => $input['role_id'],
            'gender_id' => $input['gender_id'],
            'department_id' => $input['department_id'],
            'address' => $input['address'],
            'birthday' => $input['birthday'],
            'email' => $input['email'],
            'sub_admin_type_id' => $input['sub_admin_type_id'],
            'user_type_id' => $input['user_type_id'],
            'department_id' => $input['department_id'],
            'mobile' => $input['mobile'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
