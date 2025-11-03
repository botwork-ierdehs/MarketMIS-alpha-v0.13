<?php
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
return [
  [
    'role_id' => 1,
    'gender_id' => 3,
    'first_name' => 'Admin',
    'last_name' => 'MarketIS',
    'address' => 'Sariaya, Quezon',
    'mobile' => '63916625711',
    'birthday' => Carbon::now()->subYears(20)->format('Y-m-d'),
    'email' => 'marketchristian7@gmail.com',
    'email_verified_at' => now(),
    'password' => Hash::make('marketmis'),
    'two_factor_secret' => null,
    'two_factor_recovery_codes' => null,
    'remember_token' => Str::random(10),
    'profile_photo_path' => null,
  ],
  [
    'role_id' => 3,
    'gender_id' => 1,
    'first_name' => 'John',
    'last_name' => 'Doe',
    'address' => 'Sariaya, Quezon',
    'mobile' => '63916625711',
    'birthday' => Carbon::now()->subYears(20)->format('Y-m-d'),
    'email' => 'johndoe@marketmis.com',
    'email_verified_at' => now(),
    'password' => Hash::make('marketmis'),
    'two_factor_secret' => null,
    'two_factor_recovery_codes' => null,
    'remember_token' => Str::random(10),
    'profile_photo_path' => null,
  ],

];
