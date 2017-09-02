<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class ResetFormRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'token' => 'required',
			'email' => 'required|email',
			'password' => 'required|min:8|confirmed',
		];
	}

}
