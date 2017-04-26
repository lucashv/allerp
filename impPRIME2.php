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



$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());

  
mysql_query("DROP TABLE IF EXISTS seguros_sinistros;") or  die (mysql_error());
$sql="CREATE TABLE  seguros_sinistros ( ".
  "numreg int(10) unsigned NOT NULL AUTO_INCREMENT, ".
  "idAPOLICE int(10) unsigned DEFAULT NULL, ".
  "cliente varchar(70) DEFAULT NULL, ".
  "dataSINISTRO date DEFAULT NULL, ".
  "dataLIBERACAO date DEFAULT NULL, ".
  "apolice varchar(30) DEFAULT NULL, ".
  "sinistro varchar(30) DEFAULT NULL, ".
  "fones varchar(70) DEFAULT NULL, ".
  "email varchar(100) DEFAULT NULL, ".
  "tipoEXCEL varchar(60) DEFAULT NULL, ".
  "idTIPO int(10) unsigned DEFAULT NULL, ".
  "terceiros varchar(60) DEFAULT NULL, ".
  "obs text default null, ".
  "PRIMARY KEY (`numreg`) ".
") ENGINE=InnoDB DEFAULT CHARSET=latin1;"; 

mysql_query($sql) or  die (mysql_error());

mysql_query("DROP TABLE IF EXISTS seguros_tipos_sinistros;") or  die (mysql_error());
$sql="CREATE TABLE  seguros_tipos_sinistros ( ".
  "numreg int(10) unsigned NOT NULL AUTO_INCREMENT, ".
  "nome varchar(70) DEFAULT NULL, ".
  "ativo char(1) DEFAULT NULL, ".
  "PRIMARY KEY (`numreg`) ".
") ENGINE=InnoDB DEFAULT CHARSET=latin1;";
mysql_query($sql) or  die (mysql_error());

mysql_query("DROP TABLE IF EXISTS seguros_renovacoes;") or  die (mysql_error());
$sql="CREATE TABLE  seguros_renovacoes ( ".
  "numreg int(10) unsigned NOT NULL AUTO_INCREMENT, ".
  "idAPOLICE int(10) unsigned DEFAULT NULL, ".
  "idCORRETOR int(10) unsigned DEFAULT NULL, ".
  "protecao int(10) DEFAULT NULL, ".
  "parcelas int(10) DEFAULT NULL, ".
  "vlrPREMIO decimal(10,2) DEFAULT NULL, ".
  "comissao decimal(10,2) DEFAULT NULL, ".
  "PRIMARY KEY (`numreg`) ".
") ENGINE=InnoDB DEFAULT CHARSET=latin1;"; 

mysql_query($sql) or  die (mysql_error());


$arq = fopen('PLANILHA SINISTROS.csv', 'r'); 

$cont=0;
$sql='insert into seguros_sinistros(cliente, dataSINISTRO, dataLIBERACAO, sinistro, apolice, fones, email, tipoEXCEL, terceiros, obs) values ';

while(true)       {
	$lin = fgets($arq);
  
	if ($lin == null)  break;
	if (trim($lin) == '')  continue;
	if (substr($lin, 0, 10) == ';;;;;;;;;;')  continue;
	if ($cont<4)  {$cont++;   continue;}
  
  if (strlen($sql)>10000) {
    mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

    $sql='insert into seguros_sinistros(cliente, dataSINISTRO, dataLIBERACAO, sinistro, apolice, fones, email, tipoEXCEL, terceiros, obs) values ';
  }
  $info = explode(';', $lin);

  $dataSIN='null';  $dataLIB='null';
  $dataSINISTRO=''; $dataLIBERACAO='';

  $cliente=''; $sinistro=''; $apolice='' ;    $fones='';   $email=''; $tipo='';     
  if ( isset($info[9]) ) {
    if (isset($info2[1])) $tipo = $info2[1];
    else  $tipo = $info[9];
  } 

  $apolice = $info[6];
  $cliente = $info[2];
  $apolice=str_replace('.', '', $apolice);
  $dataSINISTRO = $info[3];
  if ( isset($info[8]) )   $fones = $info[8];
  if ( isset($info[10]) )   $email = $info[10];
  $dataLIBERACAO = $info[4];
  if ( isset($info[12]) )   $obs = $info[12];
  if ( isset($info[11]) )  $terceiros = $info[11];

  if (strlen($dataSINISTRO)==8 &&  
     is_numeric(substr($dataSINISTRO, 3, 2)) && is_numeric(substr($dataSINISTRO, 0, 2)) && is_numeric('20'.substr($dataSINISTRO, 6, 2)) )   
    $dataSIN="'20".substr($dataSINISTRO, 6, 2).'-'.substr($dataSINISTRO, 3, 2).'-'.substr($dataSINISTRO, 0, 2)."'";
  if (strlen($dataLIBERACAO)==8 &&  
     is_numeric(substr($dataLIBERACAO, 3, 2)) && is_numeric(substr($dataLIBERACAO, 0, 2)) && is_numeric('20'.substr($dataLIBERACAO, 6, 2)) )   
    $dataLIB="'20".substr($dataLIBERACAO, 6, 2).'-'.substr($dataLIBERACAO, 3, 2).'-'.substr($dataLIBERACAO, 0, 2)."'";
  
  $sql .= 
    $sql=='insert into seguros_sinistros(cliente, dataSINISTRO, dataLIBERACAO, sinistro, apolice, fones, email, tipoEXCEL, terceiros, obs) values ' ? 
          '' : ',';       
  $sql .= "('$cliente', $dataSIN, $dataLIB, '$sinistro', '$apolice', '$fones', '$email', '$tipo', '$terceiros', '$obs')   ";
}
                              
fclose($arq);               
                           
if ($sql!='insert into seguros_sinistros(cliente, dataSINISTRO, dataLIBERACAO, sinistro, apolice, fones, email, tipoEXCEL, terceiros, obs) values ') 
  mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

$sql='insert into seguros_tipos_sinistros(nome) '.
     "select distinct(tipoexcel)  from seguros_sinistros where ifnull(tipoexcel,'')<>'' ";
mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

$sql=" update seguros_tipos_sinistros set ativo='S' ";
mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

$sql='insert into seguros_tipos_sinistros(nome) '.
     "select distinct(tipoexcel)  from seguros_sinistros where ifnull(tipoexcel,'')<>'' ";
mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());




//$sql="update tipos_contrato set ativo='N' where ativo is null ";;
//mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

                            
?>                          