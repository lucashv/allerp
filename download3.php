<?php 
ob_start();
require("doctype.php"); ?>

<head>
<script language="JavaScript" src="js/funcoes.js" type="text/javascript" xml:space="preserve"></script>
</head>

<body LEFTMARGIN="0" TOPMARGIN="0" style="HEIGHT: 100%; width:100%;" >

<script language="javascript" type="text/javascript" xml:space="preserve">
  
//<![CDATA[
function teclado(e)         {
if (window.event) tecla=window.event.keyCode;
else tecla=e.which;

}

if (window.document.addEventListener) 
 window.document.addEventListener("keydown", teclado, false);
else 
 window.document.attachEvent("onkeydown", teclado);

//]]>
</script>



<form id="frmDOWNLOAD" >

<br><br><br><br>
<span style="font-size:24px;color:blue;font-family:verdana;">
Software para comissões, créditos e débitos<br><br>
Clique (COM O BOTÃO DIREITO DO MOUSE)<a target="_blank" href="comissoes.exe"> aqui</a> e escolha "Salvar link como..."
<br><br><br><br>
<span style="font-size:24px;color:grey;font-family:verdana;">
Salve-o em qualquer pasta, execute-o...<br><br>
<br><br>
</span>  



</form>


</body>

</html>
