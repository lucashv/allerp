<?php 
ob_start();
require("../doctype.php"); 
session_start();
?>

<head>
<link href="../<?php echo $_SESSION['arqCSS']; ?>" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../js/funcoes.js" xml:space="preserve"></script>
<script type="text/javascript" src="../js/edicaoDados.js" xml:space="preserve"></script>

<!-- Folha de estilos do calendário -->
<link rel="stylesheet" type="text/css" media="all" href="../js/jscalendar-1.0/calendar-win2k-1.css" title="win2k-cold-1" />

<!-- biblioteca principal do calendario -->
<script type="text/javascript" src="../js/jscalendar-1.0/calendar.js"></script>

<!-- biblioteca para carregar a linguagem desejada -->
<script type="text/javascript" src="../js/jscalendar-1.0/lang/calendar-en.js"></script>

<!-- biblioteca que declara a função Calendar.setup, que ajuda a gerar um calendário em poucas linhas de código -->
<script type="text/javascript" src="../js/jscalendar-1.0/calendar-setup.js"></script> 

<title>
AMEG
</title>
</head>

<body style="HEIGHT: 100%; width:100%;" 
  onload="Avisa('');lerDATAS();Muda_CSS();ColocaFocoCmpInicial();">

<style>
.cssDIV_AJAX {
position: absolute; top: 200px; top:50%; left: 50%; 
width: 50px; height: 50px;	margin-top: -90px; margin-left: -10px; display:block; 
z-index:20; 
}

</style>

<div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" >
  <table height="50px" bgcolor="#a9b2ca" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="../images/database.png" alt="" /></td></tr>
  </table>
</div>


<div id="divAUXILIO" class="cssDIV_ESCONDE">&nbsp;</div>
<form id="frmREL" name="frmREL" action="">
<input id="SELECAO_2" type="hidden" value="" >

