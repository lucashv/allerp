<?
#Possibilita a correta operação no IE 
header("Content-type: application/pdf ");
header("Content-Disposition: inline; filename=arquivo.pdf");
 
# Inclui o arquivo com a classe
require('fpdf.php');

$pdf = new FPDF('L','mm','A4');
$pdf->SetFont('courier', '', 8);



$pdf->Open();
$pdf->AddPage();
$pdf->SetTopMargin(3);
$pdf->SetAutoPageBreak(false);

$txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
$arq = fopen("../ajax/txts/$txt", 'r');

$pdf->setXY(30, 5); 
while(true)       {
	$lin = fgets($arq);
	if ($lin == null)  break;
	
  if (strpos($lin, 'FIM PAGINA')!==false) { 
    $pdf->AddPage();  $pdf->setXY(20, 5); }

  else {    
    if ( strpos($lin, '<negrito>')!==false  ) $font='B';
    else $font='';

    $corFUNDO=0;
    if ( strpos($lin, '<cinza>')!==false  ) {$pdf->SetFillColor(237, 237, 237);$corFUNDO=1;}

    $lin = str_replace('<negrito>', '', $lin);
    $lin = str_replace('<cinza>', '', $lin);
    $pdf->setXY(30, $pdf->getY());
    $pdf->SetFont('courier', $font, 8);

    $pdf->Cell(0, 5, $lin, 0, 0, 'L',$corFUNDO);
    $pdf->Ln();
  }  
}


fclose($arq);
unlink("../ajax/txts/$txt");

$pdf->Output('naoIMPRIMIR');



?>