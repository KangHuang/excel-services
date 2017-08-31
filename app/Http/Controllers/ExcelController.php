<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use App\Jobs\ChangeLocale;
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

    public function getLowestRowCol($sheet, $lastRow, $lastCol) {
        $lowestRow = 1;
        $lowestCol = 0;
        for ($row = 1; $row <= $lastRow; $row++) {
            for ($col = 0; $col < $lastCol; $col++) {
                if ($sheet->getCellByColumnAndRow($col, $row)->getValue() != NULL && $sheet->getCellByColumnAndRow($col, $row)->getValue() != "") {
                    $lowestRow = $row;
                    break 2;
                }
            }
        }

        for ($col = 0; $col < $lastCol; $col++) {
            for ($row = 1; $row <= $lastRow; $row++) {
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
        if (!Redis::command('hexists', ['service' . $service_id, 'lastRow'])) {
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

            $lastRow = $sheet->getHighestRow();
            $lastColumn = $sheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($lastColumn);
            $lowestCoordinate = $this->getLowestRowCol($sheet, $lastRow, $highestColumnIndex);
            $lowestRow = $lowestCoordinate[0];
            $lowestCol = $lowestCoordinate[1];


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
            $lastRow2 = $sheet2->getHighestRow();
            $lastColumn2 = $sheet2->getHighestColumn();
            $highestColumnIndex2 = PHPExcel_Cell::columnIndexFromString($lastColumn);
            $lowestCoordinate = $this->getLowestRowCol($sheet2, $lastRow2, $highestColumnIndex2);
            $lowestRow2 = $lowestCoordinate[0];
            $lowestCol2 = $lowestCoordinate[1];

            Redis::command('hmset', ['service' . $service_id, 'objExcel', serialize($objExcel),
                'lastRow', $lastRow, 'lastRow2', $lastRow2,
                'highestColumnIndex', $highestColumnIndex, 'highestColumnIndex2', $highestColumnIndex2,
                'lowestRow', $lowestRow, 'lowestRow2', $lowestRow2,
                'lowestCol', $lowestCol, 'lowestCol2', $lowestCol2,
                'hid_fin', $hid_fin, 'hid_tec', $hid_tec,
            ]);
        }

        else {
            $service = $service_gestion->getById($service_id);
            $objExcel = unserialize(Redis::command('hget', ['service' . $service_id, 'objExcel']));
            $lastRow = (int) Redis::command('hget', ['service' . $service_id, 'lastRow']);
            $lastRow2 = (int) Redis::command('hget', ['service' . $service_id, 'lastRow2']);
            $highestColumnIndex = (int) Redis::command('hget', ['service' . $service_id, 'highestColumnIndex']);
            $highestColumnIndex2 = (int) Redis::command('hget', ['service' . $service_id, 'highestColumnIndex2']);
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


        return view('front.service.excel', compact('service', 'sheet', 'sheet2', 'lastRow', 'lastRow2', 'highestColumnIndex', 'highestColumnIndex2', 'lowestRow', 'lowestRow2', 'lowestCol', 'lowestCol2', 'hid_fin', 'hid_tec', 'user_role'));
    }

}
