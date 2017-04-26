<?php 
ob_start();
require("doctype.php"); 
session_start();
?>

<head>
<link href="<?php echo $_SESSION['arqCSS']; ?>" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/funcoes.js" xml:space="preserve"></script><script
type="text/javascript" src="js/menuContexto.js" xml:space="preserve"></script><script
type="text/javascript" src="js/edicaoDados.js" xml:space="preserve"></script>
<style type="text/css" xml:space="preserve">
.cssDIV_EDICAO {
position: absolute; top: 200px;  width: 500px; height: 80px;	
margin-top: -180px; margin-left: -250px; display:block; z-index:3;}

.cssDIV_AJAX {
position: absolute; top: 200px; top:50%; left: 50%; 
width: 50px; height: 50px;	margin-top: -40px; margin-left: -10px; display:block; 
z-index:20; 
}

.cssDIV_PGTOS {
position: absolute; top: 200px;  width: 920px; height: 480px;	
margin-top: -270px; margin-left: -455px; display:block; z-index:30; 
background:#E6E8EE;border:1px solid grey;  
}
</style>
</head>

<body style="HEIGHT: 100%; width:100%;" onload="lerREGS();Avisa('');Muda_CSS();ColocaFocoCmpInicial();">

<script type="text/javascript" xml:space="preserve">
//<![CDATA[
/* prepara menu de contexto (botao direito do mouse) */
SimpleContextMenu.setup({'preventDefault':true, 'preventForms':false});
SimpleContextMenu.attach('container', 'CM1');
//]]>
</script>
<ul id="CM1" class="SimpleContextMenu">
  <li><a href="javascript:incluirREG();">Incluir registro</a></li>
  <li><a href="javascript:editarREG();">Editar registro</a></li>
  <li><a href="javascript:excluirREG();">Excluir registro</a></li>
</ul>

<div id="divAUXILIO" class="cssDIV_ESCONDE">&nbsp;</div>
<form id="frmPERIODOS_PGTO" name="frmPERIODOS_PGTO" autocomplete="off" action="" >

<div id="divEDICAO" class="cssDIV_ESCONDE"></div>

<div id="divPGTOS" class="cssDIV_ESCONDE" >
  <table width="100%" border="0" style="font-family: Verdana;font-size: 10px;color: black;"  cellspacing="0" cellpadding="3"> 

    <tr height="30px;">
      <td width="90%"><span class="lblTitJanela" id=infoPGTOS>&nbsp;&nbsp;&nbsp;Pagamentos</td>
      <td onclick="fecharPGTOS();" style="cursor:pointer;"><span class="lblTitJanela">[ X ]</span></td>    
    </tr>
  
    <tr><td colspan="2" valign="top" height="20px" id="titPGTOS"></td></tr>
    <tr><td colspan="2" valign="top" height="430px">
      <div id="div_tabPGTOS" style="overflow:auto;min-height:95%;height:95%">
    </td></tr>

  </table>  
</div>



<div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" bgcolor=red>
  <table height="50px" bgcolor="#7CA7FF" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="images/database.png" alt="" /></td></tr>
  </table>
</div>

<input id="SELECAO" type="hidden" value="" />
<input id="SELECAO_2" type="hidden" value="" >


<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">
  
    <table cellspacing="0" cellpadding="0" border="1" width="97%" bgcolor="white" style="text-align:left;">


      <tr><td><table width="100%" cellpadding="0" cellspacing="2"><tr>
        <td width="60%"><span class="lblTitJanela" id="lblTITULO">&nbsp;&nbsp;</span></td>
        
        <td style='display:none' title="Ver o relatório impresso" align="center" onmouseout="this.style.backgroundColor='white'" 
        onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="verREL();" >
          <img src="images/protocolo.png" />
        </td>

        <td title="Ver pagamentos" align="center" onmouseout="this.style.backgroundColor='white'" 
        onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="verPGTOS();" >
          <img src="images/pgtos.png" />
        </td>

        <td title="Resumo dos pagamentos" align="center" onmouseout="this.style.backgroundColor='white'" 
        onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="recibo();" >
          <img src="images/recibo.png" />
        </td>

        <td style="cursor: pointer;text-align:right;"  
          onclick="window.top.frames['framePRINCIPAL'].location.href='representantes.php';" 
          class="lblTitJanela" >[ X ]</span>
        </td>
      
      </tr></table></td></tr>

      <tr>
        <td valign="top" height="<?php echo ($_SESSION['altIFRAME'] * .75); ?> px" >
          <div id="titTABELA">&nbsp;</div>
          <div id="divTABELA" style="overflow:auto;min-height:95%;height:95%" class="container"></div>
        </td>
      </tr>
    </table>

  </td>

</tr>
</table>

</form>

