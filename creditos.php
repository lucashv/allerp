<?php ob_start();require("doctype.php"); session_start();?><head><link href="<?php echo $_SESSION['arqCSS']; ?>" type="text/css" rel="stylesheet" /><script type="text/javascript" src="js/funcoes.js" xml:space="preserve"></script><script type="text/javascript" src="js/menuContexto.js" xml:space="preserve"></script><script type="text/javascript" src="js/edicaoDados.js" xml:space="preserve"></script><script language=javascript>Date.prototype.addDays = function(days) {this.setDate(this.getDate()+days);} </script><!-- Folha de estilos do calend�rio --><link rel="stylesheet" type="text/css" media="all" href="js/jscalendar-1.0/calendar-win2k-1.css" title="win2k-cold-1" /><!-- biblioteca principal do calendario --><script type="text/javascript" src="js/jscalendar-1.0/calendar.js"></script><!-- biblioteca para carregar a linguagem desejada --><script type="text/javascript" src="js/jscalendar-1.0/lang/calendar-en.js"></script><!-- biblioteca que declara a fun��o Calendar.setup, que ajuda a gerar um calend�rio em poucas linhas de c�digo --><script type="text/javascript" src="js/jscalendar-1.0/calendar-setup.js"></script> <style type="text/css" xml:space="preserve">.cssDIV_EDICAO {position: absolute; top: 200px;  width: 800px; height: 80px;	margin-top: -220px; margin-left: -400px; display:block; z-index:3;}.cssDIV_AJAX {position: absolute; top: 200px; top:50%; left: 50%; width: 50px; height: 50px;	margin-top: -40px; margin-left: -10px; display:block; z-index:20; }</style><?$usandoTelaMaior1024_768 = $_SESSION['usarTipoIMAGEM'] == '_HD' ? true : false;if ($usandoTelaMaior1024_768) {?>  <style type="text/css" xml:space="preserve">  .cssDIV_EDICAO_ENT {  position: absolute; top: 200px;  width: 980px; height: 400px;	  margin-top: -320px; margin-left: -490px; display:block; z-index:30;}  .cssDIV_EDICAO_CAIXA {  position: absolute; top: 200px;  width: 980px; height: 400px;	  margin-top: -270px; margin-left: -490px; display:block; z-index:30;}  </style><?  }else  {?>  <style type="text/css" xml:space="preserve">  .cssDIV_EDICAO_ENT {  position: absolute; top: 200px;  width: 980px; height: 400px;	  margin-top: -270px; margin-left: -490px; display:block; z-index:3;}  .cssDIV_EDICAO_CAIXA {  position: absolute; top: 200px;  width: 980px; height: 400px;	  margin-top: -250px; margin-left: -490px; display:block; z-index:3;}  </style><?  }?></head><body style="HEIGHT: 100%; width:100%;" onload="lerHOJE();lerREGS();Avisa('');Muda_CSS();ColocaFocoCmpInicial();"><script type="text/javascript" xml:space="preserve">//<![CDATA[/* prepara menu de contexto (botao direito do mouse) */SimpleContextMenu.setup({'preventDefault':true, 'preventForms':false});SimpleContextMenu.attach('container', 'CM1');//]]></script><ul id="CM1" class="SimpleContextMenu">  <li><a href="javascript:incluirREG();">Novo registro</a></li>  <li><a href="javascript:editarREG();">Editar registro</a></li>  <li><a href="javascript:excluirREG();">Excluir registro</a></li>  </ul><form id="frmCREDITOS" name="frmCREDITOS" autocomplete="off" action="" ><div id="divEDICAO" class="cssDIV_ESCONDE"></div><div id="divCAIXA" class="cssDIV_ESCONDE"></div><div id="divAUXILIO" class="cssDIV_ESCONDE">&nbsp;</div><div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" bgcolor=red>  <table height="50px" bgcolor="#7CA7FF" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">    <tr valign="middle"><td><img src="images/database.png" alt="" /></td></tr>  </table></div><input id="SELECAO" type="hidden" value="" /><input id="SELECAO_2" type="hidden" value="" ><input id="dataTRAB" type="hidden" value="" ><input id="lendoATUAL" type="hidden" value="creditos" ><table><tr align="center" valign="middle">  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">      <table cellspacing="0" cellpadding="0" border="1" width="95%"  bgcolor="white" style="text-align:left;">      <tr><td><table width="100%" cellpadding="0" cellspacing="2"><tr>        <td width="60%"><span class="lblTitJanela" id="lblTITULO">&nbsp;&nbsp;</span>          <input type="text" id="txtDATATRAB" value=""               style="color: white;background-color: white; border: 0px solid white;font-size:0px;height:0px"               onchange="lerHOJE(1);lerREGS();";/>                </td>              	<td title="Relat�rio cr�ditos/descontos" align="center" onmouseout="this.style.backgroundColor='white'"       	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="rel();" >      	  <img src="images/protocolo.png" />      	</td>      	<td title="Alternar registrados em/para pagar em" align="center" onmouseout="this.style.backgroundColor='white'"       	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="alternar();" >      	  <img src="images/alternar2.png" />      	</td>      	<td id="btnDATATRAB" title="Escolher data" align="center" onmouseout="this.style.backgroundColor='white'"       	onmouseover="this.style.backgroundColor='#A9B2CA'"  >      	  <img src="images/buscadata.png" />      	</td>                <td id="btnPESQUISAR" title="Pesquisa palavra (Ctrl+B)"  align="center" onmouseout="this.style.backgroundColor='white'"         onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="buscar();" >          <img src="images/pesquisa.png" />        </td>        <td  id="btnRETORNAR" title="Retornar 1 dias (seta p/ esquerda)" align="center" onmouseout="this.style.backgroundColor='white'"         onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="lerREGS(-1);" >          <img src="images/setaESQUERDA.png" />        </td>                <td id="btnAVANCAR" title="Avan�ar 1 dia (seta p/ direita)" align="center" onmouseout="this.style.backgroundColor='white'"         onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="lerREGS(1);" >          <img src="images/setaDIREITA.png" />        </td>                        <td style="cursor: pointer;text-align:right;"            onclick="window.top.frames['framePRINCIPAL'].location.href='inicial.php';"           class="lblTitJanela" >[ X ]</span>        </td>            </tr></table></td></tr>      <tr>        <td valign="top" height="<?php echo ($_SESSION['altIFRAME'] * .65); ?> px" >          <div id="titTABELA">&nbsp;</div>          <div id="divTABELA" style="overflow:auto;min-height:95%;height:95%" class="container"></div>        </td>      </tr>      <tr><td align=left><table width="100%"  ><tr>        <td width="40%"><table><tr>          <td ><span class="lblPADRAO">&nbsp;&nbsp;&nbsp;&nbsp;Total a pagar vale cr�ditos:</span></td>          <td  ><span class="lblPADRAO" id=lblPAGAR style='color:red;font-weight:bold;'>&nbsp;</span></td>        </tr></table></td>      	<td style='cursor:pointer;' width="10%"  align=center title="Mostrar vale cr�ditos dos �ltimos 45 dias" align="center" onmouseout="this.style.backgroundColor='white'"       	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="document.getElementById('lendoATUAL').value='valecreditos45';lerREGS();" >      	  <img src="images/lerVALECREDITOS3.png" />      	</td>      	<td style='cursor:pointer;' width="10%"  align=center title="Mostrar vale cr�ditos pendentes" align="center" onmouseout="this.style.backgroundColor='white'"       	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="document.getElementById('lendoATUAL').value='valecreditospendentes';lerREGS();" >      	  <img src="images/lerVALECREDITOS2.png" />      	</td>      	<td style='cursor:pointer;' width="10%"  align=center title="Mostrar somente vale cr�ditos" align="center" onmouseout="this.style.backgroundColor='white'"       	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="document.getElementById('lendoATUAL').value='valecreditos';lerREGS();" >      	  <img src="images/lerVALECREDITOS.png" />      	</td>      	<td style='cursor:pointer;' width="10%"  align=center title="Mostrar cr�ditos/d�bitos dos �ltimos 45 dias" align="center" onmouseout="this.style.backgroundColor='white'"       	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="document.getElementById('lendoATUAL').value='creditos45';lerREGS();" >      	  <img align=center src="images/lerCREDITOS2.png" />      	</td>      	<td style='cursor:pointer;' width="10%"  align=center title="Mostrar somente cr�ditos/d�bitos" align="center" onmouseout="this.style.backgroundColor='white'"       	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="document.getElementById('lendoATUAL').value='creditos';lerREGS();" >      	  <img align=center src="images/lerCREDITOS.png" />      	</td>      	<td style='cursor:pointer;' width="10%"  align=center title="Mostrar todos os registros" align="center" onmouseout="this.style.backgroundColor='white'"       	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="document.getElementById('lendoATUAL').value='';lerREGS();"" >      	  <font face=verdana size='+1'>TODOS</font>      	</td>      </tr></table></td></tr>      <tr><td><table width="100%"><tr>        <td width="10px">&nbsp;</td>        <td width="10px"><span style="background-color:red">&nbsp;&nbsp;&nbsp;</span></td>        <td><span class="lblPADRAO">= d�bitos</span></td>        <td width="10px"><span style="background-color:blue">&nbsp;&nbsp;&nbsp;</span></td>        <td><span class="lblPADRAO">= cr�ditos</span></td>                              <td align="right">        <input id="txtFOCADO" type="text" value=""           style="color: white;background-color: white; border: 0px solid white;font-size:0px;" />        <span class="lblUSUARIO" id="totREGS"><span>        </td>              </tr></table></td></tr>    </table>  </td></tr></table></form><script language="javascript" type="text/javascript" xml:space="preserve">//<![CDATA[/* qtde de campos text na tela de edi��o, necessario informar  */var nQtdeCamposTextForm = 6;var largPR = 0;var aCMPS=new Array(1);aCMPS[0]='txtTIPO;Digite o tipo';aCMPS[1]='txtREPRESENTANTE;Identifique o representante';aCMPS[2]='txtDATA;Digite uma data registro v�lida';aCMPS[3]='txtVALOR;Digite o valor';aCMPS[4]='txtDESCRICAO;Digite a descri��o';aCMPS[5]='txtDATAPAGAR;Digite uma data de pagamento v�lida';Calendar.setup({inputField:    "txtDATATRAB",     ifFormat  :     "%d/%m/%Y",     button    :    "btnDATATRAB"    });var ajax = new execAjax();/*******************************************************************************/function alternar()         {showAJAX(1);ajax.criar('ajax/ajaxCREDITOS.php?acao=alternarTipoCreditoLendo', '', 0);showAJAX(0);lerREGS();}    /*******************************************************************************/function lerHOJE( buscarDataEscolhida )         {if (typeof buscarDataEscolhida=='undefined')     {  showAJAX(1);  ajax.criar('ajax/ajaxCAIXA.php?acao=lerDataHoje', '', 0);    var hoje=ajax.ler();    hoje = hoje.replace(/<br>/g, String.fromCharCode(13));} else  hoje = document.getElementById('txtDATATRAB').value;   /*var pridiaMES = '01'+ hoje.substring(2);*/var pridiaMES = hoje;pridiaMES = pridiaMES.replace('/', '');pridiaMES = pridiaMES.replace('/', '');pridiaMES = pridiaMES.replace('/', '');document.getElementById('dataTRAB').value = pridiaMES;showAJAX(0);}/*******************************************************************************/function teclado(e)         {if (window.event) tecla=window.event.keyCode;else tecla=e.which;lCTRL = e.ctrlKey;var lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';var lJanAuxilio= document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';var lJanCAIXA= document.getElementById('divCAIXA').getAttribute(propCLASSE)!='cssDIV_ESCONDE';if  (tecla==45 && ! lJanRegistro && ! lJanCAIXA)   	incluirREG();if  (tecla==27) {          if (lJanAuxilio)   	fecharAUXILIO();  else if (lJanCAIXA)   	fecharCAIXA();  else if (lJanRegistro)   	fecharEDICAO();  else {e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='inicial.php';}}    if  (tecla==13 && lJanAuxilio)   	usouAUXILIO();  if (lJanRegistro)  eval("teclasNavegacao(e);");if (! lJanRegistro && !lJanAuxilio ) {}if  ( tecla==113 && lJanRegistro ) document.getElementById('btnGRAVAR').click();if  ( tecla==119 && lJanRegistro && ! document.getElementById('txtTIPO').readOnly)  AuxilioF7(cfoco); }if (window.document.addEventListener)  window.document.addEventListener("keydown", teclado, false);else  window.document.attachEvent("onkeydown", teclado);/*******************************************************************************/function fecharEDICAO()     {document.getElementById("divEDICAO").innerHTML='';document.getElementById("divEDICAO").setAttribute(propCLASSE, "cssDIV_ESCONDE");ColocaFocoCmpInicial();}/*******************************************************************************/function ColocaFocoCmpInicial(cmp)   {lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO';var lJanAuxilio = document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';if (cmp!=null) 	document.getElementById(cmp).focus();else if (lJanAuxilio)   document.getElementById('txtPR2').focus();	else if (lJanRegistro )	document.getElementById('txtTIPO').focus();else   document.getElementById('txtFOCADO').focus();  	}	/*******************************************************************************/function lerREGS( avancarDATA ) {if ( typeof(avancarDATA)=='undefined' )  avancarDATA=0;showAJAX(1);document.getElementById("divTABELA").innerHTML = '';document.getElementById("SELECAO").value='';data = document.getElementById('dataTRAB').value;var data2 = new Date(parseInt(data.substring(4, 10),10), parseInt(data.substring(2, 4),10)-1, parseInt(data.substring(0, 2),10));data2.addDays(avancarDATA);var dia=data2.getDate(); if (dia.toString().length<2) dia = '0'+dia;var mes=data2.getMonth()+1; if (mes.toString().length<2) mes = '0'+mes;document.getElementById('dataTRAB').value = dia+''+mes+''+data2.getFullYear();dataLER = data2.getFullYear()+''+mes+''+dia; ajax.criar('ajax/ajaxCREDITOS.php?acao=lerREGS&vlr='+  dataLER+'&vlr2='+avancarDATA+'&lendoATUAL='+document.getElementById('lendoATUAL').value , desenhaTabela);}/*******************************************************************************/function desenhaTabela() {if ( ajax.terminouLER() ) {  aRESP = ajax.ler().split('|');    document.getElementById("SELECAO").value='';  document.getElementById("titTABELA").innerHTML = aRESP[0];  document.getElementById("divTABELA").scrollTop=0;  document.getElementById("divTABELA").innerHTML = aRESP[1].split('^')[0];    var cmpPESQUISA = '';  var totPAGAR = '';  var infoREGS = aRESP[1].split('^')[1];  if (infoREGS.indexOf('-')!=-1) {    qtdeREGS = infoREGS.split('-')[0];    tipoCreditoLendo = infoREGS.split('-')[1];    totPAGAR=infoREGS.split('-')[2];       }   else {     qtdeREGS = infoREGS;    var cmpPESQUISA = aRESP[1].split('^')[2];    totPAGAR = aRESP[1].split('^')[4];  }      showAJAX(0);    centerDiv( 'divEDICAO' ); centerDiv( 'divCAIXA' );  VerificaAcaoInicial();    var titulo=document.getElementById('lblTITULO');  if (document.getElementById('lendoATUAL').value=='valecreditos')      titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">Vale cr�ditos</font>&nbsp;&nbsp;&nbsp;&nbsp;';  else if (document.getElementById('lendoATUAL').value=='creditos')    titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">Cr�ditos e d�bitos</font>&nbsp;&nbsp;&nbsp;&nbsp;';  else if (document.getElementById('lendoATUAL').value=='valecreditospendentes')    titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">Vale cr�ditos pendentes</font>&nbsp;&nbsp;&nbsp;&nbsp;';  else if (document.getElementById('lendoATUAL').value=='valecreditos45')    titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">Vale cr�ditos registrados nos �ltimos 45 dias</font>&nbsp;&nbsp;&nbsp;&nbsp;';  else if (document.getElementById('lendoATUAL').value=='creditos45')    titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">Cr�ditos/d�bitos registrados nos �ltimos 45 dias</font>&nbsp;&nbsp;&nbsp;&nbsp;';  else     titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">Cr�ditos,descontos,vl cr�ditos </font>&nbsp;&nbsp;&nbsp;&nbsp;';  data=document.getElementById("dataTRAB").value;  dataLER = data.substring(0, 2)+'/'+data.substring(2, 4)+'/'+data.substring(6, 10);  if ( document.getElementById('lendoATUAL').value=='valecreditospendentes' || document.getElementById('lendoATUAL').value=='valecreditos45'        || document.getElementById('lendoATUAL').value=='creditos45' )    {    //}  else if (cmpPESQUISA=='')     titulo.innerHTML += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+(tipoCreditoLendo=='1' ? 'Registrados em: ' : 'Para pagar em: ') + dataLER ;   else     titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">Cr�ditos e d�bitos/vale cr�ditos </font>&nbsp;&nbsp;&nbsp;&nbsp;'+cmpPESQUISA;  document.getElementById("totREGS").innerHTML = 'Filtrados: &nbsp;&nbsp;'+qtdeREGS + '&nbsp;&nbsp;';  document.getElementById("lblPAGAR").innerHTML = totPAGAR;}}/*******************************************************************************/function incluirREG() {showAJAX(1);ajax.criar('ajax/ajaxCREDITOS.php?acao=incluirREG', desenhaJanelaREG);}/*******************************************************************************/function desenhaJanelaREG()     {if ( ajax.terminouLER() ) {  var divEDICAO = document.getElementById('divEDICAO');    divEDICAO.setAttribute(propCLASSE, 'cssDIV_EDICAO');    divEDICAO.innerHTML = ajax.ler();  /* esconde checkbox PAGO se nao e vale credito */   if (document.getElementById('numVALE_CREDITO').value=='' || document.getElementById('numVALE_CREDITO').value=='0')         document.getElementById('trPAGO').style.display='none';    Muda_CSS();     showAJAX(0);  ColocaFocoCmpInicial();}}  /*******************************************************************************/function editarREG() {id = document.getElementById('SELECAO').value if (id=='') { alert('Selecione um registro');return;}	showAJAX(1);ajax.criar('ajax/ajaxCREDITOS.php?acao=editarREG&vlr=' + id, desenhaJanelaREG);}/*******************************************************************************/function VerCmp(nomeCMP)      {lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO';if (! lJanRegistro)  return;if (nomeCMP!='todos')    document.getElementById(nomeCMP).style.backgroundColor="#F6F7F7";if (nomeCMP!='todos')         {  vlr = document.getElementById(nomeCMP).value;	switch (nomeCMP) {		case 'txtREPRESENTANTE':	  cmpLBL = document.getElementById( nomeCMP.replace('txt', 'lbl') );		  		  /* deixou vlr em branco */      if (vlr.rtrim().ltrim()=='') {cmpLBL.innerHTML = '';return true;}						cmpLBL.innerHTML = 'lendo...';			ajax.criar('ajax/ajaxAUXILIO.php?oQueAuxiliar=pesquisa_' + nomeCMP + '&vlr=' +vlr, '', 0);			      aRESP = ajax.ler().split(';');      	      cIDCMP = aRESP[0]; 	cVLR = aRESP[1];      cIDCMP=cIDCMP.rtrim().ltrim();    	    	if (cVLR.indexOf('ERRO')!=-1) cmpLBL.style.color='red';    	else cmpLBL.style.color='blue';            	cmpLBL.innerHTML = cVLR;			break;		case 'txtTIPO':      vlr=vlr.toUpperCase();      document.getElementById('txtREPRESENTANTE').focus();      break;	}	return;}else  {	for (i=0;i<aCMPS.length;i++)   {		cmp = aCMPS[i].split(';');		cCMP = cmp[0]; 		cMSG = cmp[1];		cVLR = document.getElementById(cCMP).value.rtrim().ltrim();    var label = cCMP.replace('txt', 'lbl'); 								erro=0;		switch (cCMP)   {			case 'txtDESCRICAO':        if ( cVLR=='' ) erro=1;        break;        			case 'txtVALOR':        if ( cVLR=='' ) erro=1;        break;                			case 'txtTIPO':        cVLR = cVLR.toUpperCase();        break;        			case 'txtDATA':			case 'txtDATAPAGAR':        if ( cVLR=='' || ! verifica_data('txtDATA') )   erro=1;        break;        			case 'txtREPRESENTANTE':        			         if ( document.getElementById(label).innerHTML=='' ||            document.getElementById(label).innerHTML.indexOf('ERRO')!=-1) erro=1;        break; 		}	 if (erro==1) {alert(cMSG);	document.getElementById(cCMP).focus(); return false;}		}	  /* tudo ok, aciona gravacao do registro, concatena campos em uma variavel */  var vlr = document.getElementById('txtVALOR').value.replace(',','.');  var data = document.getElementById('txtDATA').value;  var dataGRAVAR='null';  if (data.rtrim().ltrim()!='')     dataGRAVAR = data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);    var dataPAGAR = document.getElementById('txtDATAPAGAR').value;  var dataGRAVAR2='null';  if (dataPAGAR.rtrim().ltrim()!='')     dataGRAVAR2 = dataPAGAR.substring(6, 10)+'-'+dataPAGAR.substring(3, 5)+'-'+dataPAGAR.substring(0, 2);    cmps= dataGRAVAR+'|'+document.getElementById('txtTIPO').value.toUpperCase()+'|'+        document.getElementById('txtDESCRICAO').value+'|'+document.getElementById('txtREPRESENTANTE').value+ '|' +        vlr + '|' +        document.getElementById('numREG').value+'|'+        dataGRAVAR2;  /* verifica se ha conta: entrega de proposta definida */	showAJAX(1);	ajax.criar('ajax/ajaxCAIXA.php?acao=verificaCONTAVALECREDITO', '' , 0);  showAJAX(0);  if (ajax.ler()=='NAO') {    alert('N�o h� conta de pagamento de vale cr�dito definida'); return;  }  idCONTA=ajax.ler();  showAJAX(1);	ajax.criar('ajax/ajaxCREDITOS.php?acao=gravar&vlr=' + cmps+                 '&numVALECRED='+document.getElementById('numVALE_CREDITO').value+'&pago='+document.forms[0].chkPAGO.checked+                '&opCaixaPagamento='+document.getElementById('opCaixaPagamento').value+                '&idCONTA='+idCONTA+                '&saida='+document.forms[0].chkSAIDA.checked, gravou);}}/*******************************************************************************/function gravou() {if ( ajax.terminouLER() ) {  showAJAX(0);    resp = ajax.ler();    fecharEDICAO();    if (resp.indexOf('OK')!=-1)   {    document.getElementById('SELECAO').value="";    	cID = resp.substring(resp.indexOf(';')+1);  	window.top.document.getElementById('infoTrab').value = 'frmCREDITOS:GRAVOU=' + cID  	lerREGS();  }  else  	alert('Erro ao gravar: \n\n ' + resp);	}}/*******************************************************************************/function excluirREG() {id = document.getElementById('SELECAO').value if (id=='') { alert('Selecione um registro');return;}alert('Para excluir uma cr�dito/d�bito, voc� precisa da senha do administrador (usu�rio n�mero 1)');var senha=prompt('Senha:','')if (senha==null) return;if (senha.rtrim()=='') return;showAJAX(1);ajax.criar('ajax/ajaxCREDITOS.php?acao=senhaEXCLUIR&vlr='+senha, '', 0);showAJAX(0);if (ajax.ler()=='nao') {  alert('Senha do administrador incorreta');  return;}var tab=document.getElementById('tabREGs');for (var t=0; t<tab.rows.length; t++) { if (tab.rows[t].id==document.getElementById('SELECAO').value) {   var desc=tab.rows[t].cells[4].innerHTML;   if (desc.indexOf('VALE CR�DITO N�')>-1 || desc.indexOf('PROPOSTA(S) PAGA(S) COM')>-1          || desc.indexOf('ADIANTAMENTO SALARIAL')>-1  || desc.indexOf('ADIANTAMENTO DE COMISS�O')>-1) {     alert('Este registro est� vinculado � uma opera��o do caixa\n\ne s� pode ser alterado atrav�s da respectiva opera��o');return;   }   break; }  }if (! confirm('Excluir este registro?')) return;ajax.criar('ajax/ajaxCREDITOS.php?acao=excluir&vlr=' + id, '', 0);if (ajax.ler().indexOf('ok')==-1)   alert('Erro ao excluir!!! \n\n' + ajax.ler());  lerREGS();  }/*******************************************************************************/function buscar() {var palavra=prompt('Digite uma palavra (descri��o da opera��o) ou um n�mero de vale cr�dito para procurar:','');if (palavra==null) return;if (palavra.rtrim()=='') return;showAJAX(1);ajax.criar('ajax/ajaxCREDITOS.php?acao=lerREGS&vlr=palavra'+palavra, desenhaTabela); }/********************************************************************************/function checar()  {rdBUTTON = document.forms['frmCREDITOS'].chkPAGO;if (rdBUTTON.checked && document.getElementById('lblOperacoesPGTO').innerHTML!='-' && document.getElementById('lblOperacoesPGTO').innerHTML!='')  {  alert('Este vale cr�dito j� come�ou a ser pago em parcelas\n\n'+        'Para efetuar um pagamento �nico, primeiro exclua (no caixa) os pagamentos \n\nparciais deste vale cr�dito.\n\n');  return;} rdBUTTON.checked = rdBUTTON.checked ? false : true;document.getElementById('txtTIPO').focus();}/********************************************************************************/function checar2()  {rdBUTTON = document.forms['frmCREDITOS'].chkSAIDA;rdBUTTON.checked = rdBUTTON.checked ? false : true;document.getElementById('txtTIPO').focus();}/*******************************************************************************/function rel() {window.top.frames['framePRINCIPAL'].location.href='rel/creditos.php';}/*******************************************************************************/function opCAIXA(idOP) {showAJAX(1);ajax.criar('ajax/ajaxCAIXA.php?acao=opCAIXA_RESUMIDA&vlr=' + idOP, '', 0);showAJAX(0);divCAIXA=document.getElementById('divCAIXA');var entregaPROP = ajax.ler().split('^')[1];if (entregaPROP==1)    divCAIXA.setAttribute(propCLASSE, 'cssDIV_EDICAO_ENT');    else  divCAIXA.setAttribute(propCLASSE, 'cssDIV_EDICAO_CAIXA');divCAIXA.innerHTML = ajax.ler().split('^')[0]; }/*******************************************************************************/function fecharCAIXA() {document.getElementById("divCAIXA").innerHTML='';document.getElementById("divCAIXA").setAttribute(propCLASSE, "cssDIV_ESCONDE");ColocaFocoCmpInicial();}//]]></script>  </body></html>