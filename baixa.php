<?php 
ob_start();
require("doctype.php"); ?>

<head>
<script language="JavaScript" src="js/funcoes.js" type="text/javascript" xml:space="preserve"></script>
<script language="javascript">
function fecha() {
parent.document.getElementById('fraUPLOAD').src="";
parent.document.getElementById('divUPLOAD').setAttribute('class', "cssDIV_ESCONDE");
}

/*******************************************************************************/
function teclado(e)         {

if (window.event) tecla=window.event.keyCode;
else tecla=e.which;


if  (tecla==27)	fecha();
}

if (window.document.addEventListener) 
 window.document.addEventListener("keydown", teclado, false);
else 
 window.document.attachEvent("onkeydown", teclado);


</script>
</head>

<body LEFTMARGIN="0" TOPMARGIN="0" style="HEIGHT: 100%; width:100%;font-family:verdana;font-size:14px;" >

<form action="ajax/processarBAIXA.php" method="post" enctype="multipart/form-data">

<table width="100%" cellpadding="0" cellspacing="0">

<tr><td><table width="97%"><tr>
  <td >Nova baixa de mensalidades</td>
  <td  style="cursor: pointer;"  align="right" onclick="fecha()">[ X ]&nbsp;&nbsp;</td>
</tr></table></td></tr>

<tr><td><table width="97%" cellpadding="0" cellspacing="0"><tr>
  <td><hr></td>
</tr></table></td></tr>

<tr><td>&nbsp;</td></tr>



<tr><td><table><tr>
  <td>Arquivo da Clinipam:</td>
</tr></table></td></tr>

<tr><td><table><tr>
  <td><input type="file" name="file" id="file" size="70" /></td>
</tr></table></td></tr>


<tr><td><table><tr>
  <td>&nbsp;</td>
</tr></table></td></tr>

<tr><td><table><tr>
  <td><input onclick="parent.showAJAX(1)" type="submit" name="submit" value="Continuar >>" /></td>
</tr></table></td></tr>

</table>
  
</form>


</body>

</html>
