<?php
require_once "PHPExcel/Classes/PHPExcel.php";
require_once 'PHPExcel/Classes/PHPExcel/IOFactory.php';


if(!empty($_FILES["fileToUpload"]["name"]))
{
    $target_file = 'upload/'.basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);

$url = $target_file;
$filecontent = file_get_contents($url);
$tmpfname = tempnam(sys_get_temp_dir(),"tmpxls");
file_put_contents($tmpfname,$filecontent);

$excelReader = PHPExcel_IOFactory::createReaderForFile($tmpfname);
$excelObj = $excelReader->load($tmpfname);
$worksheet = $excelObj->getSheet(0);
$lastRow = $worksheet->getHighestRow();
$invalidNumbers = $validNumbers = $cloumn1 = $cloumn2 = array();
for ($row = 1; $row <= $lastRow; $row++) {
         $cloumn1[] = $worksheet->getCell('A'.$row)->getValue();
         $cloumn2[] = $worksheet->getCell('B'.$row)->getValue();
}
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$row = 1;
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,'Column1');   
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,'status');   

$row++;
foreach($cloumn1 as $cloumn){
    
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$row,$cloumn);    
    if(!empty($cloumn)){
        if(in_array($cloumn, $cloumn2)){
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,'Yes');
            $validNumbers[] = $cloumn;
        }else{
             $objPHPExcel->getActiveSheet()->SetCellValue('B'.$row,'No');    
             $invalidNumbers[] = $cloumn;
        }
    }
   $row++; 
   
}

$row = 1;
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$row,'Column2');   
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$row,'status');   
$row++;
foreach($cloumn2 as $cloumn){
    
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$row,$cloumn);    
    if(!empty($cloumn)){
        if(in_array($cloumn, $cloumn1)){
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$row,'Yes');
            $validNumbers[] = $cloumn;
        }else{
             $objPHPExcel->getActiveSheet()->SetCellValue('E'.$row,'No');    
             $invalidNumbers[] = $cloumn;
        }
    }
   $row++; 
   
}

foreach(range('A','E') as $columnID) 
{
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
        ->setAutoSize(true);
}
                    
$fileName="output.xlsx";
$objPHPExcel->getActiveSheet()->setTitle("output");                    

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$fileName.'"');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
}else{
    echo 'Something went wrong. Try Again';
}
exit;
?>