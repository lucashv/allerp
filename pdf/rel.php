<?
#Possibilita a correta operação no IE 
header("Content-type: application/pdf ");
header("Content-Disposition: inline; filename=arquivo.pdf");
 
# Inclui o arquivo com a classe
require('fpdf.php');

$pdf = new FPDF('P','mm','A4');

$pdf->SetFont('courier', '', 9);

$pdf->Open();
$pdf->AddPage();

$txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
$arq = fopen("../ajax/txts/$txt", 'r');

$pdf->setXY(3, 28); 
while(true)       {
	$lin = fgets($arq);
	if ($lin == null)  break;
	
  if (strpos($lin, 'FIM PAGINA')!==false) { 
    $pdf->AddPage();  $pdf->setXY(3, 28); }

  else {    
    if ( strpos($lin, '<negrito>')!==false  ) $font='B';
    else $font='';

    $corFUNDO=0;
    if ( strpos($lin, '<cinza>')!==false  ) {$pdf->SetFillColor(237, 237, 237);$corFUNDO=1;}

    $lin = str_replace('<negrito>', '', $lin);
    $lin = str_replace('<cinza>', '', $lin);

    $pdf->setXY(3, $pdf->getY());
    if (strlen($lin)>130)
      $pdf->SetFont('courier', $font, 6);

    else if (strlen($lin)>82)
      $pdf->SetFont('courier', $font, 7);
    else
      $pdf->SetFont('courier', $font, 9);

        
    $pdf->Cell(0, 5, $lin, 0, 0, 'L',$corFUNDO);
    $pdf->Ln();
  }  
}


fclose($arq);
unlink("../ajax/txts/$txt");

$pdf->Output('naoIMPRIMIR');



?>