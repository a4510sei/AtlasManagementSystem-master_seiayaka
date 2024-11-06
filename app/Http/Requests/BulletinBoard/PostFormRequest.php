<?php

namespace App\Http\Requests\BulletinBoard;

use Illuminate\Foundation\Http\FormRequest;

class PostFormRequest extends FormRequest
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

        public function getValidatorInstance()
    {
        // 比較用の本日日付（YYYYMMDD）を取得
        $main_category = $this->main_category_name;
        $sub_category = $this->sub_category_name;

        // rules()に渡す値を追加でセット
        //     これで、この場で作った変数にもバリデーションを設定できるようになる
        $this->merge([
            'main_category' => $main_category,
            'sub_category' => $sub_category,
        ]);

        return parent::getValidatorInstance();
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'post_category_id' => 'required_with:post_title,post_body|exists:sub_categories,id',
            'post_title' => 'min:4|max:100',
            //  メインカテゴリー、サブカテゴリー登録以外では入力必須
            'post_body' => 'required_without_all:main_category_id,sub_category|min:10|max:5000',
            // メインカテゴリー、サブカテゴリーはどちらか入力必須
            'main_category' => 'required_without_all:main_category_id,sub_category,post_title,post_body|unique:main_categories|max:100',
            'main_category_id' => 'required_with:sub_category|exists:main_categories,id',
            'sub_category' => 'required_with:main_category_id|unique:sub_categories|max:100',
        ];
        return $rules;
    }

    public function messages(){
        return [
            'post_body.required_without_all' => '投稿内容は入力必須です。',
            'post_title.min' => 'タイトルは4文字以上入力してください。',
            'post_title.max' => 'タイトルは100文字以内で入力してください。',
            'post_body.min' => '内容は10文字以上入力してください。',
            'post_body.max' => '内容は5000文字以内で入力してください。。',
            'main_category.max' => 'カテゴリー名は100文字以内で入力してください。',
            'sub_category.max' => 'カテゴリー名は100文字以内で入力してください。',
            'main_category.required_without_all' => 'メインカテゴリー名、サブカテゴリーのどちらかは入力必須です。',
            'main_category_id.required_with' => 'メインカテゴリーは選択必須です',
            'SUB_category_id.required_with' => 'サブカテゴリー名は入力必須です',
            'main_category.unique' => 'そのカテゴリー名は既に登録されています。',
            'sub_category.unique' => 'そのカテゴリー名は既に登録されています。',
            'main_category_id.exists' => '存在しないメインカテゴリーです。',
        ];
    }
}
