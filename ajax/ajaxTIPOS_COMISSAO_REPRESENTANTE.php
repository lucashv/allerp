<?
header("Content-Type: text/html; charset=iso-8859-1");


session_start();

require_once( '../includes/definicoes.php'  );
require_once( '../includes/funcoes.php'  );

$acao = $_REQUEST['acao'];
if (isset( $_REQUEST['vlr']))   $vlr = $_REQUEST['vlr'];


/* conexao */
$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());

$resp = 'INEXISTENTE';


/*****************************************************************************************/
if ($acao=='lerCOMISSAO') {
  $sql  = "select nome " .
          "from tipos_comissao ".
          "where numreg=$vlr ";
          
  $resultado = mysql_query($sql) or die (mysql_error());
  if (mysql_num_rows($resultado)>0) {  
    $row = mysql_fetcH_object($resultado);
    $resp=$row->nome;
  }
  else 
    $resp='ERRO';
}

  
        



/*****************************************************************************************/
if ($acao=='duplicar') {
  $idCOMISSAO = $_REQUEST['vlr'];
  $novoESQUEMA = $_REQUEST['novo'];
  
  $sql = "insert into tipos_comissao(nome, ativo) values('$novoESQUEMA', 'S')";
  mysql_query($sql);
  if (mysql_affected_rows()==-1) 
    die( mysql_error());
  else   
    $idNOVO = mysql_insert_id();

  // duplica todos os valores configurados na comissao idCOMISSAo, para a comissao recem criada, idNOVO
  $sql = "insert into comissoes_representante " .
         "(p1a,p2a,p3a,p4a,p5a,adesao,pVITALICIA,interno_externo,idPRODUTO, comissionamentoPorVidas, idCOMISSAO) " .
         "select p1a,p2a,p3a,p4a,p5a,adesao,pVITALICIA, interno_externo,idPRODUTO, comissionamentoPorVidas, $idNOVO " .
         "from comissoes_representante ".
         " where idcomissao=$idCOMISSAO " ;

  mysql_query($sql) or die(mysql_error()); 
  $resp = 'OK;INC_' . $idNOVO;
}  

/*****************************************************************************************/
if ($acao=='padrao') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("update configuracao set idComiPadraoRepresentante=$vlr ;") or  die (mysql_error());  
  
  $resp='ok';  
}          


/*****************************************************************************************/
if ($acao=='editarCOMISSAO') {
  $idPRODUTO = $_REQUEST['prod'];
  $interno_externo = $_REQUEST['qual'];
  $parcela = $_REQUEST['parcela'];    
  $vlr = $_REQUEST['vlr'];  
  $idCOMISSAO = $_REQUEST['idCOMISSAO'];
  $comissaoPorQtdeVidas = $_REQUEST['comiQtdeVidas'];  
              
  $sql="select numreg from comissoes_representante where idCOMISSAO=$idCOMISSAO and idPRODUTO=$idPRODUTO and interno_externo=$interno_externo ".
        " and comissionamentoPorVidas=$comissaoPorQtdeVidas ";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  if ( mysql_num_rows($resultado)==0 ) 
    mysql_query("insert into comissoes_representante(idCOMISSAO, idPRODUTO, interno_externo, $parcela, comissionamentoPorVidas) ".
                " select $idCOMISSAO, $idPRODUTO, $interno_externo, $vlr, $comissaoPorQtdeVidas") or  die (mysql_error());
  else {
    $row = mysql_fetcH_object($resultado);
    $numreg = $row->numreg;
    
    mysql_query("update comissoes_representante set $parcela = $vlr where numreg=$numreg;") or  die (mysql_error());
  }  
}    

