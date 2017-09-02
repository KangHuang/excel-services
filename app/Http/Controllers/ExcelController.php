<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use App\Http\Requests\ExcelRequest;
use App\Repositories\ServiceRepository;

use PHPExcel,
    PHPExcel_IOFactory,
    PHPExcel_Cell;

class ExcelController extends Controller {

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
    public function __construct() {
//		$this->middleware('permit');
    }
    
    /**
     * Get the highest coordinate of valid cells
     *
     * @return array
     */
    public function getHighestRowCol($sheet, $lastRow, $lastCol) {
        $highestRow = 1;
        $highestCol = 0;
        for ($row = $lastRow; $row >= 1; $row--) {
            for ($col = $lastCol-1; $col >= 0; $col--) {
                if ($sheet->getCellByColumnAndRow($col, $row)->getValue() != NULL && $sheet->getCellByColumnAndRow($col, $row)->getValue() != "") {
                    $highestRow = $row;
                    break 2;
                }
            }
        }

        for ($col = $lastCol-1; $col >= 0; $col--) {
            for ($row = $lastRow; $row >= 1; $row--) {
                if ($sheet->getCellByColumnAndRow($col, $row)->getValue() != NULL && $sheet->getCellByColumnAndRow($col, $row)->getValue() != "") {
                    $highestCol = $col;
                    break 2;
                }
            }
        }
        return [$highestRow, $highestCol];
    }

