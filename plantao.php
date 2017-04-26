<?php 
ob_start();
require("doctype.php"); 
session_start();
?>

<head>
<link href="<?php echo $_SESSION['arqCSS']; ?>" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/funcoes.js" xml:space="preserve"></script>
<script type="text/javascript" src="js/edicaoDados.js" xml:space="preserve"></script>

<title>
</title>
</head>

<body style="HEIGHT: 100%; width:100%;" 
  onload="Avisa('');lerPLANTAO();Muda_CSS();">

<style>
.cssDIV_AJAX {
position: absolute; top: 200px; top:50%; left: 50%; 
width: 50px; height: 50px;	margin-top: -90px; margin-left: -10px; display:block; 
z-index:20; 
}

</style>

<div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" >
  <table height="50px" bgcolor="#a9b2ca" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="images/database.png" alt="" /></td></tr>
  </table>
</div>

<form id="frmPLANTAO" name="frmPLANTAO" action="">

<div id="divAUXILIO" class="cssDIV_ESCONDE">&nbsp;</div>
<input id="SELECAO_2" type="hidden" value="" >
<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">

    <table cellspacing="0" cellpadding="0" border="1"  bgcolor="white"   class="frmJANELA"    
    style="text-align:left;height:20%;width:90%"    >

      <tr height="5%"><td>

        <table WIDTH="100%"><tr  >      
          <td id=titPLANTAO style="width:30%" ><span class="lblTitJanela">&nbsp;</td>

          <td style="width:20%;cursor: pointer;" id="btnLIMPAR" align="center" onmouseout="this.style.backgroundColor='#F6F7F7';" 
          onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="limpar();" >
            <span  class="lblTitJanela" >[ F4= NOVO PLANTÃO ]</span>
          </td>

          <td >&nbsp;&nbsp;&nbsp;</td>
          <td style="width:30%;cursor: pointer;" id="btnDESBLOQUEAR" align="center" onmouseout="this.style.backgroundColor='#F6F7F7';" 
          onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="desbloquear();" >
            <span class="lblTitJanela" >[ F9= DESBLOQUEAR CORRETORES ]</span>
          </td>
          

          <td style="cursor: pointer;text-align:right;"  
            onclick="window.top.frames['framePRINCIPAL'].location.href='ligacoes.php';" 
            class="lblTitJanela" >[ X ]</span></td>      
        </tr></table>

      </td></tr>					
    
  		<tr><td><table width="100%">  					
        <tr>
          <td valign="top" height="320px" >
            <div>
              <table border="0" style="font-family: Verdana;font-size: 10px;color: black;"  cellspacing="0" cellpadding="3" width="97%" >
              <thead class="headerFIXO">	
              <tr>
              <td align="center" width="20%">Ordem</td>
              <td align="center" width="65%">Corretor</td>
              <td align="center" width="10%">Ramal</td>
              <td align="center" width="5%">&nbsp;</td>
              </tr>
              </thead></table>
            </div>
            <div style="overflow:auto;min-height:95%;height:95%;overflow:-moz-scrollbars-vertical;color:blue;" id=divREGS>
              <table id="tabREGs" width="99%" cellpadding="3"  cellspacing="0" style="font-family:verdana;font-size:10px;color:black;">
              </table>                  
            </div>
          </td>
        </tr>
  		</table></td></tr>
        
  		<tr><td><table>  					
  			<tr>
          <td>&nbsp;Corretor:</td>

    			<td onmouseover="this.style.cursor='pointer';" title="Escolher corretor (ou pressione F8)" 
              onclick="AuxilioF7('txtREPRESENTANTE')";><image src="images/setaBAIXO.gif"></td>
          <td><input type="text" id="txtREPRESENTANTE" tabindex="1"  value="" maxlength="4" size="4"
                onKeyPress="return sistema_formatar(event, this, '0000');"></td>
          <td style="padding-left:5px;WIDTH:190px" align="left"><span class="lblPADRAO_VLR_CMP" id="lblREPRESENTANTE"></span></td>

          <td>&nbsp;Ramal:</td>
          <td><input type="text" id="txtRAMAL" tabindex="2"  value="" maxlength="5" size="6"
                onKeyPress="return sistema_formatar(event, this, '00000');"></td>