/*****************************************************************************************/
if ($acao=='valores') {
  $interno_externo = $_REQUEST['qual']; // 1= interno      2= externo
  $mostrarQUAL = '<font color=blue><b>'.
                  ( ($interno_externo=='1') ? 'REPRESENTANTES INTERNOS' : 'REPRESENTANTES EXTERNOS' ) .
                  '</font></b>';  
  $descricao = $_REQUEST['desc'];   

	$resp = '<table class=frmJANELA border=1 width="100%" cellpadding=3 >' .
					'<tr><td><table width="100%" > ' .
					'	<tr width="100%" >' .
					"		<td style=\"width:90%\" style=\"cursor: move;\"><span class=lblTitJanela id=tituloVALORES>Tipo de comissão: $descricao ($mostrarQUAL)</span></td>" .
					'		<td><span onclick="fecharVALORES()" style="cursor: pointer;" class=lblTitJanela>[ X ]</td>' .
					'	</tr></table>' .
					'</td</tr>' .                
          '<tr >' .
          ' <td valign="top"  height="450px">' .
          '   <div id="titVALORES">@titVALORES</div>' .
          '   <div id="divVALORES" style="overflow:auto;min-height:92%;height:92%">@divVALORES</div>' .
          ' </td>' .
          '</tr>' .
					'</table>';
					
  $sql  = "select prod.numreg as idPRODUTO, prod.descricao, ifnull(op.nome, '') as nomeOPERADORA, prod.idOPERADORA, prod.ativo,  ".
          "  comi1.numreg as idCOMISSAO, ".
          "ifnull(comi1.p1a, '-') as p1a_1, ifnull(comi1.p2a, '-') as p2a_1, ifnull(comi1.p3a, '-') as p3a_1, ifnull(comi1.p4a, '-') as p4a_1, ". 
          " ifnull(comi1.p5a, '-') as p5a_1, ifnull(comi1.adesao, '-') as adesao_1, ifnull(comi1.pVITALICIA, '-') as pVITALICIA_1, ".
          "ifnull(comi2.p1a, '-') as p1a_2, ifnull(comi2.p2a, '-') as p2a_2, ifnull(comi2.p3a, '-') as p3a_2, ifnull(comi2.p4a, '-') as p4a_2, ". 
          " ifnull(comi2.p5a, '-') as p5a_2, ifnull(comi2.adesao, '-') as adesao_2, ifnull(comi2.pVITALICIA, '-') as pVITALICIA_2, ".
          "ifnull(comi3.p1a, '-') as p1a_3, ifnull(comi3.p2a, '-') as p2a_3, ifnull(comi3.p3a, '-') as p3a_3, ifnull(comi3.p4a, '-') as p4a_3, ". 
          " ifnull(comi3.p5a, '-') as p5a_3, ifnull(comi3.adesao, '-') as adesao_3, ifnull(comi3.pVITALICIA, '-') as pVITALICIA_3, ".
          "ifnull(comi4.p1a, '-') as p1a_4, ifnull(comi4.p2a, '-') as p2a_4, ifnull(comi4.p3a, '-') as p3a_4, ifnull(comi4.p4a, '-') as p4a_4, ". 
          " ifnull(comi4.p5a, '-') as p5a_4, ifnull(comi4.adesao, '-') as adesao_4, ifnull(comi4.pVITALICIA, '-') as pVITALICIA_4, ".
          "ifnull(comi5.p1a, '-') as p1a_5, ifnull(comi5.p2a, '-') as p2a_5, ifnull(comi5.p3a, '-') as p3a_5, ifnull(comi5.p4a, '-') as p4a_5, ". 
          " ifnull(comi5.p5a, '-') as p5a_5, ifnull(comi5.adesao, '-') as adesao_5, ifnull(comi5.pVITALICIA, '-') as pVITALICIA_5, ".
          " prod.vidas1, prod.vidas2, prod.vidas3, prod.vidas4, prod.vidas5, prod.vidas6, prod.vidas7, prod.vidas8, prod.vidas9, ".
          " prod.vidas10 ".  
          "from tipos_contrato prod ".
          "left join operadoras op ".
          '   on op.numreg=prod.idOPERADORA '.
          "left join comissoes_representante comi1 " .
          "    on  comi1.idCOMISSAO=$vlr and comi1.idPRODUTO=prod.numreg and comi1.interno_externo=$interno_externo and comi1.comissionamentoPorVidas=1 ".
          "left join comissoes_representante comi2 " .
          "    on  comi2.idCOMISSAO=$vlr and comi2.idPRODUTO=prod.numreg and comi2.interno_externo=$interno_externo and comi2.comissionamentoPorVidas=2  ".
          "left join comissoes_representante comi3 " .
          "    on  comi3.idCOMISSAO=$vlr and comi3.idPRODUTO=prod.numreg and comi3.interno_externo=$interno_externo and comi3.comissionamentoPorVidas=3  ".
          "left join comissoes_representante comi4 " .
          "    on  comi4.idCOMISSAO=$vlr and comi4.idPRODUTO=prod.numreg and comi4.interno_externo=$interno_externo and comi4.comissionamentoPorVidas=4  ".
          "left join comissoes_representante comi5 " .
          "    on  comi5.idCOMISSAO=$vlr and comi5.idPRODUTO=prod.numreg and comi5.interno_externo=$interno_externo and comi5.comissionamentoPorVidas=5  ".
          " where ifnull(prod.ativo,'')='S' ".     
          " order by op.nome, prod.descricao " ;

  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.3;
  $largura3 = $_SESSION['largIFRAME'] * 0.10;   // adesao 
  $largura4 = $_SESSION['largIFRAME'] * 0.1;   // 1a
  $largura5 = $_SESSION['largIFRAME'] * 0.1;   // 2a
  $largura6 = $_SESSION['largIFRAME'] * 0.1;   // 3a
  $largura7 = $_SESSION['largIFRAME'] * 0.1;   // 4a
  $largura8 = $_SESSION['largIFRAME'] * 0.1;   // 5a
  $largura9 = $_SESSION['largIFRAME'] * 0.1;   // vitalícia  
    
	$header = "$largura1 px,Produto|$largura4 px,&nbsp;&nbsp;&nbsp;&nbsp;_RIGHT_1ª|$largura5 px,&nbsp;&nbsp;&nbsp;&nbsp;_RIGHT_2ª|$largura6 px,&nbsp;&nbsp;&nbsp;&nbsp;_RIGHT_3ª".
	         "|$largura7 px,&nbsp;&nbsp;&nbsp;&nbsp;_RIGHT_4ª|$largura8 px,&nbsp;&nbsp;&nbsp;&nbsp;_RIGHT_5ª|$largura9 px,Vitalícia";

//	$header = "$largura1 px,Produto|1%,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;_RIGHT_A2desão|$largura4 px,&nbsp;&nbsp;&nbsp;&nbsp;_RIGHT_1ª|$largura5 px,&nbsp;&nbsp;&nbsp;&nbsp;_RIGHT_2ª|$largura6 px,&nbsp;&nbsp;&nbsp;&nbsp;_RIGHT_3ª".
//	         "|$largura7 px,&nbsp;&nbsp;&nbsp;&nbsp;_RIGHT_4ª|$largura8 px,&nbsp;&nbsp;&nbsp;&nbsp;_RIGHT_5ª|$largura9 px,Vitalícia";
   
  $titVALORES = tabelaPADRAO('width="97%" ', $header ) . '</table>';
  $divVALORES = '<table id="tabVALORES" width="97%" cellpadding=3  cellspacing=0 style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $i=0;
  while ($row = mysql_fetcH_object($resultado)) {
//    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
      $largura3="width=\"$largura3 px\"";      
      $largura4="width=\"$largura4 px\"";      
      $largura5="width=\"$largura5 px\"";      
      $largura6="width=\"$largura6 px\"";      
      $largura7="width=\"$largura7 px\"";      
      $largura8="width=\"$largura8 px\"";      
      $largura9="width=\"$largura9 px\"";      
//    } else {    
//      $largura1='';$largura3='';$largura4='';$largura5='';$largura6='';$largura7='';$largura8='';$largura9='';
//    }
  
    
    if ( ($row->vidas1>0 && $row->vidas2>0) ||
         ($row->vidas3>0 && $row->vidas4>0) ||
         ($row->vidas5>0 && $row->vidas6>0) ||
         ($row->vidas7>0 && $row->vidas8>0) ||
         ($row->vidas9>0 && $row->vidas10>0) ) {                           
      $lin = "<tr @cor onmousedown=\"Selecionar(this.id);\" onmouseover=\"this.style.cursor='default';" .  
    	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
              "<td align=\"left\" $largura1>$row->nomeOPERADORA - $row->descricao (<font color=blue><b>$row->idPRODUTO</font></b>)</td>".
              "<td colspan=7>&nbsp;</td>".            
              "</tr>";
      $i++;
      if ($i % 2==0) {
        $lin = str_replace('@cor', "bgcolor=lightgrey", $lin);
        $cor='lightgrey';
      } 
      else {
        $lin = str_replace('@cor', "bgcolor='#F6F7F7'", $lin);
        $cor='#F6F7F7';
      }
      $divVALORES .= $lin;
    }
    else 
      continue;                  

    
    //****************************************************************************
    // 1o comissionamento baseado na qtde de vidas
    //****************************************************************************
    if ($row->vidas1>0 && $row->vidas2>0) {
      $p1a=($row->p1a_1==0) ? '-' : "$row->p1a_1%";
      $p2a=($row->p2a_1==0) ? '-' : "$row->p2a_1%";    
      $p3a=($row->p3a_1==0) ? '-' : "$row->p3a_1%";    
      $p4a=($row->p4a_1==0) ? '-' : "$row->p4a_1%";    
      $p5a=($row->p5a_1==0) ? '-' : "$row->p5a_1%";
      $adesao=($row->adesao_1==0) ? '-' : "$row->adesao_1%";
      $pVITALICIA=($row->pVITALICIA_1==0) ? '-' : "$row->pVITALICIA_1%";        
      
//              "<td align=\"right\"  $largura8 @mouse6>$adesao</td>".            

      $lin = "<tr @cor onmousedown=\"Selecionar(this.id);\" onmouseover=\"this.style.cursor='default';" .  
    	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
              "<td align=\"left\" $largura1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Qtde vidas entre $row->vidas1 e $row->vidas2</td>".
              "<td align=\"right\" $largura3 @mouse1>$p1a</td>".
              "<td align=\"right\" $largura4 @mouse2>$p2a</td>".                        
              "<td align=\"right\" $largura5 @mouse3>$p3a</td>".            
              "<td align=\"right\" $largura6 @mouse4>$p4a</td>".            
              "<td align=\"right\" $largura7 @mouse5>$p5a</td>".            
              "<td align=\"right\" $largura9 @mouse7>$pVITALICIA</td>".            
              "</tr>";
  
      if ($i % 2==0) {
        $lin = str_replace('@cor', "bgcolor=lightgrey", $lin);
        $cor='lightgrey';
      } 
      else {
        $lin = str_replace('@cor', "bgcolor='#F6F7F7'", $lin);
        $cor='#F6F7F7';
      }
              
      $lin = str_replace('@mouse1', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 1, 'p1a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);
      $lin = str_replace('@mouse2', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 1, 'p2a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);
      $lin = str_replace('@mouse3', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 1, 'p3a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);        
      $lin = str_replace('@mouse4', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 1, 'p4a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
      $lin = str_replace('@mouse5', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 1, 'p5a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
      $lin = str_replace('@mouse6', "style='display:none;cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 1, 'adesao')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
      $lin = str_replace('@mouse7', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 1, 'pvitalicia')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
              
      $divVALORES .= $lin;
    }
    
    //****************************************************************************
    // 2o comissionamento baseado na qtde de vidas
    //****************************************************************************
    if ($row->vidas3>0 && $row->vidas4>0) {
      $p1a=($row->p1a_2==0) ? '-' : "$row->p1a_2%";
      $p2a=($row->p2a_2==0) ? '-' : "$row->p2a_2%";    
      $p3a=($row->p3a_2==0) ? '-' : "$row->p3a_2%";    
      $p4a=($row->p4a_2==0) ? '-' : "$row->p4a_2%";    
      $p5a=($row->p5a_2==0) ? '-' : "$row->p5a_2%";
      $adesao=($row->adesao_2==0) ? '-' : "$row->adesao_2%";
      $pVITALICIA=($row->pVITALICIA_2==0) ? '-' : "$row->pVITALICIA_2%";        
      
      $lin = "<tr @cor onmousedown=\"Selecionar(this.id);\" onmouseover=\"this.style.cursor='default';" .  
    	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
              "<td align=\"left\" $largura1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Qtde vidas entre $row->vidas3 - $row->vidas4</td>".
              "<td align=\"right\" $largura3 @mouse1>$p1a</td>".
              "<td align=\"right\" $largura4 @mouse2>$p2a</td>".                        
              "<td align=\"right\" $largura5 @mouse3>$p3a</td>".            
              "<td align=\"right\" $largura6 @mouse4>$p4a</td>".            
              "<td align=\"right\" $largura7 @mouse5>$p5a</td>".            
              "<td align=\"right\" $largura9 @mouse7>$pVITALICIA</td>".            
              "</tr>";
  
      if ($i % 2==0) {
        $lin = str_replace('@cor', "bgcolor=lightgrey", $lin);
        $cor='lightgrey';
      } 
      else {
        $lin = str_replace('@cor', "bgcolor='#F6F7F7'", $lin);
        $cor='#F6F7F7';
      }
              
      $lin = str_replace('@mouse1', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 2, 'p1a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);
      $lin = str_replace('@mouse2', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 2, 'p2a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);
      $lin = str_replace('@mouse3', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 2, 'p3a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);        
      $lin = str_replace('@mouse4', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 2, 'p4a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
      $lin = str_replace('@mouse5', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 2, 'p5a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
      $lin = str_replace('@mouse6', "style='display:none;cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 2, 'adesao')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
      $lin = str_replace('@mouse7', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 2, 'pvitalicia')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
              
      $divVALORES .= $lin;
    }
    


    //****************************************************************************
    // 3o comissionamento baseado na qtde de vidas
    //****************************************************************************
    if ($row->vidas5>0 && $row->vidas6>0) {
      $p1a=($row->p1a_3==0) ? '-' : "$row->p1a_3%";
      $p2a=($row->p2a_3==0) ? '-' : "$row->p2a_3%";    
      $p3a=($row->p3a_3==0) ? '-' : "$row->p3a_3%";    
      $p4a=($row->p4a_3==0) ? '-' : "$row->p4a_3%";    
      $p5a=($row->p5a_3==0) ? '-' : "$row->p5a_3%";
      $adesao=($row->adesao_3==0) ? '-' : "$row->adesao_3%";
      $pVITALICIA=($row->pVITALICIA_3==0) ? '-' : "$row->pVITALICIA_3%";        
      
      $lin = "<tr @cor onmousedown=\"Selecionar(this.id);\" onmouseover=\"this.style.cursor='default';" .  
    	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
              "<td align=\"left\"  $largura1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Qtde vidas entre $row->vidas5 - $row->vidas6</td>".
              "<td align=\"right\" $largura3 @mouse1>$p1a</td>".
              "<td align=\"right\" $largura4 @mouse2>$p2a</td>".                        
              "<td align=\"right\" $largura5 @mouse3>$p3a</td>".            
              "<td align=\"right\" $largura6 @mouse4>$p4a</td>".            
              "<td align=\"right\" $largura7 @mouse5>$p5a</td>".            
              "<td align=\"right\" $largura9 @mouse7>$pVITALICIA</td>".            
              "</tr>";
  
      if ($i % 2==0) {
        $lin = str_replace('@cor', "bgcolor=lightgrey", $lin);
        $cor='lightgrey';
      } 
      else {
        $lin = str_replace('@cor', "bgcolor='#F6F7F7'", $lin);
        $cor='#F6F7F7';
      }
              
      $lin = str_replace('@mouse1', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 3, 'p1a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);
      $lin = str_replace('@mouse2', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 3, 'p2a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);
      $lin = str_replace('@mouse3', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 3, 'p3a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);        
      $lin = str_replace('@mouse4', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 3, 'p4a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
      $lin = str_replace('@mouse5', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 3, 'p5a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
      $lin = str_replace('@mouse6', "style='display:none;cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 3, 'adesao')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
      $lin = str_replace('@mouse7', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 3, 'pvitalicia')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
              
      $divVALORES .= $lin;
    }      
          
          
    //****************************************************************************
    // 4o comissionamento baseado na qtde de vidas
    //****************************************************************************
    if ($row->vidas7>0 && $row->vidas8>0) {
      $p1a=($row->p1a_4==0) ? '-' : "$row->p1a_4%";
      $p2a=($row->p2a_4==0) ? '-' : "$row->p2a_4%";    
      $p3a=($row->p3a_4==0) ? '-' : "$row->p3a_4%";    
      $p4a=($row->p4a_4==0) ? '-' : "$row->p4a_4%";    
      $p5a=($row->p5a_4==0) ? '-' : "$row->p5a_4%";
      $adesao=($row->adesao_4==0) ? '-' : "$row->adesao_4%";
      $pVITALICIA=($row->pVITALICIA_4==0) ? '-' : "$row->pVITALICIA_4%";        
      
      $lin = "<tr @cor onmousedown=\"Selecionar(this.id);\" onmouseover=\"this.style.cursor='default';" .  
    	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
              "<td align=\"left\" $largura1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Qtde vidas entre $row->vidas7 - $row->vidas8</td>".
              "<td align=\"right\" $largura3 @mouse1>$p1a</td>".
              "<td align=\"right\" $largura4 @mouse2>$p2a</td>".                        
              "<td align=\"right\" $largura5 @mouse3>$p3a</td>".            
              "<td align=\"right\" $largura6 @mouse4>$p4a</td>".            
              "<td align=\"right\" $largura7 @mouse5>$p5a</td>".            
              "<td align=\"right\" $largura9 @mouse7>$pVITALICIA</td>".            
              "</tr>";
  
      if ($i % 2==0) {
        $lin = str_replace('@cor', "bgcolor=lightgrey", $lin);
        $cor='lightgrey';
      } 
      else {
        $lin = str_replace('@cor', "bgcolor='#F6F7F7'", $lin);
        $cor='#F6F7F7';
      }
              
      $lin = str_replace('@mouse1', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 4, 'p1a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);
      $lin = str_replace('@mouse2', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 4, 'p2a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);
      $lin = str_replace('@mouse3', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 4, 'p3a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);        
      $lin = str_replace('@mouse4', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 4, 'p4a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
      $lin = str_replace('@mouse5', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 4, 'p5a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
      $lin = str_replace('@mouse6', "style='display:none;cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 4, 'adesao')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
      $lin = str_replace('@mouse7', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 4, 'pvitalicia')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
              
      $divVALORES .= $lin;
    }      

      
    
    
    //****************************************************************************
    // 5o comissionamento baseado na qtde de vidas
    //****************************************************************************
    if ($row->vidas9>0 && $row->vidas10>0) {
      $p1a=($row->p1a_5==0) ? '-' : "$row->p1a_5%";
      $p2a=($row->p2a_5==0) ? '-' : "$row->p2a_5%";    
      $p3a=($row->p3a_5==0) ? '-' : "$row->p3a_5%";    
      $p4a=($row->p4a_5==0) ? '-' : "$row->p4a_5%";    
      $p5a=($row->p5a_5==0) ? '-' : "$row->p5a_5%";
      $adesao=($row->adesao_5==0) ? '-' : "$row->adesao_5%";
      $pVITALICIA=($row->pVITALICIA_5==0) ? '-' : "$row->pVITALICIA_5%";        
      
      $lin = "<tr @cor onmousedown=\"Selecionar(this.id);\" onmouseover=\"this.style.cursor='default';" .  
    	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
              "<td align=\"left\" $largura1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Qtde vidas entre $row->vidas9 - $row->vidas10</td>".
              "<td align=\"right\" $largura3 @mouse1>$p1a</td>".
              "<td align=\"right\" $largura4 @mouse2>$p2a</td>".                        
              "<td align=\"right\" $largura5 @mouse3>$p3a</td>".            
              "<td align=\"right\" $largura6 @mouse4>$p4a</td>".            
              "<td align=\"right\" $largura7 @mouse5>$p5a</td>".            
              "<td align=\"right\" $largura9 @mouse7>$pVITALICIA</td>".            
              "</tr>";
  
      if ($i % 2==0) {
        $lin = str_replace('@cor', "bgcolor=lightgrey", $lin);
        $cor='lightgrey';
      } 
      else {
        $lin = str_replace('@cor', "bgcolor='#F6F7F7'", $lin);
        $cor='#F6F7F7';
      }
              
      $lin = str_replace('@mouse1', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 5, 'p1a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);
      $lin = str_replace('@mouse2', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 5, 'p2a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);
      $lin = str_replace('@mouse3', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 5, 'p3a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);        
      $lin = str_replace('@mouse4', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 5, 'p4a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
      $lin = str_replace('@mouse5', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 5, 'p5a')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
      $lin = str_replace('@mouse6', "style='display:none;cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 5, 'adesao')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
      $lin = str_replace('@mouse7', "style='cursor:pointer;' onclick=\"editaCOMISSAO($row->idPRODUTO,$interno_externo, 5, 'pvitalicia')\" onmouseout=\"this.style.backgroundColor='$cor'\" onmouseover=\"this.style.backgroundColor='#A9B2CA'\"", $lin);    
              
      $divVALORES .= $lin;
    }      
  }
  $resp = str_replace('@titVALORES', $titVALORES, $resp);
  $resp = str_replace('@divVALORES', $divVALORES, $resp);  
}					

 
/*****************************************************************************************/
if ($acao=='mudarSITUACAO') {
  $id = $_REQUEST['vlr'];
  
  mysql_query("update tipos_comissao set ativo=case ativo when 'S' then 'N' else 'S' end where numreg=$id") 
    or  die (mysql_error());
    
  echo('ok'); die();
}    



/*****************************************************************************************/
if ($acao=='gravar') {
  $cmps = explode('|', $_REQUEST['vlr']);
  
  $id = $cmps[1];
  
  if ($id=='') 
    $sql = "insert into tipos_comissao(nome, ativo) values('$cmps[0]', 'S')";
  
  else  
    $sql = "update tipos_comissao set nome='$cmps[0]' where numreg=$id"; 
  
  mysql_query($sql);
  if (mysql_affected_rows()==-1) 
    $resp = mysql_error();

  else   {
    /* busca ultimo ID gerado */
    if ($id=='')
      $resp = 'OK;INC_' . mysql_insert_id();
    else
      $resp = 'OK;' . $id;      
  }
  
  
  mysql_close($conexao);
  echo $resp; die();
}  
           



/*****************************************************************************************/
IF ($acao=='lerREGS') {
  
  $ativos = $_REQUEST['ativos'];
  
  $sql  = "select idComiPadraoRepresentante from configuracao";
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  $row = mysql_fetcH_object($resultado);
  $comiPADRAO=$row->idComiPadraoRepresentante;
  mysql_free_result($resultado);
  
  
  $sql  = "select numreg, nome, ativo ".
          " from tipos_comissao " .
          ($ativos=='S' ? " where ifnull(ativo,'')='S' " : "" ) .    
          ($ativos=='N' ? " where ifnull(ativo,'')<>'S' " : "" ) .          
          "order by nome " ;
  $resultado = mysql_query($sql, $conexao) or die (mysql_error());
  
  $largura1 = $_SESSION['largIFRAME'] * 0.2;
  $largura2 = $_SESSION['largIFRAME'] * 0.8;
    
	$header = "$largura1 px,Número|$largura2 px,Descrição";
   
  $resp = tabelaPADRAO('width="97%" ', $header );
  $resp .= '</table>|<table id="tabREGs" width="97%" cellpadding=3  cellspacing=0 style="font-family:verdana;font-size:10px;color:black;">';									 
  
  $qtdeREGS = mysql_num_rows($resultado);
  
  $i=1;
  while ($row = mysql_fetcH_array($resultado, MYSQL_NUM)) {
    if ($i==1) {
      $largura1="width=\"$largura1 px\"";
      $largura2="width=\"$largura2 px\"";
    } else {    
      $largura1='';$largura2='';
    }
    $i++;
  
    $padrao='';
    if ($comiPADRAO==$row[0]) $padrao='<font color=blue face=arial style="font-size:14px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>** COMISSÃO PADRÃO **</b></font>';

    $lin = "<tr @mudaCOR ondblclick=\"editarREG();\" onmousedown=\"Selecionar(this.id);\" id=\"$row[0]\" onmouseover=\"this.style.cursor='default';" .  
  	  			"MouseSobre(this.id);\" onmouseout=\"MouseFora(this.id);\">" . 
            "<td align=\"left\" $largura1>$row[0]</td>".
            "<td align=\"left\" $largura2>$row[1] $padrao</td>".
            "<td style='display:none'>$row[1]</td>".
            "</tr>";
            
    $lin= str_replace('@mudaCOR', (($row[2]!='S') ? 'style="color:red"' : ''), $lin);
               

    $resp = $resp . ($lin);
  }
  $resp .= '^'.$qtdeREGS;
}


/*****************************************************************************************/
IF ( $acao=='incluirREG' || $acao=='editarREG'  ) {

  $arq = fopen('funcionario.txt', 'r');
  $form = '';
  
  // le codigo html do form
  while(true)       {
  	$lin = fgets($arq);
  	if ($lin == null)  break;
  
    if ($lin != '')  $form = $form . $lin;
  }
  
  $resp = $form;
  fclose($arq);
  
  switch ($acao) { 
    case 'incluirREG':
      $resp=str_replace('TITULO_JANELA', 'Novo Registro',$resp);
      $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);
      $resp=str_replace('readonly', '',$resp);
      break;      
    case 'editarREG':
      $resp=str_replace('TITULO_JANELA', "Editar Registro Nº $vlr ",$resp);    
      $resp=str_replace('texto_botao', '[ F2=gravar ]',$resp);
      $resp=str_replace('readonly', '',$resp);
      break;
  }        
  
  if ($acao!='incluirREG')   {
    $sql  = "select numreg, nome, ativo " .
            "from tipos_comissao rep ".
            "where numreg=$vlr ";
            
    $resultado = mysql_query($sql) or die (mysql_error());  
    $row = mysql_fetcH_object($resultado);
    
    $resp=str_replace('vNOME', $row->nome, $resp);
    $resp=str_replace('@numREG', $vlr, $resp);    
        
  }    
  else {
    $resp=str_replace('vNOME', '', $resp);
    $resp=str_replace('@numREG', '', $resp);    
  }
}

/*****************************************************************************************/
/* fecha conexao */
if ( isset($resultado) )  mysql_free_result($resultado);
mysql_close($conexao);


echo ($resp); die();

?>


