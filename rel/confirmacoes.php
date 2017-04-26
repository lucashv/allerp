<?php 
ob_start();
require("../doctype.php"); 
session_start();
?>

<head>
<link href="../<?php echo $_SESSION['arqCSS']; ?>" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../js/funcoes.js" xml:space="preserve"></script>
<script type="text/javascript" src="../js/edicaoDados.js" xml:space="preserve"></script>

<!-- Folha de estilos do calendßrio -->
<link rel="stylesheet" type="text/css" media="all" href="../js/jscalendar-1.0/calendar-win2k-1.css" title="win2k-cold-1" />

<!-- biblioteca principal do calendario -->
<script type="text/javascript" src="../js/jscalendar-1.0/calendar.js"></script>

<!-- biblioteca para carregar a linguagem desejada -->
<script type="text/javascript" src="../js/jscalendar-1.0/lang/calendar-en.js"></script>

<!-- biblioteca que declara a funþÒo Calendar.setup, que ajuda a gerar um calendßrio em poucas linhas de cµdigo -->
<script type="text/javascript" src="../js/jscalendar-1.0/calendar-setup.js"></script> 

<title>
AMEG
</title>
</head>

<body style="HEIGHT: 100%; width:100%;" 
  onload="Avisa('');lerPERIODOS();lerDATAS();Muda_CSS();ColocaFocoCmpInicial();">

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
<input id="dataHOJE" type="hidden" value="" >
<input id="SELECAO_2" type="hidden" value="" >