<td>
        <input id="txtFOCADO" type="text" value="" 
          style="color: white;background-color: white; border: 0px solid white;font-size:0px;" /></td>

  			</tr>

    </table></td></tr>   
    
    
</td></tr></table>


<script language="javascript" type="text/javascript" xml:space="preserve">
//<![CDATA[
/* qtde de campos text na tela de edição, necessario informar  */
var largPR = 0;
var nQtdeCamposTextForm = 3;

var aCMPS=new Array();
aCMPS[0]='txtREPRESENTANTE;Identifique o corretor';

var ajax = new execAjax();

/*******************************************************************************/
function teclado(e)         {

if (window.event) tecla=window.event.keyCode;
else tecla=e.which;

var lJanAuxilio= document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
if  (tecla==115) document.getElementById('btnLIMPAR').click();
if  (tecla==120) document.getElementById('btnDESBLOQUEAR').click();
if  (tecla==27) {
  if (lJanAuxilio)   	fecharAUXILIO();
  else {
    e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='ligacoes.php';
  }
}
if  (tecla==13 && lJanAuxilio)   	usouAUXILIO();
if  ( tecla==119 && ! lJanAuxilio )  AuxilioF7(cfoco);

if  ( (tecla==13 || tecla==40) && cfoco=='txtRAMAL' ) {
  showAJAX(1);
  ajax.criar('ajax/ajaxLIGACOES.php?acao=verPLANTAO&vlr='+document.getElementById('txtREPRESENTANTE').value+
                      '&ramal='+document.getElementById('txtRAMAL').value, '', 0);
  showAJAX(0);
  
  if (document.getElementById('lblREPRESENTANTE').style.color=='red' || document.getElementById('lblREPRESENTANTE').innerHTML=='') {
    alert('Defina o corretor');
    return;
  }
  if (ajax.ler()=='jaCORRETOR') {
    alert('Corretor já listado');
    return;
  }
  if (ajax.ler()=='jaRAMAL') {
    alert('Ramal já utilizado');
    return;
  }

  showAJAX(1);
  ajax.criar('ajax/ajaxLIGACOES.php?acao=adicionaCORRETOR&vlr='+document.getElementById('txtREPRESENTANTE').value+
                          '&ramal='+document.getElementById('txtRAMAL').value, '', 0);
  showAJAX(0);
  
  document.getElementById('txtREPRESENTANTE').value=''; document.getElementById('lblREPRESENTANTE').innerHTML='';
  document.getElementById('txtRAMAL').value=''
  lerPLANTAO();return;
}

eval("teclasNavegacao(e);");

}  

if (window.document.addEventListener) 
 window.document.addEventListener("keydown", teclado, false);
else 
 window.document.attachEvent("onkeydown", teclado);




