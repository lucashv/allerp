<?
header("Content-Type: text/html; charset=iso-8859-1");

session_start();

echo('<script language="javascript">');
echo('parent.showAJAX(0);');
echo('</script>');


require_once( '..'.$_SESSION['barra'].'includes'.$_SESSION['barra'].'definicoes.php'  );
require_once( '..'.$_SESSION['barra'].'includes'.$_SESSION['barra'].'funcoes.php'  );


/* conexao */
$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());

$botaoSAIR="<input type=button value=' << RETORNAR ' onclick=\"document.location='..|baixa.php'\" />";
$botaoSAIR = str_replace('|', chr(47), $botaoSAIR);

if (  $_FILES["file"]["name"] == '' )
  die("<font color=red>ERRO</font><br><br>ARQUIVO NÃO SELECIONADO<br><br>$botaoSAIR");

if (  $_FILES["file"]["type"] != "text/plain" )
  die("<font color=red>ERRO</font><br><br>TIPO ERRADO DE ARQUIVO<br><br>$botaoSAIR");
  

if ($_FILES["file"]["error"] > 0)  
  die("<font color=red>ERRO</font><br><br>CÓDIGO ERRO= ".
      $_FILES["file"]["error"]."<br><br>$botaoSAIR");

/*
echo "Upload: " . $_FILES["file"]["name"] . "<br />";
echo "Type: " . $_FILES["file"]["type"] . "<br />";
echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";


  if (file_exists("upload/" . $_FILES["file"]["name"]))
    {
    echo $_FILES["file"]["name"] . " already exists. ";
    }
  else
    {
    
*/    


move_uploaded_file($_FILES["file"]["tmp_name"], "baixas/" . $_FILES["file"]["name"]) or 
  die("<font color=red>ERRO</font><br><br>ERRO DE UPLOAD<br><br>$botaoSAIR");


$arq = fopen('baixas/'.$_FILES["file"]["name"], 'r');
$form = '';

$erro='';

$PF = false;     // pessoa fisica, comissões pessoa física

$vlr=0;

$nomeARQBaixado = $_FILES["file"]["name"];
$txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
$ArqSAIDA = fopen("../ajax/txts/$txt", 'w');

$txtERRO = 'erros_'.$_SERVER['REMOTE_ADDR'];  $txtERRO = str_replace('.', '_', $txtERRO);  $txtERRO .= ".txt";
$ArqERROS = fopen("../ajax/txts/$txtERRO", 'w');

$erros=0;

$contLIN =0;
$contBAIXAS=0;

$ano = date('Y');
while(true)       {
	$lin = fgets($arq);
	if ($lin == null)  break;
	
	$contLIN++;
	
	if ( strpos(strtolower($lin),  'comissionamento de pf')!==false ) $PF=true;

  // a CLinipam nao usa ano na data de pgto da parcela........entao....pegamos o ano 
  // na frase "Referência: Pagamento de"  e concatenamos para formar o ano do pgto da parcela   
	if(   (strpos(strtolower($lin),  'refer')!==false) && 
	      (strpos(strtolower($lin),  'ncia: pagamento de')!==false) )  
    $ano = substr($lin, strpos(strtolower($lin), 'ncia: pagamento de')+25, 4);

  // se linha é de comissao de pessoa física, processa
  if ( substr($lin, 0, 9)=='COMISSOES' && $PF ){
    
    // deixa somente 1 espaço entre as palavras
    $lin = str_replace('/ ', '/', $lin);

    while (strpos($lin, '  ')!==false ) {
      $lin = str_replace('  ', ' ', $lin);
    }    

  
    $info = explode(' ', $lin);
    
    // num contrato
    $infoPROPOSTA = explode('/', $info[1]);
    $numPROPOSTA = $infoPROPOSTA[1];
    
//    print_r($info);die();
    // parcela
    $parcela=-1;
    $achouCONTRATANTE=false;
    $achouPARCELA=false;
    
    for ($tt=4; $tt< count($info); $tt++)  {
      if (! is_numeric( $info[$tt] )) $achouCONTRATANTE=true;
      
      if ($achouCONTRATANTE)   {
        for ($ee=0; $ee< strlen($info[$tt]); $ee++)  {

          if ( is_numeric( substr($info[$tt], $ee, 1) ) )   {
            $colunaPARCELA = $tt;
        
            $parcela = substr($info[$tt], $ee, 1); 
            $achouPARCELA=true;
            break;
          }
        }
      }
      
      if ($achouPARCELA) break;            
    }
    
    if ($parcela==-1) {
      $erros++;
      
      fwrite($ArqERROS, "linha $contLIN=    $lin \n");
      continue;
    }  
    
    $dataBAIXA = explode('/', $info[ $colunaPARCELA+3 ]);     // mes/ano    
    $vlrPAGO = str_replace(',', '.', $info[ $colunaPARCELA+4 ]);
    
    $ok = false;
    if ( is_numeric($numPROPOSTA) && is_numeric($parcela) && is_numeric($vlrPAGO) && count($dataBAIXA)==2 )    {
    
      // ASSUMINDO ARBITRARIAMENTE O ANO ATUAL 
  //    $ano = date('Y');
      $mes = $dataBAIXA[1];   
      $dia = $dataBAIXA[0];
 
      if ( checkdate($mes, $dia, $ano) && $parcela>=1 and $parcela<=20 )  {
        $ok=true;
        $mes = str_pad($mes, 2, '0', 0); 
        $dia = str_pad($dia, 2, '0', 0);
        $dataBAIXA = "$ano$mes$dia";
      }  
    }
    if ($ok) {
       $contBAIXAS++;
       $vlr += (float)$vlrPAGO;    
     
       fwrite($ArqSAIDA, "$numPROPOSTA|$parcela|$dataBAIXA|$vlrPAGO\n");
    } else {
       $erros++;        
       fwrite($ArqERROS, "linha $contLIN=    $lin \n");
    }       
          
  }  	
}

fclose($arq);
fclose($ArqSAIDA);
fclose($ArqERROS);

if ($erro==1)
  die("<font color=red>ERRO AO LER ARQUIVO!</font><br><br>Erro= $erro<br><br>$botaoSAIR"); 

$vlr = number_format($vlr, 2, ',', '')  ;

// tudo ok, pede confirmação
if ($contBAIXAS>0) {
 $botao="<input type=button value=' PROCESSAR BAIXAS DO ARQUIVO >> ' onclick=\"parent.baixa('$nomeARQBaixado');\" />";
 $botao = str_replace('|', chr(47), $botao);
} else {
 $botao="<font face=verdana color=red>* NENHUMA PARCELA ENCONTRADA *</font>";
 $botao = str_replace('|', chr(47), $botao);
} 

$botaoERRO='';
if ($erros>0) 
  $botaoERRO="<input type=button value=' Ver erros ' onclick=\"window.open('../ajax/txts/$txtERRO')\" />"; 

die("<font face='verdana' color=red>Arquivo lido!</font><br><br><font size='+1' face='verdana'>$contBAIXAS ".
  "parcelas, &nbsp;&nbsp;&nbsp;&nbsp;Total R$: $vlr<br>Erros encontrados: $erros".
  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$botaoERRO<br><br><br></font>".
  "$botaoSAIR&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$botao");  

 


/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


die();

?>