<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">

    <table cellspacing="0" cellpadding="0" border="1"  bgcolor="white"   class="frmJANELA"    
    style="text-align:left;height:20%;width:95%"    >

      <tr height="5%"><td>

        <table WIDTH="100%"><tr  >      
          <td style="width:60%" style="cursor: move;"><span class="lblTitJanela">&nbsp;Vendas</td>
          <td style="width:30%" id="btnGERAR" align="center" onmouseout="this.style.backgroundColor='#F6F7F7;'" 
          onmouseover="this.style.backgroundColor='#efefef'" onclick="return VerCmp('todos');" >
            <span style="cursor: pointer;" class="lblTitJanela" >[ F2= gerar ]</span>
          </td>
          <td style="cursor: pointer;text-align:right;"  
            onclick="window.top.frames['framePRINCIPAL'].location.href='../relatorios.php';" 
            class="lblTitJanela" >[ X ]</span></td>      
        </tr></table>

      </td></tr>					
    
      <tr valign="top"  ><td style="height:95%;width:100%" ><table width="100%">

    		<tr><td>&nbsp;</td></tr>  					

        <tr><td ><table><tr >
          <td width="110px" align=right>Grupo de vendas:</td>
    			<td onmouseover="this.style.cursor='pointer';" title="Escolher grupo (ou pressione F8)" 
              onclick="AuxilioF7('txtREL_GRUPO')";><image src="../images/setaBAIXO.gif"></td>
          <td><input type="text" id="txtREL_GRUPO" tabindex="1"  value="" maxlength="4" size="6"
                onKeyPress="return sistema_formatar(event, this, '0000');"></td>
          <td style="padding-left:5px;width:400px" align="left"><span class="lblPADRAO_VLR_CMP" id="lblREL_GRUPO"></span></td>
    		</tr></table></td></tr>



    		<tr><td><table>
  				<td width="110px" align="right">Corretor:</td>					
			    <td onmouseover="this.style.cursor='pointer';" title="Escolher representante (ou pressione F8)"  
          onclick="AuxilioF7('txtREL_REPRESENTANTE');"><image src="../images/setaBAIXO.gif"></td>
  				<td><input type="text" id="txtREL_REPRESENTANTE" tabindex="2"   
            maxlength="4" size="6" onKeyPress="return sistema_formatar(event, this, '0000');"></td>
			    <td><span class="lblPADRAO_VLR_CMP" id="lblREL_REPRESENTANTE"></td>
    		</table></td></tr>

    		<tr><td><table>
  				<td width="110px" align="right">(9999= todos)</td>					
    		</table></td></tr>

    		<tr><td>&nbsp;</td></tr>
    		<tr><td><table>
  				<td width="110px" align="right">Operadora:</td>					
    			<td onmouseover="this.style.cursor='pointer';" title="Escolher operadora (ou pressione F8)" 
              onclick="AuxilioF7('txtOPERADORA')";><image src="../images/setaBAIXO.gif"></td>
          <td><input type="text" id="txtOPERADORA" tabindex="3"  value="" maxlength="4" size="6"
                onKeyPress="return sistema_formatar(event, this, '0000');"></td>
          <td style="padding-left:5px;WIDTH:120px" align="left"><span class="lblPADRAO_VLR_CMP" id="lblOPERADORA"></span></td>
    		</table></td></tr>

    		<tr><td>&nbsp;</td></tr>
    		<tr><td><table>
  				<td width="110px" align="right">Tipo de contrato:</td>
    			<td onmouseover="this.style.cursor='pointer';" title="Escolher tipo de contrato (ou pressione F8)" 
              onclick="AuxilioF7('txtTIPO_CONTRATO')";><image src="../images/setaBAIXO.gif"></td>
          <td><input type="text" id="txtTIPO_CONTRATO" tabindex="4"  value="" maxlength="4" size="6"
                onKeyPress="return sistema_formatar(event, this, '0000');"></td>
          <td style="padding-left:5px;WIDTH:120px" align="left"><span class="lblPADRAO_VLR_CMP" id="lblTIPO_CONTRATO"></span></td>
    		</table></td></tr>

    		<tr><td>&nbsp;</td></tr>

    		<tr><td><table>  					
    			<tr>
    				<td width="110px" align="right">Data Inicial:&nbsp;</td>					
    				<td><input type=text id="txtDATAINI" tabindex="5"  maxlength="8" size="10"
              onKeyPress="return sistema_formatar(event, this, '00/00/00');"  ></td>
          	<td><input type="button" id="btnDATAINI" title="Escolher data" class="btnSUBMIT" value="..."></td>              
    		</tr>
    		</table></td></tr>
    		
    		<tr><td><table>  					
    			<tr>
    				<td width="110px" align="right">Data final:&nbsp;</td>					
    				<td><input type=text id="txtDATAFIN" tabindex="6"  maxlength="8" size="10"
              onKeyPress="return sistema_formatar(event, this, '00/00/00');"  ></td>
          	<td><input type="button" id="btnDATAFIN" title="Escolher data" class="btnSUBMIT" value="..."></td>              
    		</tr>
    		</table></td></tr>
    		
    		<tr><td>&nbsp;</td></tr>
    		<tr><td><table>
    		  <td width="110px" align="right">Buscar por:&nbsp;</td>

          <td onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
            onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="seleciona2(1);">
            <input type="radio" id="tipobusca" name="tipobusca" value="1" checked >Data de cadastro&nbsp;&nbsp;
          </td>
          <td onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
            onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="seleciona2(2);">
            <input type="radio" id="tipobusca" name="tipobusca" value="2" >Data de vigência&nbsp;&nbsp;
          </td>
          <td onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
            onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="seleciona2(3);">
            <input type="radio" id="tipobusca" name="tipobusca" value="3" >Data de envio&nbsp;&nbsp;
          </td>
    		</table></td></tr>

    		<tr><td>&nbsp;</td></tr>
    		<tr><td><table>
    		  <td width="110px" align="right">Tipo de relatório:&nbsp;</td>

          <td onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
            onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="seleciona3(1);">
            <input type="radio" id="tipoREL" name="tipoREL" value="1" checked >Listar/somar por corretor&nbsp;&nbsp;
          </td>
          <td onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
            onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="seleciona3(2);">
            <input type="radio" id="tipoREL" name="tipoREL" value="2" >Cancelados&nbsp;&nbsp;
          </td>
          <td onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
            onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="seleciona3(3);">
            <input type="radio" id="tipoREL" name="tipoREL" value="3" >Classificação&nbsp;&nbsp;
          </td>
          <td onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
            onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="seleciona3(4);">
            <input type="radio" id="tipoREL" name="tipoREL" value="4" >Somatória por produto&nbsp;&nbsp;
          </td>
          <td onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
            onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="seleciona3(5);">
            <input type="radio" id="tipoREL" name="tipoREL" value="5" >Somar por corretor&nbsp;&nbsp;
          </td>

    		</table></td></tr>

    		<tr><td>&nbsp;</td></tr>
        
      </td></tr>
    
    </table>   
    
    
