<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

use Faker\Factory as FakerFactory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = FakerFactory::create('pt_BR');

        return [
            'name' => $faker->name,
            'email' => $faker->unique()->safeEmail,
            'document' => $faker->cpf()
        ];
    }
}
