<?
header("Content-Type: text/html; charset=iso-8859-1");

require_once( '../includes/definicoes.php'  );
require_once( '../includes/funcoes.php'  );


$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());


$arq = fopen('bancos.csv', 'r');
  
while(true)       {
	$lin = fgets($arq);
	if ($lin == null)  break;

  $cmps = explode(';', $lin);
  $sql="insert into bancos(numero,nome) select $cmps[0], '$cmps[1]'; ";

  mysql_query($sql, $conexao) or die (mysql_error());
}
  
mysql_close($conexao);

?>



