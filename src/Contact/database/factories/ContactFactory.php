<?php

namespace Octo\Contact\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Octo\Contact\Models\Contact;

class ContactFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contact::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            // 'contact_type' => $this->faker->word,
            // 'contact_id' => $this->faker->numberBetween(-10000, 10000),
            'status' => $this->faker->boolean,
            'name' => $this->faker->name,
            'properties' => [
                'description' => $this->faker->sentence,
            ],
            'nickname' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'phone_number' => $this->faker->phoneNumber,
            'phone_number_is_whatsapp' => $this->faker->boolean,
            'birthday' => $this->faker->date,
            'gender' => $this->faker->word,
            'favorite' => $this->faker->boolean,
            'notificable' => $this->faker->boolean,
            'loggable' => $this->faker->boolean,
            // 'deleted_at' => $this->faker->word,
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Contact $contact) {
            $contact->syncTagsWithType($this->faker->words(rand(0, 2)), 'contacts.tags');
        });
    }
}
