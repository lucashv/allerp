<?
header("Content-Type: text/html; charset=utf-8");
require_once 'reader_XLS.php';
require_once( '../includes/definicoes.php'  );
require_once( '../includes/funcoes.php'  );

session_start();

echo('<script language="javascript">');
echo('parent.showAJAX(0);');
echo('function fecha() {');
echo('parent.document.getElementById(\'fraUPLOAD\').src="";');
echo('parent.document.getElementById(\'divUPLOAD\').setAttribute(\'class\', "cssDIV_ESCONDE");');
echo('}');
echo('</script>');



$botaoSAIR="<input type=button value=' << RETORNAR ' onclick=\"document.location='..|planilhaPJ.php'\" />";
$botaoSAIR = str_replace('|', chr(47), $botaoSAIR);


if (  $_FILES["file"]["name"] == '' )
  die("<font color=red>ERRO</font><br><br>ARQUIVO NÃO SELECIONADO<br><br>$botaoSAIR");

if (  $_FILES["file"]["type"] != "application/vnd.ms-excel" )
  die("<font color=red>ERRO</font><br><br>TIPO ERRADO DE ARQUIVO<br><br>$botaoSAIR");
  

if ($_FILES["file"]["error"] > 0)  
  die("<font color=red>ERRO</font><br><br>CÓDIGO ERRO= ".
      $_FILES["file"]["error"]."<br><br>$botaoSAIR");

move_uploaded_file($_FILES["file"]["tmp_name"], "planilhasPJ/" . $_FILES["file"]["name"]) or 
  die("<font color=red>ERRO</font><br><br>ERRO DE UPLOAD<br><br>$botaoSAIR");


//die( 'planilhasPJ/'.$_FILES["file"]["name"] );
$data = new Spreadsheet_Excel_Reader();
$data->setOutputEncoding('CP1251');

$data->read("planilhasPJ/".$_FILES["file"]["name"]);

error_reporting(E_ALL ^ E_NOTICE);

//$txt = $_SERVER['REMOTE_ADDR'];  $txt = str_replace('.', '_', $txt);  $txt = $txt . ".txt";
//$arqSAIDA = fopen("../ajax/planilhasPJ/$txt", 'w');


/* conexao */
$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());

$idempresa = $_REQUEST['idempresa'];
$nomeempresa = $_REQUEST['nomeempresa'];


// le data vigencia 
$rstEMP = mysql_query("select date_format(vigencia, '%Y%m%d') as vigencia from pj where numero=$idempresa", $conexao) or die (mysql_error());
$regEMP = mysql_fetcH_object($rstEMP);

if (trim($regEMP->vigencia)=='')   die("<font color=red>ERRO</font><br><br>DESCRICAO ERRO= Data vigência em branco - será ".
      "impossível gerar mensasalidade futura<br><br><br>$botaoSAIR");

$dataVIGENCIA =  $regEMP->vigencia;
mysql_free_result($rstEMP);



// apaga usuarios atuais
mysql_query("delete from pj_usuarios where idempresa=$idempresa") or  
  die("<font color=red>ERRO</font><br><br>DESCRICAO ERRO= ".mysql_error().'<br>'.
      "delete from pj_usuarios where idempresa=$idempresa"."<br><br>$botaoSAIR");

// apaga futuras atuais
mysql_query("delete from pj_futuras where idempresa=$idempresa") or  
  die("<font color=red>ERRO</font><br><br>DESCRICAO ERRO= ".mysql_error().'<br>'.
      "delete from pj_usuarios where idempresa=$idempresa"."<br><br>$botaoSAIR");

  