<script language="javascript" type="text/javascript" xml:space="preserve">//<![CDATA[
/* qtde de campos text na tela de edição, necessario informar  */
var nQtdeCamposTextForm = 6;

var largPR = 0;

var aCMPS=new Array();
aCMPS[0]='txtDATAINI_CONF;Digite data inicial válida ou deixe em branco';
aCMPS[1]='txtDATAFIN_CONF;Digite a data final válida ou deixe em branco';
aCMPS[2]='txtDATAINI_VALES;Digite a data inicial para créditos/débitos válida ou deixe em branco';
aCMPS[3]='txtDATAFIN_VALES;Digite a data final para créditos/débitos válida ou deixe em branco';
aCMPS[3]='txtOPERADORA;Identifique a operadora';

var ajax = new execAjax();

/*******************************************************************************/
function teclado(e)         {

var lJanAuxilio= document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
if (window.event) tecla=window.event.keyCode;
else tecla=e.which;

var lJanRegistro=0; 

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
lJanPGTOS= document.getElementById('divPGTOS').getAttribute(propCLASSE)!='cssDIV_ESCONDE';

if  (tecla==27 && lJanAuxilio)   	{fecharAUXILIO();return;}
if  (tecla==13 && lJanAuxilio)   	usouAUXILIO();
if  (tecla==45 && ! lJanRegistro )   	incluirREG();


if  (tecla==27) {
  if (lJanRegistro)   	fecharEDICAO();
  else if (lJanPGTOS)   	fecharPGTOS();
  else {e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='representantes.php';}
}  
 
if (lJanRegistro)  eval("teclasNavegacao(e);");

var lF2=tecla==113;

if  ( lF2 && lJanRegistro ) 
  document.getElementById('btnGRAVAR').click();
}

if (window.document.addEventListener) 
 window.document.addEventListener("keydown", teclado, false);
else 
 window.document.attachEvent("onkeydown", teclado);


/*******************************************************************************/
function fecharEDICAO()     {
document.getElementById("divEDICAO").innerHTML='';
document.getElementById("divEDICAO").setAttribute(propCLASSE, "cssDIV_ESCONDE");
ColocaFocoCmpInicial();
}

/*******************************************************************************/
function fecharPGTOS()     {
var divPGTOS = document.getElementById('divPGTOS'); divPGTOS.setAttribute(propCLASSE, 'cssDIV_ESCONDE');    
var tabPGTOS = document.getElementById('div_tabPGTOS'); tabPGTOS.innerHTML = '';
ColocaFocoCmpInicial();
}


/*******************************************************************************/
function ColocaFocoCmpInicial(cmp)   {

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO';
var lJanAuxilio = document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';

if (cmp!=null) 
	document.getElementById(cmp).focus();
else if (lJanAuxilio) 
  document.getElementById('txtPR2').focus();	

else if (lJanRegistro ) 
	document.getElementById('txtDATAINI_CONF').focus();
}	

/*******************************************************************************/
function lerREGS() {

showAJAX(1);

document.getElementById("divTABELA").innerHTML = '';
document.getElementById("SELECAO").value='';

ajax.criar('ajax/ajaxPERIODOS_PGTO.php?acao=lerREGS' , desenhaTabela);
}


/*******************************************************************************/
function  desenhaTabela() {
if ( ajax.terminouLER() ) {

  aRESP = ajax.ler().split('|');
  
  document.getElementById("titTABELA").innerHTML = aRESP[0];
  document.getElementById("divTABELA").scrollTop=0;
  document.getElementById("divTABELA").innerHTML = aRESP[1].split('^')[0];
  
  var qtdeREGS = aRESP[1].split('^')[1]; 

  showAJAX(0);
  
  centerDiv( 'divEDICAO' ); centerDiv( 'divPGTOS' );
  VerificaAcaoInicial();
  
  var titulo=document.getElementById('lblTITULO');
  titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">Relatórios de pagamento para corretor</font>&nbsp;&nbsp;&nbsp;&nbsp;';
}
}



/*******************************************************************************/
function desenhaJanelaREG()     {
if ( ajax.terminouLER() ) {
  var divEDICAO = document.getElementById('divEDICAO');

  divEDICAO.setAttribute(propCLASSE, 'cssDIV_EDICAO');    divEDICAO.innerHTML = ajax.ler();
  
  /* muda estilo de campos text, span para o padrão do site */
  Muda_CSS(); 
  
  showAJAX(0);
  ColocaFocoCmpInicial();
}
}  

/*******************************************************************************/
function editarREG() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}
	
showAJAX(1);
ajax.criar('ajax/ajaxPERIODOS_PGTO.php?acao=editarREG&vlr=' + id, desenhaJanelaREG);
}

/*******************************************************************************/
function incluirREG() {
showAJAX(1);
ajax.criar('ajax/ajaxPERIODOS_PGTO.php?acao=incluirREG', desenhaJanelaREG);
}