</td></tr></table>


<script language="javascript" type="text/javascript" xml:space="preserve">
//<![CDATA[
/* qtde de campos text na tela de edição, necessario informar  */

var nQtdeCamposTextForm = 6;
var largPR = 0;

var aCMPS=new Array();
aCMPS[0]='txtDATAINI;Preencha a data inicial';
aCMPS[1]='txtDATAFIN;Preencha a data final';
aCMPS[2]='txtREL_REPRESENTANTE;Identifique um representante válido ou 9999= todos';
aCMPS[3]='txtREL_GRUPO;Identifique um grupo ou deixe em branco';

Calendar.setup({
inputField:    "txtDATAINI",     
ifFormat  :     "%d/%m/%y",     
button    :    "btnDATAINI"    
});

Calendar.setup({
inputField:    "txtDATAFIN",     
ifFormat  :     "%d/%m/%y",     
button    :    "btnDATAFIN"    
});


var ajax = new execAjax();



/*******************************************************************************/
function teclado(e)         {

var lJanAuxilio= document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
if (window.event) tecla=window.event.keyCode;
else tecla=e.which;

if  (tecla==113) document.getElementById('btnGERAR').click();
if  (tecla==27 && lJanAuxilio)   	{fecharAUXILIO();return;}

if  (tecla==13 && lJanAuxilio)   	usouAUXILIO();
if  ( tecla==119 )  AuxilioF7(cfoco);  

if  (tecla==27) {e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='../relatorios.php';}

eval("rel_teclasNavegacao(e);");

}  

if (window.document.addEventListener) 
 window.document.addEventListener("keydown", teclado, false);
else 
 window.document.attachEvent("onkeydown", teclado);



/*******************************************************************************/
function ColocaFocoCmpInicial(cmp)   {
var lJanAuxilio = document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';

if (cmp!=null) 
	document.getElementById(cmp).focus();

else if (lJanAuxilio) 
  document.getElementById('txtPR2').focus();	
else 
  document.getElementById('txtREL_GRUPO').focus();
}	


