<?php

use Illuminate\Database\Seeder;
use App\Models\Users\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            // over_name,under_name,over_name_kana,under_name_kana,mail_address,sex,birth_day,role,password
            // 姓,名,セイ,メイ,メールアドレス,性別,生年月日,権限,パスワード
            [
            'over_name' => '田中',
            'under_name' => '一郎',
            'over_name_kana' => 'タナカ',
            'under_name_kana' => 'イチロウ',
            'mail_address' => 'TanakaI@mail.com',
            // sex:1(男性)、2(女性)、3(その他)
            'sex' => '1',
            // birth_day:YYYYMMDD
            'birth_day' => '19990101',
            // 1(講師(国語))、2(講師(数学))、3(教師(英語))、4(生徒)
            'role' => '4',
            'password' => Hash::make('pass1234'),
            ],
        ]);

    }
}
