<?
//define('CHARSET2', 'utf-8'); 
//function_exists('mb_internal_encoding') AND mb_internal_encoding(CHARSET); 
//ini_set('default_charset', CHARSET); 
//header("Content-Type: text/html; charset=" . CHARSET);
//header("Content-Type: text/html; charset=utf-8");

/******************************************************/
function tabelaPADRAO($aMais, $COLS)      {

$colunas = '';
$TDs = explode('|', $COLS);

for ($i=0; $i<count($TDs); $i++)  {
	$info = explode(',', $TDs[$i] );
	
  if ($info[0]=='1%')
    $colunas = $colunas . "<td align=\"center\" style=\"display:none\"></td>";
  else if(strpos($info[0], '_PRECO')!==false)
    $colunas = $colunas . "<td align=\"right\" width=\" " .
        str_replace('_PRECO', '', $info[0]) . "\">$info[1]</td>";
        
  else if(strpos($info[1], '_RIGHT_')!==false)     
    $colunas = $colunas . "<td align=\"right\" width=\"$info[0]\">".
      str_replace('_RIGHT_', '', $info[1])."</td>";
    
  else    
    $colunas = $colunas . "<td align=\"center\" width=\"$info[0]\">$info[1]</td>";
}	

$html = '<table border="0" style="font-family: Verdana;font-size: 10px;color: black;"  cellspacing="0" cellpadding="3" ' . $aMais . '>' . 
        '<thead class="headerFIXO">' .
  			 '	<tr>' .  $colunas . '</tr>' .
        '</thead>' ;  			 
return  $html;			 
}



/******************************************************/
function tabela1($aMais, $COLS)      {

$colunas = '';
$TDs = explode(';', $COLS);

for ($i=0; $i<count($TDs); $i++)  {
	$info = explode(',', $TDs[$i] );
	
  if ($info[0]=='1%')
    $colunas = $colunas . "<td align=center width=\"$info[0]\" style=\"display:none\">$info[1]</td>";
  else  
    $colunas = $colunas . "<td align=center width=\"$info[0]\">$info[1]</td>";
}	

$html = '<table border=0 style="font-family: Verdana;font-size: 10px;color: black;"  cellspacing=0 cellpadding=3 ' . $aMais . '>' . 
			 '	<tr>' .  $colunas . '</tr>' .
			 '</table>';
return  $html;			 
}




/******************************************************/
function tabelaPadraoComOrganizacao($aMais, $COLS)      {

$colunas = '';
$TDs = explode(';', $COLS);

for ($i=0; $i<count($TDs); $i++)  {
	$info = explode('!', $TDs[$i] );
	
  // se especificado *esconder*, cria coluna oculta... usado normalmente qdo necessario guardar o id da linha
  // sem mostra-lo
  if ($info[0]=='*esconder*')
    $colunas = $colunas . 
      '<td style="display:none"></td>';
  else    
  	$colunas = $colunas . 
      "<td tag=\"$info[1]\" id=\"$info[3]\" style=\"cursor:pointer;\" align=center onclick=\"$info[2]\" width=\"$info[0]\">$info[1]</td>";
}	

$html = '<table border=0 style="font-family: Verdana;font-size: 10px;color: black;"  cellspacing=0 cellpadding=3 ' . $aMais . '>' . 
			 '<thead class=headerFIXO>' .
			 '	<tr>' .  $colunas . '</tr>' .
			 '</thead>';
return  $html;			 
}

/*****************************************************************************************/
function esquemaCORES( $esquema ) {

$corFormJanela='#F6F7F7';
//$corFormJanela='#FFF0CD';        
//$corMouseOver='#9F9E9C';
$corMouseOver='#D4D0C8';
$corMouseDown='#c0ffc0';      

$corFormAuxilio='#D8D7D8';
$corMouseOverAuxilio='#B6BDD2';      
$corMouseDownAuxilio='#9BA8D1';      

$corTextBox='#cddcff';  

 
// muda esquema de cores na session e retorna novo esquema atraves de string 
$_SESSION['cores']=
    "$corFormJanela,$corMouseOver,$corMouseDown,$corFormAuxilio,$corMouseOverAuxilio,$corMouseDownAuxilio,$corTextBox";

$_SESSION['arqCSS'] = $esquema==1 ? 'css/padroes.css' : 'css/padroes2.css'; 
    
return "$corFormJanela,$corMouseOver,$corMouseDown,$corFormAuxilio,$corMouseOverAuxilio,$corMouseDownAuxilio,$corTextBox";
}


          



?>
