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
        User::insert($users->makeVisible(['password', 'remember_token'])->toArray());

        $user = User::find(1);
        $user->name = 'bao';
        $user->email = 'zhong1714@qq.com';
        $user->password = bcrypt('bb5211714');
        $user->is_admin = true;
        $user->activated = true;
        $user->save();
    }
}