/*******************************************************************************/
function excluirREG() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}
	
if (! confirm('Certeza excluir este período??\n\nO sistema não irá recriá-lo.\n\n')) return;

showAJAX(1);
ajax.criar('ajax/ajaxPERIODOS_PGTO.php?acao=excluirREG&vlr=' + id, '', 0);
showAJAX(0);

lerREGS();
}


/*******************************************************************************/
function VerCmp(nomeCMP)      {

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO';
if (! lJanRegistro)  return;

if (nomeCMP!='todos')    document.getElementById(nomeCMP).style.backgroundColor="#F6F7F7";

if (nomeCMP!='todos')    {
  document.getElementById(nomeCMP).style.backgroundColor="white";
  
	switch (nomeCMP) {
		case 'txtOPERADORA':
		  vlr = document.getElementById(nomeCMP).value;
		  cmpLBL = document.getElementById( nomeCMP.replace('txt', 'lbl') );
		  
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

      if ( cmpLBL.innerHTML.toUpperCase().indexOf('AMIL')!=-1 ) {
        document.getElementById('trDIF2').style.display='block';
        document.getElementById('trDIF3').style.display='block';
        document.getElementById('trDIF4').style.display='block';

        document.getElementById('txtDATAFIN_VALES').value='';
        document.getElementById('txtDATAINI_VALES').value='';

        alert('Para operadora AMIL, o sistema inclui períodos automaticamente');
     	  cmpLBL.style.color='red';
    	   cmpLBL.innerHTML='* AMIL - ERRO - INCLUSAO DE PERIODOS DESATIVADA *';

      }
      else {
        document.getElementById('trDIF2').style.display='none';
        document.getElementById('trDIF3').style.display='none';
        document.getElementById('trDIF4').style.display='none';

      }
			
			break;
	}
	return;
}  

else  {
	for (i=0;i<aCMPS.length;i++)   {
		cmp = aCMPS[i].split(';');
		cCMP = cmp[0]; 
		cMSG = cmp[1];
		cVLR = document.getElementById(cCMP).value.rtrim().ltrim();
    var label = cCMP.replace('txt', 'lbl'); 		
				
		erro=0;
		switch (cCMP)   {
  		case 'txtDATAINI_CONF':
  		case 'txtDATAFIN_CONF':
  		case 'txtDATAINI_VALES':
  		case 'txtDATAFIN_VALES':
  		case 'txtDATAPGTO':
  			if ( cVLR!='' && ! verifica_data(cCMP) )   erro=1;
  			break;

    	case 'txtOPERADORA':			
        if ( document.getElementById(label).innerHTML.indexOf('ERRO')!=-1 ) erro=1;
        break;
 		}

	 if (erro==1) {alert(cMSG);	document.getElementById(cCMP).focus(); return false;}	
	}	

  /* tudo ok, aciona gravacao do registro, concatena campos em uma variavel */
  var data = document.getElementById('txtDATAINI_CONF').value;
  var dataini_conf='null';
  if (data.rtrim().ltrim()!='') 
    dataini_conf = data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);

  var data = document.getElementById('txtDATAFIN_CONF').value;
  var datafin_conf='null';
  if (data.rtrim().ltrim()!='') 
    datafin_conf = data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);

  var data = document.getElementById('txtDATAINI_VALES').value;
  var dataini_vales='null';
  if (data.rtrim().ltrim()!='') 
    dataini_vales = data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);

  var data = document.getElementById('txtDATAFIN_VALES').value;
  var datafin_vales='null';
  if (data.rtrim().ltrim()!='') 
    datafin_vales = data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);

  var data = document.getElementById('txtDATAPGTO').value;
  var dataPGTO='null';
  if (data.rtrim().ltrim()!='') 
    dataPGTO = data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);


  cmps=document.getElementById('numREG').value+'|'+
       dataini_conf+'|'+datafin_conf+'|'+
       dataini_vales+'|'+datafin_vales+'|'+dataPGTO+'|'+
       document.getElementById('txtOPERADORA').value; 

	showAJAX(1);
	ajax.criar('ajax/ajaxPERIODOS_PGTO.php?acao=gravar&vlr=' + cmps, gravou);
}

}

/*******************************************************************************/
function gravou() {

if ( ajax.terminouLER() ) {
  showAJAX(0);
  
  resp = ajax.ler();
  
  fecharEDICAO();
  
  if (resp.indexOf('OK')!=-1)   {
    document.getElementById('SELECAO').value="";
  
  	cID = resp.substring(resp.indexOf(';')+1);
  	cID= cID.replace('INC_', '');
  	window.top.document.getElementById('infoTrab').value = 'frmPERIODOS_PGTO:GRAVOU=' + cID
  	lerREGS();
  }
  else
  	alert('Erro ao gravar: \n\n ' + resp);	
}
}

