<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    /**
     *  rules()の前に実行される
     *       $this->merge(['key' => $value])を実行すると、
     *       フォームで送信された(key, value)の他に任意の(key, value)の組み合わせをrules()に渡せる
     */
    public function getValidatorInstance()
    {
        // 比較用の本日日付（YYYYMMDD）を取得
        $today = date("Ymd");
        // 比較用の基準日（2000/1/1）を設定
        $start_date = '20000101';
        // 日付を作成(2020-1-20)
        $old_date = $this->input('old_year').$this->input('old_month').$this->input('old_day');

        // rules()に渡す値を追加でセット
        //     これで、この場で作った変数にもバリデーションを設定できるようになる
        $this->merge([
            'old_date' => $old_date,
            'today' => $today,
            'start_date' => $start_date,
        ]);

        return parent::getValidatorInstance();
    }

     public function rules()
    {
        return [
          'over_name' => 'required|string|max:10',
          'under_name' => 'required|string|max:10',
          'over_name_kana' => 'required|string|regex:/^[ア-ン゛゜ァ-ォャ-ョー]+$/u|max:30',
          'under_name_kana' => 'required|string|regex:/^[ア-ン゛゜ァ-ォャ-ョー]+$/u|max:30',
          'mail_address' => 'required|unique:users|email|max:100',
          'sex' => 'required|between:1,3',
          'old_date' => 'required|date|before_or_equal:today|after_or_equal:start_date',
          'old_year'  => 'required_with:old_month,old_day',
          'old_month' => 'required_with:old_year,old_day',
          'old_day'   => 'required_with:old_year,old_month',
          'role' => 'required|between:1,4',
          'password' => 'required|min:8|max:30|same:password_confirmation',

        ];
        return $rules;
    }
    // エラー時のメッセージ
    public function messages(){
        return [
            'over_name.max' => '姓は10文字以内で入力してください。',
            'under_name.max' => '名は10文字以内で入力してください。',
            'over_name_kana.max' => 'セイは30文字以内で入力してください。',
            'under_name_kana.max' => 'メイは30文字以内で入力してください。',
            'over_name_kana.regex' => 'カタカナで入力してください。',
            'under_name_kana.regex' => 'カタカナで入力してください。',
            'mail_address.unique' => '登録済のメールアドレスです。',
            'mail_address.email' => 'メール形式で入力してください。',
            'old_date.date' => '正しい日付を入力してください。',
            'old_date.before_or_equal' => '今日以前の日付で入力してください。',
            'old_date.after_or_equal' => '2020/1/1以降の日付で入力してください。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
            'password.max' => 'パスワードは30文字以内で入力してください。',
            'password.same' => 'パスワードは確認用と同じ値を入力してください。',
        ];
    }

}
