@extends('front.template')

@section('main')



<div  class="col-sm-12">
    <?php
    $hidearray_tec = array();
    $hidearray_fin = array();

    if ($hid_tec != "no") {   //input 'no' specifies no cell is hidden
        $hidearray_tec = explode(',', $hid_tec);
    }
    if ($hid_fin != "no") {   //input 'no' specifies no cell is hidden
        $hidearray_fin = explode(',', $hid_fin);
    }
    echo '<center>';

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
                echo "<td><input type='text' value='$value' name='cellvalue[$col][$row]' pattern='[+-]?([0-9]*[.])?[0-9]+' required></input></td>";
            else
                echo "<td><input type='text' value='$value' readonly></input></td>";
        }
        echo '</tr>';
    }

    echo '</table>';
    echo "<br><button type='submit'>calculate</button>";
    echo "</form><br>";

    echo '<h5>output</h5>';

    if ($user_role == 'tec' || $user_role == 'manager' || $user_role == 'admin') {
        echo '<center>technical information</center>';
        echo "<form>";
        echo '<table>';
        for ($row = $lowestRow2; $row <= $lastRow2; $row++) {
            echo "<tr>";
            for ($col = $lowestCol2; $col < $highestColumnIndex2; $col++) {
                $coordinate = $sheet2->getCellByColumnAndRow($col, $row)->getCoordinate();
                $value = $sheet2->getCellByColumnAndRow($col, $row)->getCalculatedValue();
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
    if ($user_role == 'fin' || $user_role == 'manager' || $user_role == 'admin') {
        echo '<center>financial information</center>';
        echo "<form>";
        echo '<table>';
        for ($row = $lowestRow2; $row <= $lastRow2; $row++) {
            echo "<tr>";
            for ($col = $lowestCol2; $col < $highestColumnIndex2; $col++) {
                $coordinate = $sheet2->getCellByColumnAndRow($col, $row)->getCoordinate();
                $value = $sheet2->getCellByColumnAndRow($col, $row)->getCalculatedValue();
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

@if(session('statut') == 'manager'||session('statut') == 'tec'||session('statut') == 'fin')
<div class="col-sm-12">
    <br>
    <br>
    <center>
        {!! Form::open(['url' => 'comment', 'method' => 'post']) !!}	
        {!! Form::control('text', 0, 'content', $errors,'Comments') !!}
        {!! Form::hidden('service_id', $service->id) !!}

        {!! Form::submit(trans('front/form.send')) !!}
        {!! Form::close() !!}
    </center>
</div>
@endif

@stop