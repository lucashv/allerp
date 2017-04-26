<?
header("Content-Type: text/html; charset=iso-8859-1");

//  $servidor = 'localhost';
//  $loginMYSQL = 'root';
//  $baseMYSQL = 'allcross';
//  $senha = "sucesso";


  $servidor = 'mysql.netnigro.com.br';
  $loginMYSQL = 'netnigbr27';
  $baseMYSQL = 'netnigbr27';
  $senha = "03netn13";


require_once( 'includes/senha.php'  );


$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());
  
mysql_query("DROP TABLE importa_premier;") ;
mysql_query("DROP TABLE IF EXISTS importa_premier;") or  die (mysql_error());
mysql_query("DROP TABLE IF EXISTS importa_premier;") or  die (mysql_error());

//exit;
$sql="CREATE TABLE  importa_premier ( ".
  "numreg int(10) unsigned NOT NULL AUTO_INCREMENT, ".
  "dataCADASTRO date NOT NULL, ".
  "dataASSINATURA date NOT NULL, ".
  "vlrCONTRATO decimal(10,2) DEFAULT NULL, ".
  "contratante varchar(100) DEFAULT NULL, ".
  "proposta varchar(20) DEFAULT NULL, ".
  "corretorEXCEL varchar(100) DEFAULT NULL, ".
  "idCORRETOR int(10) unsigned DEFAULT NULL, ".
  "contratoEXCEL varchar(100) DEFAULT NULL, ".
  "idTipoContrato int(10) unsigned DEFAULT NULL, ".
  "PRIMARY KEY (`numreg`) ".
") ENGINE=InnoDB DEFAULT CHARSET=latin1;"; 

mysql_query($sql) or  die (mysql_error());

for ($ee=0; $ee==0; $ee++)  {
  if ($ee==0)   $arq = fopen('Santos e Barros.csv', 'r');

  $cont=0;
  $sql='insert into importa_premier(dataCADASTRO, dataASSINATURA, proposta, corretorEXCEL, contratoEXCEL,  '.
       ' contratante, vlrCONTRATO) values ';
  
  while(true)       {
  	$lin = fgets($arq);
  
  	if ($lin == null)  break;
  	if (trim($lin) == '')  continue;
  	if (substr($lin, 0, 7) == ';;;;;;;')  continue;
  	if ($cont<1)  {$cont++;continue;}
  
  if (strlen($sql)>10000) {
      echo($sql); 
      mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());
  
      $sql='insert into importa_premier(dataCADASTRO, dataASSINATURA, proposta, corretorEXCEL, contratoEXCEL,  '.
           ' contratante, vlrCONTRATO) values ';
    }
    $info = explode(';', $lin);

    $dataCAD='null'; 
    $dataCADASTRO='';  

    if ($ee==0)   {
      $colCADASTRO=2;

      $colVALOR=6;
      $colCORRETOR=5;
      $colPROPOSTA=0;
      $colPRODUTO=1;
      $colCONTRATANTE=3;
    }
  
    $dataCADASTRO = $info[$colCADASTRO];

    if (  (strlen($dataCADASTRO)==8) &&    
      ( is_numeric(substr($dataCADASTRO, 3, 2)) && is_numeric(substr($dataCADASTRO, 0, 2)) && is_numeric('20'.substr($dataCADASTRO, 6, 2)) )  )   
      $dataCAD='20'.substr($dataCADASTRO, 6, 2).'-'.substr($dataCADASTRO, 3, 2).'-'.substr($dataCADASTRO, 0, 2);

    $corretor=''; $contratante=''; $produto=''; $proposta=''; 
   
    $corretor=str_replace("'", '', $info[$colCORRETOR]);  $corretor=str_replace(chr(92), '', $corretor); $corretor=substr($corretor,0,59);
    $proposta=str_replace("'", '', $info[$colPROPOSTA]);  $proposta=str_replace(chr(92), '', $proposta); $proposta=substr($proposta,0,59);
    $produto=str_replace("'", '', $info[$colPRODUTO]);  $produto=str_replace(chr(92), '', $produto); $produto=substr($produto,0,59);
    $contratante=str_replace("'", '', $info[$colCONTRATANTE]);  $contratante=str_replace(chr(92), '', $contratante); $contratante=substr($contratante,0,59);

    $vlr=str_replace(",", '.', $info[$colVALOR]);
    $vlr=trim($vlr)=='' ? '0' : $vlr;
    $vlr=trim($vlr)=='R$ -' ? '0' : $vlr;

    $corretor = str_pad(trim($corretor), 30, ' ', 1);
  
    $corretor = str_replace('Lidiana          ',  'Lidiane'                                        ,$corretor);
                              
    $cont++;
  
    $numLINHA = $cont-1;
    $sql .= 
      $sql=='insert into importa_premier(dataCADASTRO, dataASSINATURA, proposta, corretorEXCEL, contratoEXCEL,  '.
           ' contratante, vlrCONTRATO) values ' ? '' : ',';       
      $sql .= "('$dataCAD', '$dataCAD', '$proposta', '$corretor', '$produto', '$contratante', $vlr)";
    }                         
  }                           
  if ($sql!='insert into importa_premier(dataCADASTRO, dataASSINATURA, proposta, corretorEXCEL, contratoEXCEL,  '.
           ' contratante, vlrCONTRATO) values ') {
       echo($sql);
    mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());
}                              
                              
/*
  $sql="update origens_atendimento set nome=replace(nome,'www.','');"; 
  mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());
                              
  $sql='insert into representantes(nome) '.
       'select distinct(corretorexcel)  from importa_indicacoes '.
       'left join representantes repre '.
       "on repre.nome=corretorexcel where repre.numero is null and ifnull(corretorexcel,'')<>''; "; 
  mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());
  
*/
  

//mysql_query("DROP TABLE IF EXISTS importa_indicacoes;") or  die (mysql_error());
                              
  fclose($arq);               
                            
                            
?>                          