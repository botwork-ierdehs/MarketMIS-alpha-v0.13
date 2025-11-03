<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "role_id" => 3,
            "gender_id" => 1,
            "first_name" => $this->faker->firstName(),
            "last_name" => $this->faker->lastName(),
            "address" => $this->faker->address(),
            "mobile" => "639" . $this->faker->numerify("#########"),
            "birthday" => Carbon::now()
                ->subYears(20)
                ->format("Y-m-d"),
            "email" => $this->faker->unique()->safeEmail(),
            "email_verified_at" => now(),
            "password" => Hash::make("marketmis"),
            "two_factor_secret" => null,
            "two_factor_recovery_codes" => null,
            "remember_token" => Str::random(10),
            "profile_photo_path" => null,
        ];
    }

    /**
     * Custom static seed users
     */
    public static function seedDefaultUsers()
    {
        $users = [
            [
                "role_id" => 1,
                "gender_id" => 3,
                "first_name" => "Admin",
                "last_name" => "MarketIS",
                "address" => "Sariaya, Quezon",
                "mobile" => "63916625711",
                "birthday" => Carbon::now()
                    ->subYears(20)
                    ->format("Y-m-d"),
                "email" => "marketchristian7@gmail.com",
                "email_verified_at" => now(),
                "password" => Hash::make("marketmis"),
                "two_factor_secret" => null,
                "two_factor_recovery_codes" => null,
                "remember_token" => Str::random(10),
                "profile_photo_path" => null,
            ],
            [
                "role_id" => 2,
                "gender_id" => 2, // Male
                "department_id" => 1,
                "department_position_id" => 3,
                "sub_admin_type_id" => 1,
                "first_name" => "John",
                "last_name" => "Doe",
                "address" => "Lucena City, Quezon",
                "mobile" => "639111111111",
                "birthday" => Carbon::now()
                    ->subYears(28)
                    ->format("Y-m-d"),
                "email" => "john.doe@marketmis.com",
                "email_verified_at" => now(),
                "password" => Hash::make("marketmis"),
                "two_factor_secret" => null,
                "two_factor_recovery_codes" => null,
                "remember_token" => Str::random(10),
                "profile_photo_path" => null,
                "status" => true,
            ],
            [
                "role_id" => 2,
                "gender_id" => 1, // Female
                "department_id" => 2,
                "sub_admin_type_id" => 2,
                "department_position_id" => 1,
                "first_name" => "Jane",
                "last_name" => "Smith",
                "address" => "Tayabas, Quezon",
                "mobile" => "639122222222",
                "birthday" => Carbon::now()
                    ->subYears(25)
                    ->format("Y-m-d"),
                "email" => "jane.smith@marketmis.com",
                "email_verified_at" => now(),
                "password" => Hash::make("marketmis"),
                "two_factor_secret" => null,
                "two_factor_recovery_codes" => null,
                "remember_token" => Str::random(10),
                "profile_photo_path" => null,
                "status" => true,
            ],
            [
                "role_id" => 2,
                "gender_id" => 2,
                "department_id" => 3,
                "sub_admin_type_id" => 3,
                "department_position_id" => 2,
                "first_name" => "Carlos",
                "last_name" => "Reyes",
                "address" => "Sariaya, Quezon",
                "mobile" => "639133333333",
                "birthday" => Carbon::now()
                    ->subYears(32)
                    ->format("Y-m-d"),
                "email" => "carlos.reyes@marketmis.com",
                "email_verified_at" => now(),
                "password" => Hash::make("marketmis"),
                "two_factor_secret" => null,
                "two_factor_recovery_codes" => null,
                "remember_token" => Str::random(10),
                "profile_photo_path" => null,
                "status" => true,
            ],
            [
                "role_id" => 3,
                "gender_id" => 1,
                "first_name" => "John",
                "last_name" => "Doe",
                "address" => "Sariaya, Quezon",
                "mobile" => "63916625711",
                "birthday" => Carbon::now()
                    ->subYears(20)
                    ->format("Y-m-d"),
                "email" => "johndoe@marketmis.com",
                "email_verified_at" => now(),
                "password" => Hash::make("marketmis"),
                "two_factor_secret" => null,
                "two_factor_recovery_codes" => null,
                "remember_token" => Str::random(10),
                "profile_photo_path" => null,
                "status" => true,
            ],
            [
                "role_id" => 2,
                "gender_id" => 1,
                "department_id" => 3,
                "sub_admin_type_id" => 3,
                "department_position_id" => 2,
                "first_name" => "Mika",
                "last_name" => "Lim",
                "address" => "Sariaya, Quezon",
                "mobile" => "639133333333",
                "birthday" => Carbon::now()
                    ->subYears(32)
                    ->format("Y-m-d"),
                "email" => "mika.lim@marketmis.com",
                "email_verified_at" => now(),
                "password" => Hash::make("marketmis"),
                "two_factor_secret" => null,
                "two_factor_recovery_codes" => null,
                "remember_token" => Str::random(10),
                "profile_photo_path" => null,
                "status" => true,
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(["email" => $user["email"]], $user);
        }
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(
            fn(array $attributes) => [
                "email_verified_at" => null,
            ]
        );
    }

    /**
     * Indicate that the user should have a personal team.
     */
    public function withPersonalTeam(?callable $callback = null): static
    {
        if (!Features::hasTeamFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Team::factory()
                ->state(
                    fn(array $attributes, User $user) => [
                        "name" => $user->name . '\'s Team',
                        "user_id" => $user->id,
                        "personal_team" => true,
                    ]
                )
                ->when(is_callable($callback), $callback),
            "ownedTeams"
        );
    }
}
