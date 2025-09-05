<?php

declare(strict_types=1);

use App\Model\User;
use Hyperf\Database\Seeders\Seeder;
use Faker\Factory;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // https://github.com/fzaninotto/Faker
        $faker = Factory::create('zh_CN');

        User::truncate();

        for ($i = 0; $i < 5; $i++) {
            User::create([
                'name'          => $faker->name,
                'mobile'        => $faker->regexify('1[3-9]\d{9}'),
                'password'      => password_hash('123123', PASSWORD_BCRYPT),
                'avatar'        => $faker->imageUrl(100, 100),
                // 'email'         => $faker->email,
                'gender'        => $faker->randomElement(['男', '女', '未知']),
                // 'is_verified'   => $faker->randomElement([1, 2]),
                // 'last_login_at' => $faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
            ]);
        }
    }
}