<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            // 拡張子が.jpegもしくは.png
            'image' => ['nullable', 'image', 'mimes:jpeg,png'],

            // 入力必須、20文字以内
            'name' => ['required', 'string', 'max:20'],

            // 入力必須、ハイフンありの8文字（例: 123-4567）
            'post_code' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'],

            // 入力必須
            'address' => ['required', 'string', 'max:255'],

            // 任意項目（ソースのテーブル仕様ではNULL許容 [2]）
            'building' => ['nullable', 'string', 'max:255'],

        ];
    }
    public function messages()
    {
        return[
            'image.mimes' => '画像は.jpegまたは.png形式でアップロードしてください',
            'name.required' => 'お名前を入力してください',
            'name.max' => 'お名前は20文字以内で入力してください',
            'post_code.required' => '郵便番号を入力してください',
            'post_code.regex' => '郵便番号はハイフンを含めた8文字で入力してください',
            'address.required' => '住所を入力してください',
        ];
    }
}