/*******************************************************************************/
function VerCmp(nomeCMP)      {

if (nomeCMP!='todos')    {
  document.getElementById(nomeCMP).style.backgroundColor="white";
  
	switch (nomeCMP) {
		case 'txtREL_REPRESENTANTE':
		case 'txtREL_GRUPO':      
		case 'txtOPERADORA':
		case 'txtTIPO_CONTRATO':
		  vlr = document.getElementById(nomeCMP).value;
		  cmpLBL = document.getElementById( nomeCMP.replace('txt', 'lbl') );
		  
      if (vlr.rtrim().ltrim()=='') {cmpLBL.innerHTML = '';return true;}

      if (nomeCMP=='txtREL_REPRESENTANTE' && document.getElementById('lblREL_GRUPO').innerHTML.indexOf('ERRO')==-1 &&
            document.getElementById('lblREL_GRUPO').innerHTML.trim()!='' ) {
        document.getElementById('txtREL_REPRESENTANTE').value='';
        document.getElementById('lblREL_REPRESENTANTE').innerHTML='';return;
      }

      if (nomeCMP=='txtTIPO_CONTRATO' && document.getElementById('lblOPERADORA').innerHTML.indexOf('ERRO')==-1 &&
            document.getElementById('lblOPERADORA').innerHTML.trim()!='' ) {
        document.getElementById('txtTIPO_CONTRATO').value='';
        document.getElementById('lblTIPO_CONTRATO').innerHTML='';return;
      }

			cmpLBL.innerHTML = 'lendo...';
			showAJAX(1);
			ajax.criar('../ajax/ajaxAUXILIO.php?oQueAuxiliar=pesquisa_' + nomeCMP + '&vlr=' +vlr, '', 0);
			showAJAX(0);
			
      aRESP = ajax.ler().split(';');

      cIDCMP = aRESP[0]; 	cVLR = aRESP[1];
      cIDCMP=cIDCMP.rtrim().ltrim();

      if (nomeCMP=='txtTIPO_CONTRATO') {
        if (cVLR.indexOf('* ERRO *')==-1) cVLR = cVLR.split('!')[0];
      }
    	
    	if (cVLR.indexOf('ERRO')!=-1) cmpLBL.style.color='red';
    	else cmpLBL.style.color='blue';

      if ( nomeCMP=='txtREL_GRUPO' && cVLR.indexOf('ERRO')==-1) cVLR=cVLR.split('&nbsp;')[0];
        
    	cmpLBL.innerHTML = cVLR;
			
			break;
	}
	return;
}  

if (nomeCMP=='todos')         {
	
  for (h=0;h<aCMPS.length;h++)   {
  	cmp = aCMPS[h].split(';');
		cCMP = cmp[0]; 
		cMSG = cmp[1];
		
		cVLR = document.getElementById(cCMP).value.rtrim().ltrim();
    var label = cCMP.replace('txt', 'lbl');				
		erro=0;
		switch (cCMP)   {
			case 'txtDATAINI':
			case 'txtDATAFIN':			
        if ( cVLR=='' || ! verifica_data(cCMP) )   erro=1;
        break;
		case 'txtREL_REPRESENTANTE':      
      if ( document.getElementById(label).innerHTML.indexOf('ERRO')!=-1 ) erro=1;
      break;

		case 'txtREL_GRUPO':      
      if ( document.getElementById(label).innerHTML.indexOf('ERRO')!=-1 ) erro=1;
      break;

 		}

	 if (erro==1) {alert(cMSG);	document.getElementById(cCMP).focus(); return false;}	
	}	

  if ( document.getElementById('lblREL_REPRESENTANTE').innerHTML=='' && document.getElementById('lblREL_GRUPO').innerHTML=='' ) {
    alert('Identifique um grupo ou corretor válido'); return;
  } 
  var DATAINIMOSTRAR = document.getElementById('txtDATAINI').value.trim();
  var DATAINI = document.getElementById('txtDATAINI').value.trim();
  DATAINI = '20'+DATAINI.substring(6, 10)+DATAINI.substring(3, 5)+DATAINI.substring(0, 2);

  var DATAFINMOSTRAR = document.getElementById('txtDATAFIN').value.trim();
  var DATAFIN = document.getElementById('txtDATAFIN').value.trim();
  DATAFIN = '20'+DATAFIN.substring(6, 10)+DATAFIN.substring(3, 5)+DATAFIN.substring(0, 2);

    
  rdBUTTON = document.forms['frmREL'].elements['tipobusca'];
  var tipobusca='';
  for( i = 0; i < rdBUTTON.length; i++ ) {
    if( rdBUTTON[i].checked == true )     tipobusca = rdBUTTON[i].value;
  }
  rdBUTTON = document.forms['frmREL'].elements['tipoREL'];
  var tipoREL='';
  for( i = 0; i < rdBUTTON.length; i++ ) {
    if( rdBUTTON[i].checked == true )     tipoREL = rdBUTTON[i].value;
  }

	showAJAX(1);	
	ajax.criar('ajaxRELS.php?acao=vendas&DATAINI=' + DATAINI+'&DATAFIN='+DATAFIN+
        '&dataIniMostrar='+DATAINIMOSTRAR+'&dataFinMostrar='+DATAFINMOSTRAR+
        '&tipobusca='+tipobusca+'&tipoREL='+tipoREL+
        '&repre='+document.getElementById('txtREL_REPRESENTANTE').value+
        '&gr='+document.getElementById('txtREL_GRUPO').value+
        '&nomeGR='+document.getElementById('lblREL_GRUPO').innerHTML+
        '&ope='+document.getElementById('txtOPERADORA').value+
        '&nomeOPE='+document.getElementById('lblOPERADORA').innerHTML+
        '&prod='+document.getElementById('txtTIPO_CONTRATO').value+
        '&nomePROD='+document.getElementById('lblTIPO_CONTRATO').innerHTML, '', 0);
	showAJAX(0);


/*alert(ajax.ler());return;*/

  if (ajax.ler().indexOf('nada')!=-1) 
    alert('Nenhum registro encontrado');
  else
    window.open('../pdf/rel.php', 'nome', 'width=10,height=10' );	
 
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

/********************************************************************************/
function rel_teclasNavegacao(e)  {
if (window.event) tecla=window.event.keyCode;
else tecla=e.which;

// nao permite ;(59),'(39),"(34)Œ(179)  */
if ( (tecla==59) || (tecla==39) || (tecla==34) || (tecla==179)   )         void(0);


/* qdo a var CMP != null, significa que ha uma DIV de auxilio (auto complete) para o campo em questao 

nas linhas abaixo, somente 'navega' entre os campos caso a eventual DIV de auxilio do campo atual
nao esteja visivel, se estiver, quem vai manipular teclado é quem controla a DIV */

CMP = document.getElementById( cfoco.replace('txt','div') );


// seta para baixo ou Enter foi press
if ( tecla==40   ||  tecla==13   )         {

//alert(CMP);
//alert(CMP.style.display);
  if ( (CMP && CMP.style.display=='none') ||    ( ! CMP) ) {
    if (foco < nQtdeCamposTextForm)  {                 
      var cProx = document.getElementById( aOrdemCmps[foco+1] );
      cProx.focus();
    }       
  }   
}        

// seta para cima foi press
if ( tecla==38  )           {

  if (  (CMP && ! Element.visible(CMP))  ||  ! CMP )   {
    if (foco != 1)  {
      var cProx = document.getElementById(aOrdemCmps[foco-1]);
      cProx.focus();
    }
  }     
}
}


/********************************************************************************/
function seleciona2(opcao)  {

rdBUTTON = document.forms['frmREL'].elements['tipobusca'];
rdBUTTON[opcao-1].checked = true;

document.getElementById('txtDATAINI').focus();
}

/********************************************************************************/
function seleciona3(opcao)  {

rdBUTTON = document.forms['frmREL'].elements['tipoREL'];
rdBUTTON[opcao-1].checked = true;

document.getElementById('txtDATAINI').focus();
}



/********************************************************************************/
function lerDATAS()     {
ajax.criar('ajaxRELS.php?acao=lerDATAS', '', 0);  

var resp = ajax.ler().replace(/-/g, '/');
document.getElementById('txtDATAINI').value = resp.split(';')[2];
document.getElementById('txtDATAFIN').value = resp.split(';')[3];
}

/********************************************************************************/
function checar()  {

rdBUTTON = document.forms['frmREL'].gerarTXT;
rdBUTTON.checked = rdBUTTON.checked ? false : true;

document.getElementById('txtDATAINI').focus();
}






//]]>
</script>
</body>
</html>
