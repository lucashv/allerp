<?php 
ob_start();
require("doctype.php"); 
session_start();

?>

<head>
<link href="<?php echo $_SESSION['arqCSS']; ?>" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/funcoes.js" xml:space="preserve"></script>
<script type="text/javascript" src="js/edicaoDados.js" xml:space="preserve"></script>
</head>

<body style="HEIGHT: 100%; width:100%;" onload="Avisa('');Muda_CSS();lerINFO();ColocaFocoCmpInicial();">

<style>
.cssDIV_AJAX {
position: absolute; top: 200px; top:50%; left: 50%; 
width: 50px; height: 50px;	margin-top: -40px; margin-left: -10px; display:block; 
z-index:20; 
}
</style>

<div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" bgcolor=red>
  <table height="50px" bgcolor="#7CA7FF" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="images/database.png" alt="" /></td></tr>
  </table>
</div>


<form id="frmSENHA" name="frmSENHA" action="">

<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">

    <table cellspacing="0" cellpadding="0" border="1"  bgcolor="white"   class="frmJANELA"    
    style="text-align:left;height:20%;width:60%"    >

      <tr height="5%"><td >

        <table width="100%"><tr >      
          <td><span class="lblTitJanela">&nbsp;Informações da empresa</span></td>
          <td align="right" id="btnGRAVAR" align="center" onmouseout="this.style.backgroundColor='#fffff0'" 
          onmouseover="this.style.backgroundColor='#efefef'" onclick="return VerCmp('todos');" >
            <span style="cursor: pointer;" class="lblTitJanela" >[ F2= gravar ]</span>            
          </td>      
         <td align="right" style="cursor: pointer;text-align:right;"  
           onclick="window.top.frames['framePRINCIPAL'].location.href='inicial.php';" 
           class="lblTitJanela" >[ X ]</span></td>
        </tr></table>

      </td></tr>					
    
      <tr valign="top"  ><td style="height:95%;width:100%" >

        <table><tr >
          <td>&nbsp;</td>
    		</tr></table>

        <table>
          <tr>
            <td width="50px">&nbsp;Nome: </td>
            <td><input type="text" id="txtNOME" tabindex="1" value="" maxlength="45" size="70"></td>
          </tr>

    		</tr></table>
    		
      </td></tr>
    
    </table>   
    
    
</td></tr></table>




<script language="javascript" type="text/javascript" xml:space="preserve">
//<![CDATA[
/* qtde de campos text na tela de edição, necessario informar  */
var nQtdeCamposTextForm = 1;

var aCMPS=new Array(1);
aCMPS[0]='txtNOME;Digite o nome da empresa';

var ajax = new execAjax();

/*******************************************************************************/
function teclado(e)         {

if (window.event) tecla=window.event.keyCode;
else tecla=e.which;

var lF2=tecla==113;  

if  (lF2) document.getElementById('btnGRAVAR').click();
if (tecla==27) {e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='inicial.php';}
  
eval("teclasNavegacao(e);");  
}

if (window.document.addEventListener) 
 window.document.addEventListener("keydown", teclado, false);
else 
 window.document.attachEvent("onkeydown", teclado);



/*******************************************************************************/
function ColocaFocoCmpInicial(cmp)   {
document.getElementById('txtNOME').focus();
}	


/*******************************************************************************/
function VerCmp(nomeCMP)      {

if (nomeCMP!='todos')    document.getElementById(nomeCMP).style.backgroundColor="white";


/* concatena */
//cmps= document.getElementById('txtNOME').value+'|'+
//document.getElementById('SELECAO').value;

if (nomeCMP=='todos')         {
	
  /* tudo ok, aciona gravacao do registro, concatena campos em uma variavel */
	showAJAX(1);
	
  strGRAVAR = document.getElementById( 'txtNOME').value;

	ajax.criar('ajax/ajax.php?acao=gravarInfoEmpresa&vlr=' + strGRAVAR, gravou);
}

}

/*******************************************************************************/
function gravou() {

showAJAX(0);

if ( ajax.terminouLER() ) {
  resp = ajax.ler();
  
  if (resp.indexOf('OK')!=-1) {   
    alert('Informação gravada.');
    window.top.frames['framePRINCIPAL'].location.href='inicial.php';
  }      
 	else alert('Erro ao gravar: \n\n ' + resp);  	
}
}

/*******************************************************************************/
function showAJAX(acao) {
dv= document.getElementById('divAJAX');
if (acao==1) {
	dv.setAttribute("className", "cssDIV_AJAX");
	dv.setAttribute("class", "cssDIV_AJAX");	
	
}
else   {
	dv.setAttribute("className", "cssDIV_ESCONDE");
	dv.setAttribute("class", "cssDIV_ESCONDE");	
}  	
}

/*******************************************************************************/
function lerINFO()  {

ajax.criar('ajax/ajax.php?acao=lerInfoEmpresa', '' , 0);

document.getElementById( 'txtNOME' ).value = ajax.ler().split(';')[0];  

}


//]]>
</script>
</body>
</html>
