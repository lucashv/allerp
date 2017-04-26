<?
header("Content-Type: text/html; charset=iso-8859-1");


  $servidor = 'mysql.netnigro.com.br';
  $loginMYSQL = 'netnigbr27';
  $baseMYSQL = 'netnigbr27';
  $senha = "03netn13";




$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());

  
mysql_query("DROP TABLE IF EXISTS seguros_tiposcliente;") or  die (mysql_error());
$sql="CREATE TABLE  seguros_tiposcliente ( ".
  "numreg int(10) unsigned NOT NULL AUTO_INCREMENT, ".
  "nome varchar(30) DEFAULT NULL, ".
  "ativo char(1) DEFAULT NULL, ".
  "PRIMARY KEY (`numreg`) ".
") ENGINE=InnoDB DEFAULT CHARSET=latin1;"; 

mysql_query($sql) or  die (mysql_error());

mysql_query("insert into seguros_tiposcliente(nome, ativo) select 'Jurídica', 'S'") or  die (mysql_error());
mysql_query("insert into seguros_tiposcliente(nome, ativo) select 'Física', 'S'") or  die (mysql_error());


mysql_query("DROP TABLE IF EXISTS seguros_apolices;") or  die (mysql_error());
$sql="CREATE TABLE  seguros_apolices ( ".
  "numreg int(10) unsigned NOT NULL AUTO_INCREMENT, ".
  "cliente varchar(70) DEFAULT NULL, ".
  "tipoEXCEL varchar(60) DEFAULT NULL, ".
  "idTIPO int(10) unsigned DEFAULT NULL, ".
  "seguradoraEXCEL varchar(60) DEFAULT NULL, ".
  "idSEGURADORA int(10) unsigned DEFAULT NULL, ".
  "dataASSINATURA date DEFAULT NULL, ".
  "apolice varchar(30) DEFAULT NULL, ".
  "fones varchar(70) DEFAULT NULL, ".
  "email varchar(100) DEFAULT NULL, ".
  "dataNASCIMENTO date DEFAULT NULL, ".
  "idCORRETOR int(10) unsigned DEFAULT NULL, ".
  "vlrPREMIO decimal(10,2) DEFAULT NULL, ".
  "percentual int(10) unsigned DEFAULT NULL, ".
  "parcelas int(10) unsigned DEFAULT NULL, ".
  "excluido int(10) unsigned DEFAULT NULL, ".
  "obs text, ".
  "tipoCLIENTE int(10) unsigned DEFAULT NULL, ".
  "PRIMARY KEY (`numreg`) ".
") ENGINE=InnoDB DEFAULT CHARSET=latin1;"; 

mysql_query($sql) or  die (mysql_error());

mysql_query("DROP TABLE IF EXISTS seguros_tipos;") or  die (mysql_error());
$sql="CREATE TABLE  seguros_tipos ( ".
  "numreg int(10) unsigned NOT NULL AUTO_INCREMENT, ".
  "nome varchar(70) DEFAULT NULL, ".
  "ativo char(1) DEFAULT NULL, ".
  "PRIMARY KEY (`numreg`) ".
") ENGINE=InnoDB DEFAULT CHARSET=latin1;";
mysql_query($sql) or  die (mysql_error());

mysql_query("DROP TABLE IF EXISTS seguros_seguradoras;") or  die (mysql_error());
$sql="CREATE TABLE  seguros_seguradoras ( ".
  "numreg int(10) unsigned NOT NULL AUTO_INCREMENT, ".
  "nome varchar(70) DEFAULT NULL, ".
  "ativo char(1) DEFAULT NULL, ".
  "PRIMARY KEY (`numreg`) ".
") ENGINE=InnoDB DEFAULT CHARSET=latin1;"; 
mysql_query($sql) or  die (mysql_error());

mysql_query("DROP TABLE IF EXISTS seguros_corretores;") or  die (mysql_error());
$sql="CREATE TABLE  seguros_corretores ( ".
  "numreg int(10) unsigned NOT NULL AUTO_INCREMENT, ".
  "nome varchar(70) DEFAULT NULL, ".
  "ativo char(1) DEFAULT NULL, ".
  "PRIMARY KEY (`numreg`) ".
") ENGINE=InnoDB DEFAULT CHARSET=latin1;"; 
mysql_query($sql) or  die (mysql_error()); 
 