<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">

    <table cellspacing="0" cellpadding="0" border="1"  bgcolor="white"   class="frmJANELA"    
    style="text-align:left;height:20%;width:80%"    >

      <tr height="5%"><td>

        <table WIDTH="100%"><tr  >      
          <td style="width:60%" style="cursor: move;"><span class="lblTitJanela">&nbsp;Confirmaþ§es</td>
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

    		<tr><td><table>
  				<td width="110px" align="right">Corretor:</td>					
			    <td onmouseover="this.style.cursor='pointer';" title="Escolher representante (ou pressione F8)"  
          onclick="AuxilioF7('txtREL_REPRESENTANTE');"><image src="../images/setaBAIXO.gif"></td>
  				<td><input type="text" id="txtREL_REPRESENTANTE" tabindex="1"   
            maxlength="4" size="6" onKeyPress="return sistema_formatar(event, this, '0000');"></td>
			    <td><span class="lblPADRAO_VLR_CMP" id="lblREL_REPRESENTANTE"></td>
    		</table></td></tr>

    		<tr><td><table>
  				<td width="110px" align="right">(9999= todos)</td>					
    		</table></td></tr>

    		<tr ><td><table>
  				<td width="110px" align="right">Operadora:</td>					
    			<td onmouseover="this.style.cursor='pointer';" title="Escolher operadora (ou pressione F8)" 
              onclick="AuxilioF7('txtOPERADORA')";><image src="../images/setaBAIXO.gif"></td>
          <td><input type="text" id="txtOPERADORA" tabindex="2"  value="" maxlength="4" size="6"
                onKeyPress="return sistema_formatar(event, this, '0000');"></td>
          <td style="padding-left:5px;WIDTH:120px" align="left"><span class="lblPADRAO_VLR_CMP" id="lblOPERADORA"></span></td>
    		</table></td></tr>

    		<tr style="height:30px"><td><table>
  				<td width="110px" align="right">Período:</td>					
          <td id="tdPERIODOS"></td>
          <td>&nbsp;&nbsp;&nbsp;Nº relatório: </td>
			    <td><span class="lblPADRAO_VLR_CMP" id=lblID_RELATORIO>&nbsp;</td>
    		</table></td></tr>



    		<tr><td><table>
    		  <tr>
            <td>&nbsp;&nbsp;Período para buscar confirmações: </td>
            <td width="30px">&nbsp;</td>
            <td>(buscar por: </td>            

            <td onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
              onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="seleciona2(1);">
              <input type="radio" id="tipobusca" name="tipobusca" value="1" checked >Data do pagamento&nbsp;&nbsp;
            </td>
            <td onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
              onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="seleciona2(2);">
              <input type="radio" id="tipobusca" name="tipobusca" value="2" >Data do repasse&nbsp;)
            </td>
          </tr>  
    		</table></td></tr>            

    		<tr><td><table>  					
    			<tr>
    				<td width="110px" align="right">Data Inicial:&nbsp;</td>					
    				<td><input type=text id="txtDATAINI" tabindex="3"  maxlength="8" size="10"
              onKeyPress="return sistema_formatar(event, this, '00/00/00');"  ></td>
          	<td><input type="button" id="btnDATAINI" title="Escolher data" class="btnSUBMIT" value="..."></td>              
    		</tr>
    		</table></td></tr>
    		
    		<tr><td><table>  					
    			<tr>
    				<td width="110px" align="right">Data final:&nbsp;</td>					
    				<td><input type=text id="txtDATAFIN" tabindex="4"  maxlength="8" size="10"
              onKeyPress="return sistema_formatar(event, this, '00/00/00');"  ></td>
          	<td><input type="button" id="btnDATAFIN" title="Escolher data" class="btnSUBMIT" value="..."></td>              
    		</tr>
    		</table></td></tr>
        
        <tr><td>&nbsp;</td></tr>
        
    		<tr><td>&nbsp;&nbsp;Período para buscar créditos/débitos (campo usado: data para pagar)</td></tr>

    		<tr><td><table>  					
    			<tr>
    				<td width="110px" align="right">Data Inicial:&nbsp;</td>					
    				<td><input type=text id="txtDATAINI_VALES" tabindex="5"  maxlength="8" size="10"
              onKeyPress="return sistema_formatar(event, this, '00/00/00');"  ></td>
          	<td><input type="button" id="btnDATAINI_VALES" title="Escolher data" class="btnSUBMIT" value="..."></td>              
    		</tr>
    		</table></td></tr>
    		
    		<tr><td><table>  					
    			<tr>
    				<td width="110px" align="right">Data final:&nbsp;</td>					
    				<td><input type=text id="txtDATAFIN_VALES" tabindex="6"  maxlength="8" size="10"
              onKeyPress="return sistema_formatar(event, this, '00/00/00');"  ></td>
          	<td><input type="button" id="btnDATAFIN_VALES" title="Escolher data" class="btnSUBMIT" value="..."></td>              
    		</tr>
    		</table></td></tr>


  			<tr><td colspan=11><table onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="checar();"><tr> 
          <td onclick="checar();"><input type="checkbox" id="chkGERAL" name="chkGERAL" value="" /></td>
          <td>Relatório completo (repasse 0% / listar corretores sem pular página)</td></td>
          <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>         
        </tr>
    		</table></td></tr>

  			<tr><td colspan=11><table onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="checar2();"><tr> 
          <td onclick="checar2();"><input type="checkbox" id="chkREGISTRAR" name="chkREGISTRAR" value="" /></td>
          <td>Registrar data do repasse: </td>
          <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td style="padding-left:40px" colspan=11><input type=text id="txtDATAREPASSE" tabindex="7"  maxlength="8" size="10"
              onKeyPress="return sistema_formatar(event, this, '00/00/00');"  >
          </td>                   
        </tr></table></td></tr>


  			<tr><td colspan=11><table onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="checar4();"><tr> 
          <td onclick="checar4();"><input type="checkbox" id="chkADIANTAMENTO" name="chkADIANTAMENTO" value="" /></td>
          <td>Buscar somente 1as parcelas marcadas para pagar (Adiantamento)</td>
          <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>         
        </tr></table></td></tr>

  			<tr><td colspan=11><table  onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="checar3();"><tr > 
          <td id=tdCREDITOS onclick="checar3();"><input type="checkbox" id="chkCREDITOS" name="chkCREDITOS" value="" /></td>
          <td id=tdCREDITOS2>Incluir créditos/débitos (<font color=red><b>serß usada a DATA PARA PAGAR ao selecionar os registros</b></font>)</td>
          <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>         
        </tr></table></td></tr>

  			<tr><td colspan=11><table  onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="checar5();"><tr > 
          <td onclick="checar5();"><input type="checkbox" id="chkDENOVO" name="chkDENOVO" value="" /></td>
          <td>Listar mensalidades com data de repasse já gravada</td>
          <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>         
        </tr></table></td></tr>
        

        
      </td></tr>
    
    </table>   
    
    
</td></tr></table>


<script language="javascript" type="text/javascript" xml:space="preserve">
//<![CDATA[
/* qtde de campos text na tela de ediþÒo, necessario informar  */

