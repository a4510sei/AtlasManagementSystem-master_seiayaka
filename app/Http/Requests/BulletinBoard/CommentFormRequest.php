<?php

namespace App\Http\Requests\BulletinBoard;

use Illuminate\Foundation\Http\FormRequest;

class CommentFormRequest extends FormRequest
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
            'comment' => 'required|string|max:250',
        ];
        return $rules;
    }

    public function messages(){
        return [
            'comment.required' => 'コメントを入力してください。',
            'comment.string' => 'コメントには文字を入力してください。',
            'comment.max' => 'コメントは250字以内で入力してください。',
        ];
    }
}