$usuarios=0;
for ($i = 2; $i <= $data->sheets[1]['numRows']; $i++) {

  $valores='';

  if ( trim($data->sheets[1]['cells'][$i][3]) == '')   continue;

  $usuarios++;

	for ($j = 2; $j <= $data->sheets[1]['numCols']; $j++) {

    if ( (($j>1) && ($j<10) ) || (($j>17) && ($j<29)) ) { 
      $valores .= ($valores=='' ? '' : ', ');
      
      $vlr=$data->sheets[1]['cells'][$i][$j];
      //== foi um xunxo na hora ler a celula da planilha
      // o prog reader.xls que lê...convertia pra "numero de euler" E+bla bla bla
      // concatenande "==" ele converte para string mesmo -mas aqui precisamos retirar o == 
      $vlr= str_replace('== ', '', $vlr);  

      // campos alfanumericos
      if ($j==3 || $j==4 || $j==5 || $j==7 || $j==8 || $j==21 || $j==24 || $j==25 || $j==26 || $j==27 || $j==28)
        $valores .= "'$vlr'" ;

      // campos data
      else if ($j==6  || $j==23) {
        $dataGRAVAR='null';
        if (trim($vlr)!='') { 
          $dataGRAVAR = substr($vlr, 6, 4).'-'.substr($vlr, 3, 2).'-'.substr($vlr, 0, 2);
          $valores .= "'".date("Y-m-d", strtotime($dataGRAVAR))."'" ;
        }
        else 
          $valores .= 'null' ;
      }
      else {
        if (trim($vlr)=='') $vlr='null'; 
        $valores .= $vlr;
      }
    }
  }
  $sql = 'insert into pj_usuarios(tipodependente, titular, nome, nomeabreviado, datanasc, sexo, cpf, parentesco, grauinstrucao, '. 
         "estadocivil, nacionalidade, cnpj, plano, datainclusao, nomemae, pis, rg, expedidor, ufexpedidor, idempresa) values($valores, $idempresa)";
  mysql_query($sql, $conexao) or 
      die("<font color=red>ERRO</font><br><br>DESCRICAO ERRO= ".mysql_error().'<br>'.       
          $sql."<br><br>$botaoSAIR");
}


// gera futuras


$rstPLANOS = mysql_query('select pl.plano, pl.numero as idPLANO, fx.faixainicial, fx.faixafinal, vl.preco ' . 
                          'from pj_planos pl ' .
                          'left join pj_faixas_etarias fx ' .
                          "	on fx.idEMPRESA=$idempresa " .
                          'left join pj_precos vl '.
                          '	on vl.idEMPRESA=fx.idEMPRESA and '. 
                          '		 vl.idPLANO=pl.numREG and vl.idFAIXAETARIA=fx.numREG ', $conexao) or die (mysql_error());
       
$rstUSUARIOS = mysql_query("select date_format(datanasc, '%Y%m%d') as datanasc, plano ".
                           " from pj_usuarios where idempresa=$idempresa", $conexao) or die (mysql_error());



$futuras=array(0,0,0,0,0,0,0,0,0,0);
while ($regUSUARIO = mysql_fetcH_object($rstUSUARIOS)) {

  $dataMENS = $dataVIGENCIA;
  // calcula o mesmo usuários em cada mes das 10 mensalidades

  $txt='';
  for ($i=0; $i<count($futuras); $i++) {
    $dataMENS = date("Ymd", strtotime(date("Y-m-d", strtotime($dataMENS)) ." +1 month"  )       );

    $idade = calculate_age($regUSUARIO->datanasc, $dataMENS, 0);

    mysql_data_seek($rstPLANOS, 0);
    while ($regPLANO=mysql_fetcH_object($rstPLANOS)) {
 
      if ( strpos($idade, 'M')>-1 ) 
        $idadeCALC='0';
      else {
        $idadeCALC=str_replace(' A', '', $idade); $idadeCALC=str_replace(' M', '', $idadeCALC);
      }  

//$txt .= "$regUSUARIO->plano   $regPLANO->idPLANO $idadeCALC    $regPLANO->faixainicial $regPLANO->faixafinal , ";   
    // soma enésima mensalidade do usuario a parcela da emrpesa
      if ( trim($regUSUARIO->plano)==trim($regPLANO->idPLANO) && $idadeCALC>=$regPLANO->faixainicial && $idadeCALC<=$regPLANO->faixafinal) {
//die('entrou');
        $somar=$futuras[$i]; $somar += $regPLANO->preco;
        $futuras[$i] = $somar;
        break; 
      } 
    }
  }
}


// insere as 10 mensalidades
$dataMENS = $dataVIGENCIA;
for ($i=0; $i<count($futuras); $i++) {
  $dataMENS = date("Y-m-d", strtotime(date("Y-m-d", strtotime($dataMENS)) ." +1 month"  )       );

  $j=$i+1;
  $somar=$futuras[$i];
  $sql = "insert into pj_futuras(idempresa, vencimento, valor, ordem) values($idempresa, '$dataMENS', $somar, $j)"; 

  mysql_query($sql, $conexao) or 
      die("<font color=red>ERRO</font><br><br>DESCRICAO ERRO= ".mysql_error()."<br>$sql<br><br>$botaoSAIR");
}




 