var nQtdeCamposTextForm = 6;
var largPR = 0;

var aCMPS=new Array();
aCMPS[0]='txtDATAINI;Preencha a data inicial vßlida';
aCMPS[1]='txtDATAFIN;Preencha a data final vßlida';
aCMPS[2]='txtREL_REPRESENTANTE;Identifique um representante vßlido ou 9999= todos';
aCMPS[3]='txtDATAREPASSE;Preencha uma data de repasse vßlida';
aCMPS[4]='txtDATAINI_VALES;Preencha a data inicial vales vßlida';
aCMPS[5]='txtDATAFIN_VALES;Preencha a data final vales vßlida';


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

Calendar.setup({
inputField:    "txtDATAINI_VALES",     
ifFormat  :     "%d/%m/%y",     
button    :    "btnDATAINI_VALES"    
});

Calendar.setup({
inputField:    "txtDATAFIN_VALES",     
ifFormat  :     "%d/%m/%y",     
button    :    "btnDATAFIN_VALES"    
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
  document.getElementById('txtREL_REPRESENTANTE').focus();
}	


/*******************************************************************************/
function VerCmp(nomeCMP)      {

if (nomeCMP!='todos')    {
  document.getElementById(nomeCMP).style.backgroundColor="white";
  
	switch (nomeCMP) {
		case 'txtREL_REPRESENTANTE':
		case 'txtOPERADORA':
		  vlr = document.getElementById(nomeCMP).value;
		  cmpLBL = document.getElementById( nomeCMP.replace('txt', 'lbl') );
		  
      if (vlr.rtrim().ltrim()=='') {
        cmpLBL.innerHTML = ''; if (nomeCMP=='txtOPERADORA') lerPERIODOS(); return true;
      }
			cmpLBL.innerHTML = 'lendo...';
			showAJAX(1);
			ajax.criar('../ajax/ajaxAUXILIO.php?oQueAuxiliar=pesquisa_' + nomeCMP + '&vlr=' +vlr, '', 0);
			showAJAX(0);
			
      aRESP = ajax.ler().split(';');  
    	
      cIDCMP = aRESP[0]; 	cVLR = aRESP[1];
      cIDCMP=cIDCMP.rtrim().ltrim();
    	
    	if (cVLR.indexOf('ERRO')!=-1) cmpLBL.style.color='red';
    	else cmpLBL.style.color='blue';

      if (nomeCMP=='txtOPERADORA' ) lerPERIODOS();

    	cmpLBL.innerHTML = cVLR;

      if (nomeCMP=='txtREL_REPRESENTANTE' && cmpLBL.innerHTML=='TODOS') {
        cmpLBL.innerHTML += ' <font color=red>&nbsp;&nbsp;** Qtde corretores pagar/pagos serß atualizada neste relatµrio</font>'
      }

			
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
			case 'txtDATAINI_VALES':
			case 'txtDATAFIN_VALES':		
			case 'txtDATAREPASSE':
        /* se for adto nao verifica nada nada, pois nao precisa */
        if (! document.forms[0].chkCREDITOS.checked && (cCMP=='txtDATAINI_VALES' || cCMP=='txtDATAFIN_VALES')) break; 

        if (! document.forms[0].chkADIANTAMENTO.checked) {
          if (cCMP=='txtDATAREPASSE' && ! document.forms['frmREL'].chkREGISTRAR.checked) continue;
  
          if ( cVLR=='' || ! verifica_data(cCMP) )   erro=1;
        }

        break;
		case 'txtREL_REPRESENTANTE':      
      if ( document.getElementById(label).innerHTML.indexOf('ERRO')!=-1 ||
          document.getElementById(label).innerHTML=='') erro=1;
      break;
  	case 'txtOPERADORA':			
      if ( document.getElementById(label).innerHTML.indexOf('ERRO')!=-1 ) erro=1;
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
  
  var DATAINIMOSTRAR_VALES = document.getElementById('txtDATAINI_VALES').value.trim();
  var DATAINI_VALES = document.getElementById('txtDATAINI_VALES').value.trim();
  DATAINI_VALES = '20'+DATAINI_VALES.substring(6, 10)+DATAINI_VALES.substring(3, 5)+DATAINI_VALES.substring(0, 2);

  var DATAFINMOSTRAR_VALES = document.getElementById('txtDATAFIN_VALES').value.trim();
  var DATAFIN_VALES = document.getElementById('txtDATAFIN_VALES').value.trim();
  DATAFIN_VALES = '20'+DATAFIN_VALES.substring(6, 10)+DATAFIN_VALES.substring(3, 5)+DATAFIN_VALES.substring(0, 2);

  var DATAREPASSE = document.getElementById('txtDATAREPASSE').value.trim();
  DATAREPASSE = '20'+DATAREPASSE.substring(6, 10)+DATAREPASSE.substring(3, 5)+DATAREPASSE.substring(0, 2);

  rdBUTTON = document.forms['frmREL'].elements['tipobusca'];
  var tipobusca='';
  for( i = 0; i < rdBUTTON.length; i++ ) {
    if( rdBUTTON[i].checked == true )     tipobusca = rdBUTTON[i].value;
  }
    
  if (document.forms[0].chkREGISTRAR.checked) {
    if (! confirm('Você vai registrar o repasse, pagamento das mensalidades\n\n'+
                'Estas mensalidades não serão listadas novamente no relatório de confirmações\n\n'+
                'Continua??') ) return;
  }
  if ( document.forms[0].chkDENOVO.checked ) {
    alert('Para listar mensalidades já pagas, você precisa da senha do administrador '+
            '(usuário número 1)\n\nou da senha de algum usußrio que tenha acesso ao caixa geral');
    
    var senha=prompt('Senha:','')
    
    if (senha==null) return;
    if (senha.rtrim()=='') return;
    
    showAJAX(1);
    ajax.criar('ajaxRELS.php?acao=senhaREPASSE&vlr='+senha, '', 0);
    showAJAX(0);

   
    if (ajax.ler()=='nao') {
      alert('Senha incorreta');
      return;
    }
  }

  if (document.getElementById('lblID_RELATORIO').innerHTML.trim()=='') {
    alert('Você não especificou um relatório, você escolheu um período manualmente\n\n'+
          'Nenhuma folha de pagamento será atualizada');
  }
	showAJAX(1);	
	ajax.criar('ajaxRELS.php?acao=confirmacoes'+
        '&DATAINI=' + DATAINI+'&DATAFIN='+DATAFIN+
        '&dataIniMostrar='+DATAINIMOSTRAR+'&dataFinMostrar='+DATAFINMOSTRAR+
        '&DATAINI_VALES=' + DATAINI_VALES+'&DATAFIN_VALES='+DATAFIN_VALES+        
        '&dataIniMostrar_VALES='+DATAINIMOSTRAR_VALES+'&dataFinMostrar_VALES='+DATAFINMOSTRAR_VALES+        
        '&repre='+document.getElementById('txtREL_REPRESENTANTE').value+
        '&geral='+document.forms[0].chkGERAL.checked+
        '&gravarDATA='+document.forms[0].chkREGISTRAR.checked+
        '&tipobusca='+tipobusca+
        '&operadora='+document.getElementById('txtOPERADORA').value+';'+
            document.getElementById('lblOPERADORA').innerHTML+'&repasse='+DATAREPASSE+
        '&creditos='+document.forms[0].chkCREDITOS.checked+
        '&adiantamento='+document.forms[0].chkADIANTAMENTO.checked+
        '&denovo='+document.forms[0].chkDENOVO.checked+'&idRELATORIO='+document.getElementById('lblID_RELATORIO').innerHTML, '', 0);
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

// nao permite ;(59),'(39),"(34)î(179)  */
if ( (tecla==59) || (tecla==39) || (tecla==34) || (tecla==179)   )         void(0);


/* qdo a var CMP != null, significa que ha uma DIV de auxilio (auto complete) para o campo em questao 

nas linhas abaixo, somente 'navega' entre os campos caso a eventual DIV de auxilio do campo atual
nao esteja visivel, se estiver, quem vai manipular teclado Ú quem controla a DIV */

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
document.getElementById('txtDATAINI').value = resp.split(';')[2];
document.getElementById('txtDATAFIN').value = resp.split(';')[3];
document.getElementById('txtDATAINI_VALES').value = resp.split(';')[2];
document.getElementById('txtDATAFIN_VALES').value = resp.split(';')[3];

document.getElementById('dataHOJE').value = resp.split(';')[4];
}

/********************************************************************************/
function checar()  {

rdBUTTON = document.forms['frmREL'].chkGERAL;
rdBUTTON.checked = rdBUTTON.checked ? false : true;

if (rdBUTTON.checked) { 
  document.getElementById('tdCREDITOS').style.display='none';
  document.getElementById('tdCREDITOS2').style.display='none';
  document.getElementById('chkCREDITOS').checked=false;
}
else { 
  document.getElementById('tdCREDITOS').style.display='inline';
  document.getElementById('tdCREDITOS2').style.display='inline';
}


document.getElementById('txtDATAINI').focus();
}

/********************************************************************************/
function checar3()  {

rdBUTTON = document.forms['frmREL'].chkCREDITOS;
rdBUTTON.checked = rdBUTTON.checked ? false : true;

document.getElementById('txtDATAINI').focus();
}

/********************************************************************************/
function checar4()  {

rdBUTTON = document.forms['frmREL'].chkADIANTAMENTO;
rdBUTTON.checked = rdBUTTON.checked ? false : true;

document.getElementById('txtDATAINI').focus();
}

/********************************************************************************/
function checar5()  {

rdBUTTON = document.forms['frmREL'].chkDENOVO;
rdBUTTON.checked = rdBUTTON.checked ? false : true;

document.getElementById('txtDATAINI').focus();
}



/********************************************************************************/
function checar2()  {

rdBUTTON = document.forms['frmREL'].chkREGISTRAR;
rdBUTTON.checked = rdBUTTON.checked ? false : true;
if (rdBUTTON.checked) {
  document.getElementById('txtDATAREPASSE').value=document.getElementById('dataHOJE').value;
  document.getElementById('txtDATAREPASSE').focus();
}
else {
  document.getElementById('txtDATAREPASSE').value='';
  document.getElementById('txtREL_REPRESENTANTE').focus();
}

document.getElementById('txtDATAINI').focus();
}

/********************************************************************************/
function seleciona2(opcao)  {

rdBUTTON = document.forms['frmREL'].elements['tipobusca'];
rdBUTTON[opcao-1].checked = true;

document.getElementById('txtDATAINI').focus();
}

/********************************************************************************/
function lerPERIODOS()     {
/* se for operadora 1= AMIL, permite trabalhar com periodos pre estipulados, se nao, trabalha com periodo
  escolhido pelo usuario */

document.getElementById('txtDATAINI').readOnly=true;
document.getElementById('txtDATAFIN').readOnly=true;
document.getElementById('txtDATAINI_VALES').readOnly=true;
document.getElementById('txtDATAFIN_VALES').readOnly=true;

if (document.getElementById('txtOPERADORA').value.trim()=='') {
  document.getElementById('tdPERIODOS').innerHTML = '-';
  document.getElementById('txtDATAINI').value='';
  document.getElementById('txtDATAFIN').value='';
  document.getElementById('txtDATAINI_VALES').value='';
  document.getElementById('txtDATAFIN_VALES').value='';
  document.getElementById('lblID_RELATORIO').innerHTML='';
  return;
}

/* se ja ha listbox montada, nao a remonta */
showAJAX(1);
ajax.criar('ajaxRELS2.php?acao=lerPERIODOS&op='+document.getElementById('txtOPERADORA').value, '', 0);
showAJAX(0);

document.getElementById('tdPERIODOS').innerHTML = ajax.ler();

if (ajax.ler().indexOf('NENHUM PE')!=-1) {
  document.getElementById('txtDATAINI').value='';
  document.getElementById('txtDATAFIN').value='';
  document.getElementById('txtDATAINI_VALES').value='';
  document.getElementById('txtDATAFIN_VALES').value='';
  document.getElementById('lblID_RELATORIO').innerHTML='';
  return;
}

mudouPERIODO();
}


/********************************************************************************/
function mudouPERIODO()     {
var lstbox = document.getElementById('lstPERIODOS');
var info = lstbox[lstbox.selectedIndex].value;

document.getElementById('txtDATAINI').value=info.split('|')[0];
document.getElementById('txtDATAFIN').value=info.split('|')[1];
document.getElementById('txtDATAINI_VALES').value=info.split('|')[2];
document.getElementById('txtDATAFIN_VALES').value=info.split('|')[3];

document.getElementById('lblID_RELATORIO').innerHTML=info.split('|')[4];

document.getElementById('txtDATAINI').focus();
document.getElementById('txtDATAINI').focus();
}



//]]>
</script>
</body>
</html>
