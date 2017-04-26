<?
header("Content-Type: text/html; charset=iso-8859-1");




  $servidor = 'localhost';
  $loginMYSQL = 'root';
  $baseMYSQL = 'ac';
  $senha = "sucesso";
  
  $servidor = 'mysql.premiercorretora.kinghost.net';
  $loginMYSQL = 'premiercorreto';
  $baseMYSQL = 'premiercorreto';
  $senha = "f2a9b0";
  

die("CONEXÃO NEGADA A BASE DE SÃO PAULO");
echo('sd');

$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());



$arq = fopen('planilha thais all cross.csv', 'r');
  
mysql_query("DROP TABLE IF EXISTS importa_premier;") or  die (mysql_error());
$sql="CREATE TABLE  importa_premier ( ".
  "data datetime NOT NULL, ".
  "proposta varchar(30) DEFAULT NULL, ".
  "corretorEXCEL varchar(60) DEFAULT NULL, ".  
  "idCORRETOR int(10) unsigned DEFAULT NULL, ".
  "contratoEXCEL varchar(60) DEFAULT NULL, ".
  "idCONTRATO int(10) unsigned DEFAULT NULL, ".
  "cliente varchar(80) DEFAULT NULL, ".
  "fone varchar(30) DEFAULT NULL, ".    
  "valor varchar(20) DEFAULT NULL) ".
" ENGINE=InnoDB DEFAULT CHARSET=latin1;"; 

mysql_query($sql) or  die (mysql_error());

$cont=0;
$sql='insert into importa_premier(data,proposta,corretorEXCEL,contratoEXCEL,cliente,valor,fone) values ';

while(true)       {
	$lin = fgets($arq);

	if ($lin == null)  break;
	if (trim($lin) == '')  continue;
	if ($cont<1)  {$cont++;continue;}

  $cont++;

  if (strlen($sql)>20000) {
    echo($sql); 
    mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

    $sql='insert into importa_premier(data,proposta,corretorEXCEL,contratoEXCEL,cliente,valor,fone) values ';
  }
  $info = explode(';', $lin);
  $data=$info[2];
  if (strlen($data)!=8) continue;

  if (! is_numeric(substr($data, 3, 2)) || ! is_numeric(substr($data, 0, 2)) || !is_numeric('20'.substr($data, 6, 2)) ) continue;  

  if (checkdate( substr($data, 3, 2), substr($data, 0, 2), '20'.substr($data, 6, 2) )    )  {  
    $data='20'.substr($data, 6, 2).'-'.substr($data, 3, 2).'-'.substr($data, 0, 2);

    $corretor=''; $proposta=''; $valor=''; $cliente=''; $fone='';
 
    $corretor=$info[5];  
    $proposta=$info[0];
    $contrato=$info[1];
    $cliente=$info[3];
    $fone=$info[4];
    $valor=$info[6];    
                    
    $corretor = str_replace('MARCOS','Marcos Felipe',$corretor);
    $corretor = str_replace('SARA','Sara Aparecida',$corretor);
    $corretor = str_replace('FAIMI','FAIMI CAMARGO GOVATISKI',$corretor);

    $sql .= $sql=='insert into importa_premier(data,proposta,corretorEXCEL,contratoEXCEL,cliente,valor,fone) values ' ? 
            '' : ',';
    $sql .= "('$data', '$proposta', '$corretor', '$contrato', '$cliente', '$valor', '$fone')";
  }
}
if ($sql!='insert into importa_premier(data,proposta,corretorEXCEL,contratoEXCEL,cliente,valor) values ') 
  mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());


$sql='update importa_premier, representantes set idCORRETOR=numero where '.
      ' nome=corretorEXCEL;';
mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

$sql='update importa_premier, tipos_contrato set idCONTRATO=numero where '.
      ' nome=corretorEXCEL;';
mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());


//$sql="update origens_atendimento set ativo='S'";
//mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());



fclose($arq);
    
    
  
?>