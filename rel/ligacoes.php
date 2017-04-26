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
    style="text-align:left;height:20%;width:70%"    >

      <tr height="5%"><td>

        <table WIDTH="100%"><tr  >      
          <td style="width:60%" style="cursor: move;"><span class="lblTitJanela">&nbsp;Ligações</td>
          <td style="width:30%" id="btnGERAR" align="center" onmouseout="this.style.backgroundColor='#F6F7F7;'" 
          onmouseover="this.style.backgroundColor='#efefef'" onclick="return VerCmp('todos');" >
            <span style="cursor: pointer;" class="lblTitJanela" >[ F2= gerar ]</span>
          </td>
          <td style="cursor: pointer;text-align:right;"  
            onclick="window.top.frames['framePRINCIPAL'].location.href='../ligacoes.php';" 
            class="lblTitJanela" >[ X ]</span></td>      
        </tr></table>

      </td></tr>					
    
      <tr valign="top"  ><td style="height:95%;width:100%" ><table width="100%">

    		<tr><td><table>
  				<td width="205px" align="right">Corretor:&nbsp;(9999= todos)</td>					
			    <td onmouseover="this.style.cursor='pointer';" title="Escolher representante (ou pressione F8)"  
          onclick="AuxilioF7('txtREL_REPRESENTANTE');"><image src="../images/setaBAIXO.gif"></td>
  				<td><input type="text" id="txtREL_REPRESENTANTE" tabindex="1"   
            maxlength="4" size="6" onKeyPress="return sistema_formatar(event, this, '0000');"></td>
			    <td ><span class="lblPADRAO_VLR_CMP"  id="lblREL_REPRESENTANTE">&nbsp;</td>
    		</table></td></tr>
        
        <tr ><td><table>
  				<td width="205px" align="right">Produto:</td>					
    			<td onmouseover="this.style.cursor='pointer';" title="Escolher operadora (ou pressione F8)" 
              onclick="AuxilioF7('txtATENDIMENTO_PRODUTO')";><image src="../images/setaBAIXO.gif"></td>
          <td><input type="text" id="txtATENDIMENTO_PRODUTO" tabindex="2"  value="" maxlength="4" size="6"
                onKeyPress="return sistema_formatar(event, this, '0000');"></td>
          <td style="padding-left:5px;WIDTH:120px" align="left"><span class="lblPADRAO_VLR_CMP" id="lblATENDIMENTO_PRODUTO"></span></td>
    		</table></td></tr>        

    		<tr><td><table>
  				<td width="205px" align="right">Situação: (somente para indicações)</td>					
			    <td onmouseover="this.style.cursor='pointer';" title="Escolher situação (ou pressione F8)"  
          onclick="AuxilioF7('txtSITUACAO');"><image src="../images/setaBAIXO.gif"></td>
  				<td><input type="text" id="txtSITUACAO" tabindex="3"   
            maxlength="4" size="6" onKeyPress="return sistema_formatar(event, this, '0000');"></td>
			    <td ><span class="lblPADRAO_VLR_CMP"  id="lblSITUACAO">&nbsp;</td>
    		</table></td></tr>

    		<tr><td><table>  					
    			<tr>
    				<td width="205px" align="right">Data Inicial:&nbsp;</td>					
    				<td><input type=text id="txtDATAINI" tabindex="4"  maxlength="8" size="10"
              onKeyPress="return sistema_formatar(event, this, '00/00/00');"  ></td>
          	<td><input type="button" id="btnDATAINI" title="Escolher data" class="btnSUBMIT" value="..."></td>              
    		</tr>
    		</table></td></tr>
    		
    		<tr><td><table>  					
    			<tr>
    				<td width="205px" align="right">Data final:&nbsp;</td>					
    				<td><input type=text id="txtDATAFIN" tabindex="5"  maxlength="8" size="10"
              onKeyPress="return sistema_formatar(event, this, '00/00/00');"  ></td>
          	<td><input type="button" id="btnDATAFIN" title="Escolher data" class="btnSUBMIT" value="..."></td>              
    		</tr>
    		</table></td></tr>
    		
    		<tr><td>&nbsp;</td></tr>
      </td></tr>

    		<tr><td><table>
    		  <td width="205px" align="right">Filtrar:&nbsp;</td>
          <td onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
            onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="seleciona3(1);">
            <input type="radio" id="tipoREL" name="tipoREL" value="1" checked >Todos os registros&nbsp;&nbsp;
          </td>
    		</table></td></tr>
    		<tr><td><table>
    		  <td width="205px" align="right">&nbsp;</td>
          <td onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
            onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="seleciona3(2);">
            <input type="radio" id="tipoREL" name="tipoREL" value="2" >Ligações telefônicas (plantão)&nbsp;&nbsp;
          </td>
    		</table></td></tr>
    		<tr><td><table>
    		  <td width="205px" align="right">&nbsp;</td>
          <td onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
            onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="seleciona3(3);">
            <input type="radio" id="tipoREL" name="tipoREL" value="3" >Indicações&nbsp;&nbsp;
          </td>
    		</table></td></tr>
    		<tr><td><table>
    		  <td width="205px" align="right">&nbsp;</td>
          <td onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
            onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="seleciona3(4);">
            <input type="radio" id="tipoREL" name="tipoREL" value="4" >Atendimento presencial&nbsp;&nbsp;
          </td>
    		</table></td></tr>
    		<tr><td><table>
    		  <td width="205px" align="right">&nbsp;</td>
          <td onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
            onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="seleciona3(5);">
            <input type="radio" id="tipoREL" name="tipoREL" value="5" >Ligações extra plantão&nbsp;&nbsp;
          </td>
    		</table></td></tr>


    		<tr><td>&nbsp;</td></tr>


    		<tr><td><table>
    		  <td width="205px" align="right">Tipo do relatório:&nbsp;</td>
          <td onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
            onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="seleciona4(1);">
            <input type="radio" id="formatoREL" name="formatoREL" value="1" checked >Listar registros
          </td>
    		</table></td></tr>

    		<tr><td><table>
    		  <td width="205px" >&nbsp;</td>
          <td onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
            onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="seleciona4(2);">
            <input type="radio" id="formatoREL" name="formatoREL" value="2" >Somatória por origem do atendimento
          </td>
    		</table></td></tr>

    		<tr><td><table>
    		  <td width="205px" >&nbsp;</td>
          <td onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
            onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="seleciona4(3);">
            <input type="radio" id="formatoREL" name="formatoREL" value="3" >Somatória por corretor
          </td>
    		</table></td></tr>

    		<tr><td><table>
    		  <td width="205px" >&nbsp;</td>
          <td onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
            onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="seleciona4(4);">
            <input type="radio" id="formatoREL" name="formatoREL" value="4" >Extrair nome, e-mail, telefone em XLS
          </td>
    		</table></td></tr>

    		<tr><td><table>
    		  <td width="205px" >&nbsp;</td>
          <td onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
            onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="seleciona4(5);">
            <input type="radio" id="formatoREL" name="formatoREL" value="5" >Somatória por situação (somente para indicações)
          </td>
    		</table></td></tr>

    		<tr><td>&nbsp;</td></tr>

  			<tr><td colspan=11><table onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="checar5();"><tr> 
          <td onclick="checar5();"><input type="checkbox" id="chkNOME" name="chkNOME" value="" /></td>
          <td align=left>Buscar corretor por nome (<font color=blue><b>para ler registros importados do sistema anterior em Excel</b></font>)</td>
          <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>         
        </tr>
        </table></td></tr>

    		<tr><td><table>
  			<tr>
  				<td width="205px" align="right">Nome buscar:&nbsp;</td>					
  				<td><input type=text id="txtNOME" tabindex="6"  maxlength="30" size="50" ></td>
        </tr>
    		</table></td></tr>


    
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
aCMPS[2]='txtREL_REPRESENTANTE;Identifique um corretor válido ou 9999= todos';
aCMPS[3]='txtNOME;Preencha um nome de corretor para buscar';
aCMPS[4]='txtSITUACAO;Identifique a situação ou deixe em branco';
aCMPS[5]='txtATENDIMENTO_PRODUTO;Identifique o produto (operadora) ou deixe em branco';

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

