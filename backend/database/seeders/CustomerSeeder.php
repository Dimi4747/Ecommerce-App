<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'email' => 'jean.dupont@email.com',
                'phone' => '0123456789',
                'address' => '123 Rue de la République',
                'city' => 'Paris',
                'postal_code' => '75001',
                'country' => 'France'
            ],
            [
                'first_name' => 'Marie',
                'last_name' => 'Martin',
                'email' => 'marie.martin@email.com',
                'phone' => '0234567890',
                'address' => '456 Avenue des Champs-Élysées',
                'city' => 'Paris',
                'postal_code' => '75008',
                'country' => 'France'
            ],
            [
                'first_name' => 'Pierre',
                'last_name' => 'Durand',
                'email' => 'pierre.durand@email.com',
                'phone' => '0345678901',
                'address' => '789 Boulevard Saint-Germain',
                'city' => 'Lyon',
                'postal_code' => '69001',
                'country' => 'France'
            ]
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