/*******************************************************************************/
function verPGTOS() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}
	
showAJAX(1);
ajax.criar('ajax/ajaxPERIODOS_PGTO.php?acao=verPGTOS&vlr=' + id, '', 0);
showAJAX(0);


if (ajax.ler()=='nada') {
  alert('Relatório não gerado ainda');
  return;
}

var relatorio='';
var tab=document.getElementById('tabRELATORIOS');
for (var t=0; t<tab.rows.length; t++) {

  if (tab.rows[t].id==document.getElementById('SELECAO').value) {
    relatorio =  'Nº: <font color=blue>'+tab.rows[t].cells[0].innerHTML+'</font>'+ 
                 '&nbsp;&nbsp;&nbsp;&nbsp;Operadora: <font color=blue>'+tab.rows[t].cells[1].innerHTML+'</font>'+
                 '&nbsp;&nbsp;&nbsp;&nbsp;Mês/ano: <font color=blue>'+tab.rows[t].cells[2].innerHTML+'</font><br>'+
                 '&nbsp;&nbsp;&nbsp;&nbsp;Confirmações: <font color=blue>'+tab.rows[t].cells[3].innerHTML+'</font>'+
                 '&nbsp;&nbsp;&nbsp;&nbsp;Cré/Déb: <font color=blue>'+tab.rows[t].cells[4].innerHTML+'</font>'+
                 '&nbsp;&nbsp;&nbsp;&nbsp;Dia Pgto: <font color=blue>'+tab.rows[t].cells[5].innerHTML+'</font>';
    break;
  }  
}
var divPGTOS = document.getElementById('divPGTOS'); divPGTOS.setAttribute(propCLASSE, 'cssDIV_PGTOS');    
var titPGTOS = document.getElementById('titPGTOS'); titPGTOS.innerHTML = ajax.ler().split('|')[0];
var tabPGTOS = document.getElementById('div_tabPGTOS'); tabPGTOS.innerHTML = ajax.ler().split('|')[1];
tabPGTOS.scrollTop=0;

document.getElementById('infoPGTOS').innerHTML = '&nbsp;Relatório de pagamento: '+relatorio;
}

/*******************************************************************************/
function pagarCORRETOR(id) {

var tab=document.getElementById('tabREGS');
for (var t=0; t<tab.rows.length; t++) {

  if (tab.rows[t].id==id) {
    if (tab.rows[t].cells[7].innerHTML=='-') {
      msg = 'Confirma depósito na conta do corretor '+tab.rows[t].cells[8].innerHTML+' ?'
      novoVLR = 'Sim';
      url = 'ajax/ajaxPERIODOS_PGTO.php?acao=confirmarDEPOSITO&vlr=' + id.replace('pgto_', '');
    }
    else {
      msg = 'Depósito do corretor '+tab.rows[t].cells[8].innerHTML+' será cancelado. Continua?';
      novoVLR = '-';
      url = 'ajax/ajaxPERIODOS_PGTO.php?acao=cancelarDEPOSITO&vlr=' + id.replace('pgto_', '');
    }
    break;
  }  
}
if (! confirm(msg)) return;

showAJAX(1);
ajax.criar(url, '', 0);
showAJAX(0);

if (ajax.ler().indexOf('ERRO')==-1) tab.rows[t].cells[7].innerHTML = novoVLR;
}

/*******************************************************************************/
function recibo() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}
	
var relatorio='';
var tab=document.getElementById('tabRELATORIOS');
for (var t=0; t<tab.rows.length; t++) {

  if (tab.rows[t].id==document.getElementById('SELECAO').value) {
    relatorio =  'Nº: '+tab.rows[t].cells[0].innerHTML+''+ 
                 '    Operadora: '+tab.rows[t].cells[1].innerHTML+''+
                 '    Mês/ano: '+tab.rows[t].cells[2].innerHTML;
    relatorio2 = 'Confirmações: '+tab.rows[t].cells[3].innerHTML+''+
                 '    Cré/Déb: '+tab.rows[t].cells[4].innerHTML+'    Dia Pgto: '+tab.rows[t].cells[5].innerHTML;
    relatorio = relatorio.replace(/&nbsp;/g, '');
    relatorio2 = relatorio2.replace(/&nbsp;/g, '');
    break;
  }  
}


showAJAX(1);
ajax.criar('rel/ajaxRELS2.php?acao=resumoPgtoPorBanco&vlr=' + id+
      '&rel='+relatorio+'&rel2='+relatorio2, '', 0);
showAJAX(0);

if (ajax.ler().indexOf('nada')!=-1) 
  alert('Nenhum registro encontrado');
else
  window.open('pdf/rel_PAISAGEM.php', 'nome', 'width=10,height=10' );	
}



//]]></script>
  </body>
</html>
