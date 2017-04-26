<?php 
ob_start();
require("doctype.php"); ?>

<head>
<script language="JavaScript" src="js/funcoes.js" type="text/javascript" xml:space="preserve"></script>
</head>

<body LEFTMARGIN="0" TOPMARGIN="0" style="HEIGHT: 100%; width:100%;" onload="verMSG();">


<table><tr><td id="msg">
</td></tr></table>


<script language="javascript" type="text/javascript" xml:space="preserve">

var ajax = new execAjax();
  
//<![CDATA[

function verMSG() {
ajax.criar('ajax/ajax.php?acao=verMsgOperador', '', 0);

if (ajax.ler().indexOf('none')!=-1) 
  var html='';
else
 var html = 
   '<font color=red face=verdana size="+1">Mensagem para você:<br>'+
   '</font><br><br><br><font color=blue face=verdana>' +
   ajax.ler() +
   '</font><br><br><br>'+
   '<input type=button class=btnSUBMIT value=" Clique aqui para não aparecer mais a mensagem " onclick="limpaMSG();"/>';
 
document.getElementById('msg').innerHTML = html;
}

function limpaMSG() {
ajax.criar('ajax/ajax.php?acao=excluirMsgOperador', '', 0);

verMSG();
} 
 

//]]>
</script>



<form id="frmINICIAL" >



</form>


</body>

</html>