for ($ee=0; $ee<2; $ee++)  {
  if ($ee==0)   $arq = fopen('rel.segurados.pessoa.juridica (2).csv', 'r'); 
  if ($ee==1) $arq = fopen('rel.segurados.pessoa.fisica.csv', 'r');

  $cont=0;
  $sql='insert into seguros_apolices(cliente, tipoexcel, seguradoraexcel, dataASSINATURA, apolice, fones, email, datanascimento, tipoCLIENTE) values ';

  while(true)       {
  	$lin = fgets($arq);
  
  	if ($lin == null)  break;
  	if (trim($lin) == '')  continue;
  	if (substr($lin, 0, 10) == ';;;;;;;;;;')  continue;
  	if ($cont<1)  {$cont++;   continue;}
  
    if (strlen($sql)>10000) {
      mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());
  
      $sql='insert into seguros_apolices(cliente, tipoexcel, seguradoraexcel, dataASSINATURA, apolice, fones, email, datanascimento, tipoCLIENTE) values ';
    }
    $info = explode(';', $lin);

    $dataASS='null';  $dataNAS='null';
    $dataASSINATURA=''; $dataNASC='';

    $cliente=''; $tipo=''; $seguradora=''; $apolice='' ;    $fones='';   $email='';     
    $cliente = $info[0];
    $info2 = explode('/', $info[1]);
    if (isset($info2[0])) $tipo = $info2[0];
    if (isset($info2[1]))     $seguradora = $info2[1];

    $apolice = $info[3];
    $dataASSINATURA = $info[2];
    $fones = $info[4];
    $email = $info[5];
    $dataNASC  = $info[6];

    if (strlen($dataNASC)==8 &&  
       is_numeric(substr($dataNASC, 3, 2)) && is_numeric(substr($dataNASC, 0, 2)) && is_numeric('20'.substr($dataNASC, 6, 2)) && 
      checkdate(substr($dataNASC, 3, 2), substr($dataNASC, 0, 2), '20'.substr($dataNASC, 6, 2))    )     
   
      $dataNAS="'20".substr($dataNASC, 6, 2).'-'.substr($dataNASC, 3, 2).'-'.substr($dataNASC, 0, 2)."'";


    if ($ee==0) {
      if (strlen($dataASSINATURA)==8 &&  
         is_numeric(substr($dataASSINATURA, 3, 2)) && is_numeric(substr($dataASSINATURA, 0, 2)) && is_numeric('20'.substr($dataASSINATURA, 6, 2)) &&  
        checkdate(substr($dataASSINATURA, 3, 2), substr($dataASSINATURA, 0, 2), '20'.substr($dataASSINATURA, 6, 2))    )   
        $dataASS="'20".substr($dataASSINATURA, 6, 2).'-'.substr($dataASSINATURA, 3, 2).'-'.substr($dataASSINATURA, 0, 2)."'";
    }
    else {
      if (strlen($dataASSINATURA)==10 &&  
         is_numeric(substr($dataASSINATURA, 3, 2)) && is_numeric(substr($dataASSINATURA, 0, 2)) && is_numeric(substr($dataASSINATURA, 6, 4)) &&  
        checkdate(substr($dataASSINATURA, 3, 2), substr($dataASSINATURA, 0, 2), substr($dataASSINATURA, 6, 4))    )   
        $dataASS="'".substr($dataASSINATURA, 6, 4).'-'.substr($dataASSINATURA, 3, 2).'-'.substr($dataASSINATURA, 0, 2)."'";
    }
  
    $sql .= 
      $sql=='insert into seguros_apolices(cliente, tipoexcel, seguradoraexcel, dataASSINATURA, apolice, fones, email, datanascimento, tipoCLIENTE) values ' ? 
            '' : ',';       
    if ($ee==0)  $sql .= "('$cliente', '$tipo', '$seguradora', $dataASS, '$apolice', '$fones', '$email', $dataNAS, 1)   ";
    else $sql .= "('$cliente', '$tipo', '$seguradora', $dataASS, '$apolice', '$fones', '$email', $dataNAS, 2)   ";


  }
                              
  
fclose($arq);               
                           
  if ($sql!='insert into seguros_apolices(cliente, tipoexcel, seguradoraexcel, dataASSINATURA, apolice, fones, email, datanascimento, tipoCLIENTE) values ') 
    mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());
}
$sql='insert into seguros_tipos(nome) '.
     "select distinct(tipoexcel)  from seguros_apolices where ifnull(tipoexcel,'')<>'' ";
mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

$sql=" update seguros_tipos set ativo='S' ";
mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

$sql='insert into seguros_seguradoras(nome) '.
     "select distinct(seguradoraexcel)  from seguros_apolices where ifnull(seguradoraexcel,'')<>'' ";
mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

$sql=" update seguros_seguradoras set ativo='S' ";
mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

$sql=" update seguros_apolices, seguros_tipos set idTIPO=seguros_tipos.numreg where ".
          " seguros_apolices.tipoexcel=seguros_tipos.nome; ";
mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

$sql=" update seguros_apolices, seguros_seguradoras set idSEGURADORA=seguros_seguradoras.numreg where ".
          " seguros_apolices.seguradoraexcel=seguros_seguradoras.nome; ";
mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

$sql=" alter table seguros_apolices drop tipoEXCEL ";
mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

$sql=" alter table seguros_apolices drop seguradoraEXCEL ";
mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());


//$sql="update tipos_contrato set ativo='N' where ativo is null ";;
//mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

                            
?>                          