/*******************************************************************************/
function VerCmp(nomeCMP)      {

if (nomeCMP!='todos')    document.getElementById(nomeCMP).style.backgroundColor="white";

/* concatena */

if (nomeCMP=='todos')         {
}

else {
	switch (nomeCMP) {
		case 'txtREPRESENTANTE':
		  vlr = document.getElementById(nomeCMP).value;
		  cmpLBL = document.getElementById( nomeCMP.replace('txt', 'lbl') );
		  
		  /* deixou vlr em branco */
      if (vlr.rtrim().ltrim()=='') {cmpLBL.innerHTML = '';return true;}
      
			cmpLBL.innerHTML = 'lendo...';
			showAJAX(1);
			ajax.criar('ajax/ajaxAUXILIO.php?oQueAuxiliar=pesquisa_' + nomeCMP + '&vlr=' +vlr, '', 0);
			showAJAX(0);
			
      aRESP = ajax.ler().split(';');

      cIDCMP = aRESP[0]; 	cVLR = aRESP[1];
      cIDCMP=cIDCMP.rtrim().ltrim();

    	if (cVLR.indexOf('ERRO')!=-1) cmpLBL.style.color='red';
    	else cmpLBL.style.color='blue';
    	  
    	cmpLBL.innerHTML = cVLR;

			break;

		case 'txtRAMAL':
      break;

	}
	return;
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
function ColocaFocoCmpInicial(cmp)   {
var lJanAuxilio = document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';

if (cmp!=null) 
	document.getElementById(cmp).focus();
else if (lJanAuxilio) 
  document.getElementById('txtPR2').focus();	
else 
	document.getElementById('txtREPRESENTANTE').focus();
}

/*******************************************************************************/
function removeCORRETOR(numREG)    {

numREG=numREG.replace('r_', '');

showAJAX(1);
ajax.criar('ajax/ajaxLIGACOES.php?acao=removeCORRETOR&vlr='+numREG, '', 0);
showAJAX(0);

lerPLANTAO();
}
/********************************************************************************/
function lerPLANTAO()  {

document.getElementById('divREGS').innerHTML=
    '<table id="tabREGs" width="99%" cellpadding="3"  cellspacing="0" style="font-family:verdana;font-size:10px;color:black;"></table>';

showAJAX(1);	
ajax.criar('ajax/ajaxLIGACOES.php?acao=lerPLANTAO', '', 0);
showAJAX(0);

lendoATUAL=ajax.ler().split('^')[1];

if (ajax.ler().indexOf('nada')==-1) {
  prop=ajax.ler().split('^')[0].split('|');


  var tab = document.getElementById('tabREGs');
	for (i=0;i<prop.length;i++)   {
		info = prop[i].split(';');
		
    var lin = tab.insertRow(-1);
    lin.id = info[2];

    var tt='r_'+info[2];

    var col = lin.insertCell(-1); col.innerHTML = info[0];  col.width = '20%'; col.align='center';
    var col = lin.insertCell(-1); col.innerHTML = info[1];  col.width = '65%'; col.align='left';
    var col = lin.insertCell(-1); col.innerHTML = info[3];  col.width = '10%'; col.align='left';
           
    col = lin.insertCell(-1); col.innerHTML = '<font color="red" style="font-size:14px;font-weight:bold;">X</font>'; col.align='center';
    col.width = '5%'; col.id=tt; col.onclick = function() { removeCORRETOR(this.id);}

    var corFORM = parent.window.document.getElementById('corFormJanela').value;
    lin.onmouseout = function() {this.style.backgroundColor = corFORM;}
    lin.onmouseover = function() {
      this.style.backgroundColor='#A9B2CA'; this.style.cursor='pointer';}
    lin.width='100%';
  }
  document.getElementById('divREGS').scrollTop = 1000;
  document.getElementById('txtREPRESENTANTE').value=''; document.getElementById('lblREPRESENTANTE').innerHTML='';
  document.getElementById('txtRAMAL').value='';
}
 
document.getElementById('titPLANTAO').innerHTML='&nbsp;<span style="font-size:14px;"><b>Definindo plantão '+
            lendoATUAL+'</b></span>';

setTimeout('document.getElementById("txtREPRESENTANTE").focus()', 200);

}

/********************************************************************************/
function limpar()  {

if (!confirm('Começar novo plantão?')) return;
showAJAX(1);	
ajax.criar('ajax/ajaxLIGACOES.php?acao=novoPLANTAO', '', 0);
showAJAX(0);

lerPLANTAO();
document.getElementById('txtREPRESENTANTE').value=''; document.getElementById('lblREPRESENTANTE').innerHTML='';
document.getElementById('txtREPRESENTANTE').focus();
}

/********************************************************************************/
function desbloquear()  {

if (!confirm('Desbloquear todos os corretores?')) return;
showAJAX(1);	
ajax.criar('ajax/ajaxLIGACOES.php?acao=desbloquearTODOS', '', 0);
showAJAX(0);

lerPLANTAO();
document.getElementById('txtREPRESENTANTE').value=''; document.getElementById('lblREPRESENTANTE').innerHTML='';
document.getElementById('txtREPRESENTANTE').focus();
}



//]]>
</script>
</body>
</html>
