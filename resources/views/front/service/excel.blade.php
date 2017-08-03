@extends('front.template')

@section('main')
<div>
    <?php

    function getLowestRowCol($sheet, $lastRow, $lastCol) {
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

    require public_path('Classes/PHPExcel.php');
    $file_tec = $service->filename;

//input cell's coordinate to be hidden such as "A1,B2,D2"
    $hid_tec = $service->hid_tec;
    $hid_fin = $service->hid_fin;

    $user_role = session()->get('statut');
    $hidearray_tec = array();
    $hidearray_fin = array();

    if ($hid_tec != "no") {   //input 'no' specifies no cell is hidden
        $hidearray_tec = explode(',', $hid_tec);
    }
    if ($hid_fin != "no") {   //input 'no' specifies no cell is hidden
        $hidearray_fin = explode(',', $hid_fin);
    }

    $mark = 0; //the symbol of hidden cells


    $file = public_path('excel/' . $file_tec);
    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
    $objExcel = $objReader->load($file);



    $sheet = $objExcel->getSheetByName('input');
    $lastRow = $sheet->getHighestRow();
    $lastColumn = $sheet->getHighestColumn();
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($lastColumn);
    $lowestCoordinate = getLowestRowCol($sheet, $lastRow, $highestColumnIndex);
    $lowestRow = $lowestCoordinate[0];
    $lowestCol = $lowestCoordinate[1];

    echo '<center>';

    if (isset($_POST['cellvalue'])) {
        echo "<div>submitted data</div>";
        $cells = $_POST['cellvalue'];
        foreach ($cells as $col => $vars) {
            foreach ($vars as $row => $var) {
                $coordinate = $sheet->getCellByColumnAndRow($col, $row)->getCoordinate();
                echo $coordinate.': '.$var;
                echo '<br>';
                $sheet->getCellByColumnAndRow($col, $row)->setValue($var);
            }
        }
    }
    echo '<h5>input</h5>';
    echo "<form action=$service->id method='post'>";
    $token = csrf_token();
    echo "<input  name='_token' value='$token' hidden>";
    echo '<table>';

    for ($row = $lowestRow; $row <= $lastRow; $row++) {
        echo "<tr>";
        for ($col = $lowestCol; $col < $highestColumnIndex; $col++) {
            $type = $sheet->getCellByColumnAndRow($col, $row)->getDataType();
            $value = $sheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
            if ($type == PHPExcel_Cell_DataType::TYPE_NUMERIC)
                echo "<td><input type='text' value='$value' name='cellvalue[$col][$row]'></input></td>";
            else
                echo "<td><input type='text' value='$value' readonly></input></td>";
        }
        echo '</tr>';
    }

    echo '</table>';
    echo "<br><button type='submit'>calculate</button>";
    echo "</form><br>";

    echo '<h5>output</h5>';
    $sheet = $objExcel->getSheetByName('output');
    $lastRow = $sheet->getHighestRow();
    $lastColumn = $sheet->getHighestColumn();
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($lastColumn);
    $lowestRow = $lowestCoordinate[0];
    $lowestCol = $lowestCoordinate[1];
    
    if ($user_role == 'tec' || $user_role == 'manager' || $user_role == 'admin') {
        echo '<center>technical information</center>';
        echo "<form>";
        echo '<table>';
        for ($row = $lowestRow; $row <= $lastRow; $row++) {
            echo "<tr>";
            for ($col = $lowestCol; $col < $highestColumnIndex; $col++) {
                $coordinate = $sheet->getCellByColumnAndRow($col, $row)->getCoordinate();
                $value = $sheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                if (in_array($coordinate, $hidearray_tec) && $hid_tec != 'no')
                    echo "<td><input type='text' value='hidden' disabled></input></td>";
                else
                    echo "<td><input type='text' value='$value' readonly></input></td>";
            }
            echo '</tr>';
        }
        echo '</table>';
        echo "</form><br>";
    }
    if ($user_role == 'fin' || $user_role == 'manager'|| $user_role == 'admin') {
        echo '<center>financial information</center>';
        echo "<form>";
        echo '<table>';
        for ($row = $lowestRow; $row <= $lastRow; $row++) {
            echo "<tr>";
            for ($col = $lowestCol; $col < $highestColumnIndex; $col++) {
                $coordinate = $sheet->getCellByColumnAndRow($col, $row)->getCoordinate();
                $value = $sheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                if (in_array($coordinate, $hidearray_fin) && $hid_fin != 'no')
                    echo "<td><input type='text' value='hidden' disabled></input></td>";
                else
                    echo "<td><input type='text' value='$value' readonly></input></td>";
            }
            echo '</tr>';
        }
        echo '</table>';
        echo "</form><br>";
    }
    echo '</center>';
    ?>

</div>
@stop