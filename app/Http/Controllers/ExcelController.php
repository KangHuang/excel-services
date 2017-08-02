<?php

namespace App\Http\Controllers;

use App\Jobs\ChangeLocale;
use App\Http\Requests\ExcelRequest;
use App\Repositories\ServiceRepository;

class ExcelController extends Controller
{

    
    
        /**
	 * The ServiceRepository instance.
	 *
	 * @var App\Repositories\ServiceRepository
	 */
	protected $service_gestion;
        /**
	 * Create a new ContactController instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('permit');
	}
        
	/**
	 * Display the excel page.
	 *
	 * @return Response
	 */
	public function display($service_id, ServiceRepository $service_gestion)
	{
            
            return view('front.service.excel', $service_gestion->show($service_id));
	}
        
        public function calculate(ExcelRequest $request, $service_id, ServiceRepository $service_gestion)
	{
            
            return view('front.service.excel', $service_gestion->show($service_id));
	}

}