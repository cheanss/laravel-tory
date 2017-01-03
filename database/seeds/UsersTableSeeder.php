<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = factory(User::class)->times(50)->make();
        User::insert($users->toArray());

        $user = User::find(1);
        $user->name = 'Jack.Shi';
        $user->email = 'jack.shi@keerle.net';
        $user->password = 'password';
        $user->is_admin = true;
        $user->save();
    }
}
