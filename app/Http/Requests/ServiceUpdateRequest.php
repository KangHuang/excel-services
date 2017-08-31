<?php namespace App\Http\Requests;

class ServiceUpdateRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'title' => 'required|max:100|unique:services,title,'.$this->input('service_id'),
			'description' => 'required|max:10000',
                        'price' => 'required|numeric',
                        'hid_fin' => 'required|max:1000',
                        'hid_tec' => 'required|max:1000',                        
		];
	}

}