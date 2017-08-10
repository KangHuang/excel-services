<?php namespace App\Http\Requests;

class ServiceRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'title' => 'required|max:100|unique:services',
			'description' => 'required|max:10000',
			'filename' => 'required|mimes:xlsx|unique:services',
                        'price' => 'required|float',
                        'hid_fin' => 'required|max:1000',
                        'hid_tec' => 'required|max:1000',                        
		];
	}

}