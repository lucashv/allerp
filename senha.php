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

<body style="HEIGHT: 100%; width:100%;" onload="Avisa('');Muda_CSS();ColocaFocoCmpInicial();">

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
    style="text-align:left;height:20%;width:40%"    >

      <tr height="5%"><td>

        <table><tr >      
          <td style="width:60%" style="cursor: move;"><span class="lblTitJanela">&nbsp;Senha do operador</td>
          <td id="btnGRAVAR" align="center" onmouseout="this.style.backgroundColor='#fffff0'" 
          onmouseover="this.style.backgroundColor='#efefef'" onclick="return VerCmp('todos');" >
            <span style="cursor: pointer;" class="lblTitJanela" >[ F2= gravar ]</span>
          </td>      
        </tr></table>

      </td></tr>					
    
      <tr valign="top"  ><td style="height:95%;width:100%" >

        <table><tr >
          <td>&nbsp;</td>
    		</tr></table>

        <table><tr >
          <td align="right" width="150px">&nbsp;Senha atual: </td><td><input type="password" id="txtSENHA" tabindex="1" value="" maxlength="6" 
            size="8"></td>
    		</tr></table>
    		
        <table><tr>
          <td align="right" width="150px">&nbsp;Nova Senha: </td><td><input type="password" id="txtSENHA2" tabindex="2" value="" maxlength="6" 
            size="8"></td>
    		</tr></table>
    		
        <table><tr>
          <td align="right" width="150px">&nbsp;Confirme Nova Senha: </td><td><input type="password" id="txtSENHA3" tabindex="3" value="" 
            maxlength="6" size="8"></td>
    		</tr></table>
    		
    		<table><tr >
          <td>&nbsp;</td>
    		</tr></table>

    		
          
      </td></tr>
    
    </table>   
    
    
</td></tr></table>




<script language="javascript" type="text/javascript" xml:space="preserve">
//<![CDATA[
/* qtde de campos text na tela de edição, necessario informar  */
var nQtdeCamposTextForm = 3;

var aCMPS=new Array(3);
aCMPS[0]='txtSENHA;Digite a senha atual';
aCMPS[1]='txtSENHA2;Digite a nova senha ';
aCMPS[2]='txtSENHA3;Digite a confirma\u00e7\u00e3o da senha';

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

document.getElementById('txtSENHA').focus();
}	


/*******************************************************************************/
function VerCmp(nomeCMP)      {

if (nomeCMP!='todos')    document.getElementById(nomeCMP).style.backgroundColor="white";


/* concatena */
//cmps= document.getElementById('txtNOME').value+'|'+
//document.getElementById('SELECAO').value;

if (nomeCMP=='todos')         {
	
  for (h=0;h<aCMPS.length;h++)   {
  	cmp = aCMPS[h].split(';');
		cCMP = cmp[0]; 
		cMSG = cmp[1];
		
		
		cVLR = document.getElementById(cCMP).value.rtrim().ltrim();
				
		erro=0;
		switch (cCMP)   {
			case 'txtSENHA':
			case 'txtSENHA2':
			case 'txtSENHA3':
        if ( cVLR=='' ) erro=1;
        break;
        
 		}

	 if (erro==1) {alert(cMSG);	document.getElementById(cCMP).focus(); return false;}	
	}
  
  if ( document.getElementById('txtSENHA2').value.rtrim() != 
      document.getElementById('txtSENHA3').value.rtrim()) {
    alert('A confirma\u00e7\u00e3o da senha está diferente');
    return;
  }            
  	

  /* tudo ok, aciona gravacao do registro, concatena campos em uma variavel */
	showAJAX(1);
	
  strGRAVAR='';
  strGRAVAR += document.getElementById('txtSENHA').value + '|';
  strGRAVAR += document.getElementById('txtSENHA2').value + '|';
  strGRAVAR += document.getElementById('txtSENHA3').value + '|';    
  
	ajax.criar('ajax/ajax.php?acao=gravarSENHA&vlr=' + strGRAVAR, gravou);
}

}

/*******************************************************************************/
function gravou() {

showAJAX(0);

if ( ajax.terminouLER() ) {
  resp = ajax.ler();
  
  if (resp.indexOf('OK')!=-1) {   
    alert('Senha alterada com sucesso.');
    window.top.frames['framePRINCIPAL'].location.href='inicial.php';
  }      
  else if (resp.indexOf('incorreta')!=-1)
  	alert('Senha atual incorreta');
  else    	
  	alert('Erro ao gravar: \n\n ' + resp);  	
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


//]]>
</script>
</body>
</html>
