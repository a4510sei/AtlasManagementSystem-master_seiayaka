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
    public function rules()
    {
        return [
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
        ];
        return $rules;
    }
    // 生年月日チェックのため、年・月・日を結合しold_dateを定義
    public function getValidatorInstance()
    {
        if ($this->input('old_day') && $this->input('old_month') && $this->input('old_year'))
        {
            $oldDate = implode('-', $this->only(['old_year', 'old_month', 'old_day']));
            $this->merge([
                'old' => $oldDate,
            ]);
            // 比較用の本日日付（YYYYMMDD）を取得
            $today = date("Ymd");
            // 比較用の基準日（2000/1/1）を設定
            $start_date = '20000101';
        }
        return parent::getValidatorInstance();
    }
    // エラー時のメッセージ
    public function messages(){
        return [
            'over_name.max' => '姓は10文字以内で入力してください。',
            'under_name.max' => '名は10文字以内で入力してください。',
            'over_name_kana.max' => 'セイは30文字以内で入力してください。',
            'under_name_kana.max' => 'メイは30文字以内で入力してください。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
            'password.max' => 'パスワードは30文字以内で入力してください。',
        ];
    }

}
