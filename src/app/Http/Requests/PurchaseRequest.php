<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            'payment_method' =>'required', // 支払い方法：選択必須
            'post_code'      => ['required','regex:/^\d{3}-\d{4}$/'],// 配送先情報（郵便番号）：必須
            'address'        =>'required', // 配送先情報（住所）：必須
            'building'       =>'nullable',
        ];
    }
    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
            'post_code.required'      => '配送先を選択してください',
            'post_code.regex'         => '郵便番号はハイフンありの8文字で入力してください',
            'address.required'        => '配送先を選択してください',
        ];
    }
}