/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);

die("<font face='verdana' color=red size='+2'>$usuarios Registros de usuários lidos e <br>gravados para empresa $nomeempresa</font><br><br>".
"<input type='button' value=' FECHAR ' onclick='fecha();'/>");


/*****************************************************************************************/
/* calcula idade em anos, meses e dias */
/*****************************************************************************************/

function date_delta($ts_start_date, $ts_end_date) {
  $secs_in_day = 86400;

  $i_years = gmdate('Y', $ts_end_date) - gmdate('Y', $ts_start_date);
  $i_months = gmdate('m', $ts_end_date) - gmdate('m', $ts_start_date);
  $i_days = gmdate('d', $ts_end_date) - gmdate('d', $ts_start_date);
//  if ($i_days < 0)
//    $i_months--;
  if ($i_months < 0) {
    $i_years--;
    $i_months += 12;
  }
  if ($i_days < 0) {
    $i_days = gmdate('d', gmmktime(0, 0, 0,
      gmdate('m', $ts_start_date)+1,
      0,
      gmdate('Y', $ts_start_date))) -
      gmdate('d', $ts_start_date);
    $i_days += gmdate('d', $ts_end_date);
  }

  # calculate HMS delta
  $f_delta = $ts_end_date - $ts_start_date;
  $f_secs = $f_delta % $secs_in_day;
  $f_secs -= ($i_secs = $f_secs % 60);
  $i_mins = intval($f_secs/60)%60;
  $f_secs -= $i_mins * 60;
  $i_hours = intval($f_secs/3600);

  return array($i_years, $i_months, $i_days,
               $i_hours, $i_mins, $i_secs);
}

function calculate_age($s_start_date,
    $s_end_date = '',
    $b_show_all = 0) {
  $b_show_time = strlen($s_start_date > 8);
  $ts_start_date =
    mktime(substr($s_start_date, 8, 2),
      substr($s_start_date, 10, 2),
      substr($s_start_date, 12, 2),
      substr($s_start_date, 4, 2),
      substr($s_start_date, 6, 2),
      substr($s_start_date, 0, 4));
  if ($s_end_date) {
    $ts_end_date =
      mktime(substr($s_end_date, 8, 2),
        substr($s_end_date, 10, 2),
        substr($s_end_date, 12, 2),
        substr($s_end_date, 4, 2),
        substr($s_end_date, 6, 2),
        substr($s_end_date, 0, 4));
  } else {
    $ts_end_date = time();
  }

  list ($i_age_years, $i_age_months, $i_age_days,
        $i_age_hours, $i_age_mins, $i_age_secs) =
       date_delta($ts_start_date, $ts_end_date);

  # output
  $s_age = '';
  
  
  if ($i_age_years)
    //$s_age .= "$i_age_years ano".
      //(abs($i_age_years)>1?'s':'');
    $s_age = "$i_age_years A";
    
  else if ( $i_age_months ) {
  $s_age .= ($s_age?',':'').
      "$i_age_months  ".
      (abs($i_age_months)>1?'':'');

      $s_age = "$i_age_months M ";
  }



//    $s_age .= ($s_age?',':'').
//      "$i_age_months mes".
//      (abs($i_age_months)>1?'es':'');

/*  
  if ($b_show_all && $i_age_days )
    $s_age .= ($s_age?',':'').
      "$i_age_days dia".
      (abs($i_age_days)>1?'s':'');

/* nao mostra horas, min, segs
  
  if ($b_show_time && $i_age_hours)
    $s_age .= ($s_age?', ':'').
      "$i_age_hours hora".
      (abs($i_age_hours)>1?'s':'');
      
  if ($b_show_time && $i_age_mins)
    $s_age .= ($s_age?', ':'').
      "$i_age_mins minuto".
      (abs($i_age_mins)>1?'s':'');
  if ($b_show_time && $i_age_secs)
    $s_age .= ($s_age?', ':'').
      "$i_age_secs segundo".
      (abs($i_age_secs)>1?'s':'');
      
*/

      
  return $s_age;
}

  



?>


