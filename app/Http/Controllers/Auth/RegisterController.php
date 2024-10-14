<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\Users\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterFormRequest;
use DB;

use App\Models\Users\Subjects;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    public function registerView()
    {
        $subjects = Subjects::all();
        return view('auth.register.register', compact('subjects'));
    }

    public function registerPost(Request $request)
    {
        DB::beginTransaction();
        try{
            $old_year = $request->old_year;
            $old_month = $request->old_month;
            $old_day = $request->old_day;
            $data = $old_year . '-' . $old_month . '-' . $old_day;
            $birth_day = date('Y-m-d', strtotime($data));
            $subjects = $request->subject;

            // 比較用の本日日付（YYYYMMDD）を取得
            $today = date("Ymd");
            // 比較用の基準日（2000/1/1）を設定
            $start_date = '20000101';
            $old_date = $old_year .$old_month .$old_day;
            $request->validate([
                'over_name' => 'required|string|max:10',
                'under_name' => 'required|string|max:10',
                'over_name_kana' => 'required|string|katakana|max:30',
                'under_name_kana' => 'required|string|katakana|max:30',
                'mail_address' => 'required|email|max:100',
                'sex' => 'required|between:1,3',
                'old_date' => 'date|before:today|after:start_date',
                'old_year'  => 'required_with:old_month,old_day',
                'old_month' => 'required_with:old_year,old_day',
                'old_day'   => 'required_with:old_year,old_month',
                'role' => 'required|between:1,4',
                'password' => 'required|min:8|max:30',
              ],
              [
                'over_name.max' => '姓は10文字以内で入力してください。',
                'under_name.max' => '名は10文字以内で入力してください。',
                'over_name_kana.max' => 'セイは30文字以内で入力してください。',
                'under_name_kana.max' => 'メイは30文字以内で入力してください。',
                'password.min' => 'パスワードは8文字以上で入力してください。',
                'password.max' => 'パスワードは30文字以内で入力してください。',
            ]);
            $user_get = User::create([
                'over_name' => $request->over_name,
                'under_name' => $request->under_name,
                'over_name_kana' => $request->over_name_kana,
                'under_name_kana' => $request->under_name_kana,
                'mail_address' => $request->mail_address,
                'sex' => $request->sex,
                'birth_day' => $birth_day,
                'role' => $request->role,
                'password' => bcrypt($request->password)
            ]);
            $user = User::findOrFail($user_get->id);
            $user->subjects()->attach($subjects);
            DB::commit();
            return view('auth.login.login');
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->route('loginView');
        }
    }
}