if  (tecla==27) {e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='../ligacoes.php';}

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
  document.getElementById('txtREL_REPRESENTANTE').focus();
}	


/*******************************************************************************/
function VerCmp(nomeCMP)      {

if (nomeCMP!='todos')    {
  document.getElementById(nomeCMP).style.backgroundColor="white";

	switch (nomeCMP) {
		case 'txtREL_REPRESENTANTE':
		case 'txtSITUACAO':
		case 'txtATENDIMENTO_PRODUTO':    
		  vlr = document.getElementById(nomeCMP).value;
		  cmpLBL = document.getElementById( nomeCMP.replace('txt', 'lbl') );
		  
      if (vlr.rtrim().ltrim()=='') {cmpLBL.innerHTML = '';return true;}

			cmpLBL.innerHTML = 'lendo...';
			showAJAX(1);
			ajax.criar('../ajax/ajaxAUXILIO.php?oQueAuxiliar=pesquisa_' + nomeCMP + '&vlr=' +vlr, '', 0);
			showAJAX(0);
			
      aRESP = ajax.ler().split(';');

      cIDCMP = aRESP[0]; 	cVLR = aRESP[1];
      cIDCMP=cIDCMP.rtrim().ltrim();

    	if (cVLR.indexOf('ERRO')!=-1) cmpLBL.style.color='red';
    	else cmpLBL.style.color='blue';

      if (nomeCMP=='txtSITUACAO') {
        rdBUTTON = document.forms['frmREL'].elements['tipoREL'];
        rdBUTTON[2].checked = true;
      }
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
        if ( document.getElementById(label).innerHTML.indexOf('ERRO')!=-1 ||
                (document.getElementById(label).innerHTML=='' && ! document.forms[0].chkNOME.checked && 
                            document.getElementById('txtNOME').value=='')  ) erro=1;
        break;
        
  		case 'txtATENDIMENTO_PRODUTO':      
        if ( document.getElementById(label).innerHTML.indexOf('ERRO')!=-1 )   erro=1;
        break;
        

			case 'txtNOME':			
        if ( cVLR=='' && document.forms[0].chkNOME.checked )   erro=1;
        break;


 		}

	 if (erro==1) {alert(cMSG);	document.getElementById(cCMP).focus(); return false;}	
	}	

  var DATAINIMOSTRAR = document.getElementById('txtDATAINI').value.trim();
  var DATAINI = document.getElementById('txtDATAINI').value.trim();
  DATAINI = '20'+DATAINI.substring(6, 10)+DATAINI.substring(3, 5)+DATAINI.substring(0, 2);

  var DATAFINMOSTRAR = document.getElementById('txtDATAFIN').value.trim();
  var DATAFIN = document.getElementById('txtDATAFIN').value.trim();
  DATAFIN = '20'+DATAFIN.substring(6, 10)+DATAFIN.substring(3, 5)+DATAFIN.substring(0, 2);

    
  rdBUTTON = document.forms['frmREL'].elements['tipoREL'];
  var tipoREL='';
  for( i = 0; i < rdBUTTON.length; i++ ) {
    if( rdBUTTON[i].checked == true )     tipoREL = rdBUTTON[i].value;
  }

  rdBUTTON = document.forms['frmREL'].elements['formatoREL'];
  var formatoREL='';
  for( i = 0; i < rdBUTTON.length; i++ ) {
    if( rdBUTTON[i].checked == true )     formatoREL = rdBUTTON[i].value;
  }
  if ( (tipoREL!=2 || formatoREL!=1) && document.forms[0].chkNOME.checked ) {
    alert('A busca por nome de corretor só funciona para \n\nFiltrar= LIGAÇÕES TELEFÔNICAS\n\nformato relatorio= LISTAR REGISTROS\n\n');
    return;
  }
  if (formatoREL==5 && tipoREL!=3)  {
    alert('A somatória por situação funciona somente para indicações\n\n');
    return;
  }   
	showAJAX(1);	
	ajax.criar('ajaxRELS.php?acao=ligacoes&DATAINI=' + DATAINI+'&DATAFIN='+DATAFIN+
        '&dataIniMostrar='+DATAINIMOSTRAR+'&dataFinMostrar='+DATAFINMOSTRAR+'&repre='+
        document.getElementById('txtREL_REPRESENTANTE').value+
        '&nomeREPRE='+document.getElementById('lblREL_REPRESENTANTE').innerHTML+' ('+
          document.getElementById('txtREL_REPRESENTANTE').value+')&tipoREL='+tipoREL+
          '&formato='+formatoREL+
          '&nome='+document.getElementById('txtNOME').value+
          '&porNOME='+document.forms[0].chkNOME.checked+'&situ='+
            document.getElementById('txtSITUACAO').value+
          '&operadora='+document.getElementById('txtATENDIMENTO_PRODUTO').value+
          '&nomeOPERADORA='+document.getElementById('lblATENDIMENTO_PRODUTO').innerHTML, '', 0);
	showAJAX(0);

/*
alert(ajax.ler());return;
*/
  if (ajax.ler().indexOf('nada')!=-1) 
    alert('Nenhum registro encontrado');
  else {
    if (ajax.ler().indexOf('xls')==-1) 
      window.open('../pdf/rel.php', 'nome', 'width=10,height=10' );
    else
      window.open(ajax.ler());
  }
	
 
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
function lerDATAS()     {
ajax.criar('ajaxRELS.php?acao=lerDATAS', '', 0);  

var resp = ajax.ler().replace(/-/g, '/');
document.getElementById('txtDATAINI').value = resp.split(';')[4];
document.getElementById('txtDATAFIN').value = resp.split(';')[4];
}


/********************************************************************************/
function seleciona3(opcao)  {

rdBUTTON = document.forms['frmREL'].elements['tipoREL'];
rdBUTTON[opcao-1].checked = true;

if (document.getElementById('txtSITUACAO').value.trim()!='') {
  rdBUTTON = document.forms['frmREL'].elements['tipoREL'];
  if (! rdBUTTON[2].checked) {
    alert('Quando você define uma situação, só pode listar INDICAÇÕES.');
  } 
  rdBUTTON[2].checked = true;
}


document.getElementById('txtDATAINI').focus();
}

/********************************************************************************/
function seleciona4(opcao)  {

rdBUTTON = document.forms['frmREL'].elements['formatoREL'];
rdBUTTON[opcao-1].checked = true;

/* se escolheu somatoria por situacao, seleciona tipo rel= indicacoes */
if (opcao==5) {
  rdBUTTON = document.forms['frmREL'].elements['tipoREL'];
  rdBUTTON[2].checked = true;
}
  
document.getElementById('txtDATAINI').focus();
}



/********************************************************************************/
function checar5()  {

rdBUTTON = document.forms['frmREL'].chkNOME;
rdBUTTON.checked = rdBUTTON.checked ? false : true;
if (rdBUTTON.checked) {
  document.getElementById('txtREL_REPRESENTANTE').value='';
  document.getElementById('lblREL_REPRESENTANTE').innerHTML='';

  rdBUTTON = document.forms['frmREL'].elements['tipoREL'];
  rdBUTTON[1].checked=true;
  rdBUTTON = document.forms['frmREL'].elements['formatoREL'];
  rdBUTTON[0].checked=true; 

  document.getElementById('txtNOME').focus();
}
else {
  rdBUTTON = document.forms['frmREL'].elements['tipoREL'];
  rdBUTTON[0].checked=true;
  rdBUTTON = document.forms['frmREL'].elements['formatoREL'];
  rdBUTTON[0].checked=true; 

  document.getElementById('txtNOME').value='';
  document.getElementById('txtDATAINI').focus();
}
}



//]]>
</script>
</body>
</html>