    /**
     * Get the lowest coordinate of valid cells
     *
     * @return array
     */
    public function getLowestRowCol($sheet, $highestRow, $highestCol) {
        $lowestRow = $highestRow;
        $lowestCol = $highestCol;
        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 0; $col < $highestCol; $col++) {
                if ($sheet->getCellByColumnAndRow($col, $row)->getValue() != NULL && $sheet->getCellByColumnAndRow($col, $row)->getValue() != "") {
                    $lowestRow = $row;
                    break 2;
                }
            }
        }

        for ($col = 0; $col < $highestCol; $col++) {
            for ($row = 1; $row <= $highestRow; $row++) {
                if ($sheet->getCellByColumnAndRow($col, $row)->getValue() != NULL && $sheet->getCellByColumnAndRow($col, $row)->getValue() != "") {
                    $lowestCol = $col;
                    break 2;
                }
            }
        }
        return [$lowestRow, $lowestCol];
    }

    public function calculate(ExcelRequest $request, $service_id, ServiceRepository $service_gestion) {


        $user_role = session()->get('statut');
        if (!Redis::command('hexists', ['service' . $service_id, 'highestRow'])) {
            $service = $service_gestion->getById($service_id);
            $file_tec = $service->filename;

//input cell's coordinate to be hidden such as "A1,B2,D2"
            $hid_tec = $service->hid_tec;
            $hid_fin = $service->hid_fin;


            $file = public_path('excel/' . $file_tec);
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
            $objExcel = $objReader->load($file);

            $sheet = $objExcel->getSheetByName('input');
            if ($sheet == null) {
                if (session('statut') === 'admin')
                    return redirect('service/order')->with('error', 'The format is incorrect, '
                                    . 'please ensure input and output sheets are contained');
                else
                    return redirect('services')->with('error', 'There is something wrong with the service, '
                                    . 'please contact the service provider');
            }

            
            //the last Row detected by PHPExcel built-in functions
            $lastRow = $sheet->getHighestRow();
            
            $lastColChar = $sheet->getHighestColumn();
            
            //the last Col detected by PHPexcel built-in functions
            $lastCol = PHPExcel_Cell::columnIndexFromString($lastColChar);
            
            //get last Row and Col with valid values with designed functions
            $highestCoordinate = $this->getHighestRowCol($sheet, $lastRow, $lastCol);
            $highestRow = $highestCoordinate[0];
            $highestCol = $highestCoordinate[1];
            
            //get first Row and Col with valid values with designed functions
            $lowestCoordinate = $this->getLowestRowCol($sheet, $highestRow, $highestCol);
            $lowestRow = $lowestCoordinate[0];
            $lowestCol = $lowestCoordinate[1];

            echo $highestRow.' '.$highestCol.' '.$lowestRow.' '.$lowestCol;


            //enter input data and calculate
            if (isset($_POST['cellvalue'])) {
                $cells = $_POST['cellvalue'];
                foreach ($cells as $col => $vars) {
                    foreach ($vars as $row => $var) {
                        $sheet->getCellByColumnAndRow($col, $row)->setValue($var);
                    }
                }
            }

            $sheet2 = $objExcel->getSheetByName('output');
            if ($sheet2 == null) {
                if (session('statut') === 'admin')
                    return redirect('service/order')->with('error', 'The format is incorrect, '
                                    . 'please ensure input and output sheets are contained');
                else
                    return redirect('services')->with('error', 'There is something wrong with the service, '
                                    . 'please contact the service provider');
            }
            
            //the same process for output
            $lastRow2 = $sheet2->getHighestRow();
            $lastColChar2 = $sheet2->getHighestColumn();
            $lastCol2 = PHPExcel_Cell::columnIndexFromString($lastColChar);
            
            $highestCoordinate = $this->getHighestRowCol($sheet2, $lastRow2, $lastCol2);
            $highestRow2 = $highestCoordinate[0];
            $highestCol2 = $highestCoordinate[1];
            
            $lowestCoordinate = $this->getLowestRowCol($sheet2, $highestRow2, $highestCol2);
            $lowestRow2 = $lowestCoordinate[0];
            $lowestCol2 = $lowestCoordinate[1];
            
            //the same as output
            
            

            Redis::command('hmset', ['service' . $service_id, 'objExcel', serialize($objExcel),
                'highestRow', $highestRow, 'highestRow2', $highestRow2,
                'highestCol', $highestCol, 'highestCol2', $highestCol2,
                'lowestRow', $lowestRow, 'lowestRow2', $lowestRow2,
                'lowestCol', $lowestCol, 'lowestCol2', $lowestCol2,
                'hid_fin', $hid_fin, 'hid_tec', $hid_tec,
            ]);
        }

        else {
            $service = $service_gestion->getById($service_id);
            $objExcel = unserialize(Redis::command('hget', ['service' . $service_id, 'objExcel']));
            $highestRow = (int) Redis::command('hget', ['service' . $service_id, 'highestRow']);
            $highestRow2 = (int) Redis::command('hget', ['service' . $service_id, 'highestRow2']);
            $highestCol = (int) Redis::command('hget', ['service' . $service_id, 'highestCol']);
            $highestCol2 = (int) Redis::command('hget', ['service' . $service_id, 'highestCol2']);
            $lowestRow = (int) Redis::command('hget', ['service' . $service_id, 'lowestRow']);
            $lowestRow2 = (int) Redis::command('hget', ['service' . $service_id, 'lowestRow2']);
            $lowestCol = (int) Redis::command('hget', ['service' . $service_id, 'lowestCol']);
            $lowestCol2 = (int) Redis::command('hget', ['service' . $service_id, 'lowestCol2']);
            $hid_fin = Redis::command('hget', ['service' . $service_id, 'hid_fin']);
            $hid_tec = Redis::command('hget', ['service' . $service_id, 'hid_tec']);

            $sheet = $objExcel->getSheetByName('input');

            //enter input data and calculate
            if (isset($_POST['cellvalue'])) {
                $cells = $_POST['cellvalue'];
                foreach ($cells as $col => $vars) {
                    foreach ($vars as $row => $var) {
                        $sheet->getCellByColumnAndRow($col, $row)->setValue($var);
                    }
                }
            }

            $sheet2 = $objExcel->getSheetByName('output');
        }


        return view('front.service.excel', compact('service', 'sheet', 'sheet2', 'highestRow', 'highestRow2', 'highestCol', 'highestCol2', 'lowestRow', 'lowestRow2', 'lowestCol', 'lowestCol2', 'hid_fin', 'hid_tec', 'user_role'));
    }

}
