<?php 
    require_once "../vendor/autoload.php";
    use Dompdf\Dompdf;  // composer do DOMPDF
	use Dompdf\Options;
	use Picqer\Barcode\BarcodeGeneratorPNG; // composer do Barcode
    $generator = new BarcodeGeneratorPNG();

	$options = new Options();
	$options->set('isHtml5ParserEnabled', true); // Habilita o parser HTML5
	$options->set('isRemoteEnabled', true); // Permite carregar imagens remotas
	$options->set('defaultFont', 'Arial'); // Define a fonte padrão

	$options->set('margin_left', 8);
	$options->set('margin_right', 8);
	$options->set('margin_top', 2); // Margem superior
	$options->set('margin_bottom', 2);
	$dompdf = new Dompdf($options);

	// chave nota aqui ----------
        $xml = '0000000000000000000000000000000000000000';
        $empresa = substr($xml, 6, 14);
        $empresaAtual = 'empresa';

        $arrayCredenciaisK = array(
            'empresa' => 'TOKENOMIE_KEY',
        );

        $arrayCredenciaisS = array(
            'empresa' => 'TOKENDAOMIE_SECRET',
        );


        $url = 'https://app.omie.com.br/api/v1/produtos/recebimentonfe/';
        $headers = array(
            'Content-type: application/json',
        );

        $data = array(
            'call' => 'ConsultarRecebimento',
            'app_key' => $arrayCredenciaisK[$empresaAtual],
            'app_secret' => $arrayCredenciaisS[$empresaAtual],
            'param' => array(
                array('cChaveNFe' => $xml),
            ),
        );
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
        );
        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        $recebimento = json_decode($response, true);

		$url2 = 'https://app.omie.com.br/api/v1/geral/clientes/';
		$data2 = array(
            'call' => 'ConsultarCliente',
            'app_key' => $arrayCredenciaisK[$empresaAtual],
            'app_secret' => $arrayCredenciaisS[$empresaAtual],
            'param' => array(
                array('codigo_cliente_omie' => $recebimento['cabec']['nIdFornecedor']),
            ),
        );
        $options2 = array(
            CURLOPT_URL => $url2,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data2),
        );
        $curl2 = curl_init();
        curl_setopt_array($curl2, $options2);
        $response2 = curl_exec($curl2);
        $fornecedor = json_decode($response2, true);

		$url3 = 'https://app.omie.com.br/api/v1/geral/empresas/';
		$data3 = array(
            'call' => 'ConsultarEmpresa',
            'app_key' => $arrayCredenciaisK[$empresaAtual],
            'app_secret' => $arrayCredenciaisS[$empresaAtual],
            'param' => array(
                array('codigo_empresa' => '8915906258'),
            ),
        );
        $options3 = array(
            CURLOPT_URL => $url3,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data3),
        );
        $curl3 = curl_init();
        curl_setopt_array($curl3, $options3);
        $response3 = curl_exec($curl3);
        $vita = json_decode($response3, true);

		$cNome = (isset($recebimento['cabec']['cNome']) ? $recebimento['cabec']['cNome'] : '');
        $doc = (isset($recebimento['cabec']['cCNPJ_CPF'] ) ? $recebimento['cabec']['cCNPJ_CPF'] : '');
        $nota = (isset($recebimento['cabec']['cNumeroNFe'] ) ? $recebimento['cabec']['cNumeroNFe'] : '');
        $serie = (isset($recebimento['cabec']['cSerieNFe'] ) ? $recebimento['cabec']['cSerieNFe'] : '');
        $valornf = (isset($recebimento['cabec']['nValorNFe'] ) ? $recebimento['cabec']['nValorNFe'] : '');
        $chavenota = (isset($recebimento['cabec']['cChaveNFe'] ) ? $recebimento['cabec']['cChaveNFe'] : '');
        $dataEmissao = (isset($recebimento['cabec']['dEmissaoNFe'] ) ? $recebimento['cabec']['dEmissaoNFe'] : '');
        $naturezaOp = (isset($recebimento['cabec']['cNaturezaOperacao'] ) ? $recebimento['cabec']['cNaturezaOperacao'] : '');
        $info_dtregistro = (isset($recebimento['infoAdicionais']['dRegistro'] ) ? $recebimento['infoAdicionais']['dRegistro'] : '');
        $qtd_parcela = (isset($recebimento['parcelas']['nQtdParcela'] ) ? $recebimento['parcelas']['nQtdParcela'] : '');
        $parcela_lista = (isset($recebimento['parcelas']['parcelasLista'] ) ? $recebimento['parcelas']['parcelasLista'] : '');
        $baseicms = (isset($recebimento['totais']['bcICMS'] ) ? $recebimento['totais']['bcICMS'] : 0);
        $valoricms = (isset($recebimento['totais']['vICMS'] ) ? $recebimento['totais']['vICMS'] : 0);
        $valorpis = (isset($recebimento['totais']['vTotalPIS'] ) ? $recebimento['totais']['vTotalPIS'] : 0);
        $valortotalprod = (isset($recebimento['totais']['vTotalProdutos'] ) ? $recebimento['totais']['vTotalProdutos'] : 0);
        $valornfe = (isset($recebimento['totais']['vTotalNFe'] ) ? $recebimento['totais']['vTotalNFe'] : 0);
        $valorcofins = (isset($recebimento['totais']['vTotalCOFINS'] ) ? $recebimento['totais']['vTotalCOFINS'] : 0);
        $transp_cnpj = (isset($recebimento['transporte']['cCnpjCpfTransp'] ) ? $recebimento['transporte']['cCnpjCpfTransp'] : '');
        $transp_volume = (isset($recebimento['transporte']['cEspecieVolume'] ) ? $recebimento['transporte']['cEspecieVolume'] : '');
        $transp_razao = (isset($recebimento['transporte']['cNomeTransp'] ) ? $recebimento['transporte']['cNomeTransp'] : '');
        $transp_peso_bruto = (isset($recebimento['transporte']['nPesoBruto'] ) ? $recebimento['transporte']['nPesoBruto'] : '');
        $transp_peso_liquido = (isset($recebimento['transporte']['nPesoLiquido'] ) ? $recebimento['transporte']['nPesoLiquido'] : '');
        $transp_qtd = (isset($recebimento['transporte']['nQtdeVolume'] ) ? $recebimento['transporte']['nQtdeVolume'] : '');
        $transp_tipo = (isset($recebimento['transporte']['cTipoFrete'] ) ? $recebimento['transporte']['cTipoFrete'] : '');
        $transp_tipo_desc = ((isset($recebimento['transporte']['cTipoFrete']) and $recebimento['transporte']['cTipoFrete'] == '0') ? 'Remetente (CIF)' : '');
        $itens = $recebimento['itensRecebimento'];
        $obs = $recebimento['observacoes'];
		
		$for_telefoneddd = (isset($fornecedor['telefone1_ddd'] ) ? $fornecedor['telefone1_ddd'] : '');
		$for_telefone = (isset($fornecedor['telefone1_numero'] ) ? $fornecedor['telefone1_numero'] : '');
		$for_endereco = (isset($fornecedor['endereco'] ) ? $fornecedor['endereco'] : '');
		$for_numero = (isset($fornecedor['endereco_numero'] ) ? $fornecedor['endereco_numero'] : '');
		$for_bairro = (isset($fornecedor['bairro'] ) ? $fornecedor['bairro'] : '');
		$for_estado = (isset($fornecedor['estado'] ) ? $fornecedor['estado'] : '');
		$for_cidade = (isset($fornecedor['cidade'] ) ? $fornecedor['cidade'] : '');
		$for_complemento = (isset($fornecedor['complemento'] ) ? $fornecedor['complemento'] : '');
		$for_inscestadual = (isset($fornecedor['inscricao_estadual'] ) ? $fornecedor['inscricao_estadual'] : '');
		$for_cep = (isset($fornecedor['cep'] ) ? $fornecedor['cep'] : '');
		$for_email = (isset($fornecedor['email'] ) ? $fornecedor['email'] : '');
		$for_nome_fantasia = (isset($fornecedor['nome_fantasia'] ) ? $fornecedor['nome_fantasia'] : '');

		$vita_endereco = (isset($vita['endereco'] ) ? $vita['endereco'] : '');
		$vita_endereco_numero = (isset($vita['endereco_numero'] ) ? $vita['endereco_numero'] : '');
		$vita_complemento = (isset($vita['complemento'] ) ? $vita['complemento'] : '');
		$vita_cidade = (isset($vita['cidade'] ) ? $vita['cidade'] : '');
		$vita_estado = (isset($vita['estado'] ) ? $vita['estado'] : '');
		$vita_cep = (isset($vita['cep'] ) ? $vita['cep'] : '');
		$vita_razao = (isset($vita['razao_social'] ) ? $vita['razao_social'] : '');
		$vita_bairro = (isset($vita['bairro'] ) ? $vita['bairro'] : '');
		$vita_doc = (isset($vita['cnpj'] ) ? $vita['cnpj'] : '');
		$vita_telefone_ddd = (isset($vita['telefone1_ddd'] ) ? $vita['telefone1_ddd'] : '');
		$vita_telefone_numero = (isset($vita['telefone1_numero'] ) ? $vita['telefone1_numero'] : '');
		$vita_ie = (isset($vita['inscricao_estadual'] ) ? $vita['inscricao_estadual'] : '');
		$tpNF = (strstr(strtoupper($naturezaOp), 'VENDA') ? 1 : 0);

		
		function formataTelefone($numero){
			if(strlen($numero) == 10){
				$novo = substr_replace($numero, '(', 0, 0);
				$novo = substr_replace($novo, '9', 3, 0);
				$novo = substr_replace($novo, ') ', 3, 0);
			}else{
				$novo = substr_replace($numero, '(', 0, 0);
				$novo = substr_replace($novo, ') ', 3, 0);
			}
			return $novo;
		}

		function formatarDocumento($documento) {
			// Remove todos os caracteres que não sejam números
			$documento = preg_replace('/\D/', '', $documento);
			
			// Verifica se é CPF (11 dígitos)
			if (strlen($documento) == 11) {
				// Formata o CPF: XXX.XXX.XXX-XX
				return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $documento);
			}
			// Verifica se é CNPJ (14 dígitos)
			elseif (strlen($documento) == 14) {
				// Formata o CNPJ: XX.XXX.XXX/XXXX-XX
				return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $documento);
			}
			// Se não for CPF nem CNPJ, retorna o documento original
			else {
				return $documento;
			}
		}

		function formatarChaveNFe($chave)
		{
			return substr($chave, 0, 4) . ' ' . substr($chave, 4, 4) . ' ' . substr($chave, 8, 4) . ' ' . substr($chave, 12, 4) . ' ' . substr($chave, 16, 4) . ' ' . substr($chave, 20, 4) . ' ' . substr($chave, 24, 4) . ' ' . substr($chave, 28, 4) . ' ' . substr($chave, 32, 4) . ' ' . substr($chave, 36, 4). ' '.substr($chave, 40, 4);
		}
		$chaveNFeFormatada = formatarChaveNFe($chavenota);
		$codigoBarrasHtml = '<img src="data:image/png;base64,'. base64_encode($generator->getBarcode($chavenota, $generator::TYPE_CODE_128, 1, 35)) . '">';

		$html = null;
		$html = '
		<link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39+Extended+Text&display=swap" rel="stylesheet">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

		<style>
			@page {margin: 8px !important}
			.danfe_pagina{ font-size:10px;font-family:Courier New;margin:0px;padding:1px;}
			.danfe_pagina2{ margin:1px;padding:0 }
			.danfe_linha_tracejada{ width:100%;border-bottom:#000 1px dashed;margin:1px 0 10px 0 }

			.danfe_tabelas{ border-collapse:collapse;width:100%;margin:0;padding:0 }
			.danfe_celula_bordas{ border:1px solid black; vertical-align:top }
			.danfe_celula_titulo{ margin:0;font-size:8px;padding:0 2px 0px 2px }
			.danfe_celula_valor{ margin:0;font-size:8pt;padding-left:4px }

			.danfe_canhoto_bordas{ font-size:7pt;border:1px solid #000;margin:0px;padding:0;margin:0 1px 0 1px }
			.danfe_canhoto_texto{ font-size:6pt;margin:0;font-weight:normal;padding:0 2px 1px 2px }

			.danfe_cabecalho_danfe{ font-size:13px;font-weight:bold;margin:0;text-align:center }
			.danfe_cabecalho_danfe_texto{ font-size:7pt;padding:0;margin:0 1px 0 1px;text-align:center }
			.danfe_cabecalho_numero{ font-size:13px;font-weight:bold;margin:0;text-align:center }
			.danfe_cabecalho_entrada_saida{ font-size:7pt; }
			.danfe_cabecalho_entrada_saida_quadrado{ font-size:13pt;border:1px solid #000000;padding:0;margin:0;width:40px;text-align:center;float:none;min-width:30px }

			.danfe_titulo_externo{ font-size:8pt;margin:4px 0 0 0;font-weight:bold }

			.danfe_item{ border:1px black solid;border-top:none;border-bottom:dashed 1pt #dedede; text-align: right;}
			.danfe_item_ultimo{ border:1px black solid;border-top:none;margin:0px;padding:0;font-size:1px }
			.danfe_item_cabecalho{ border:1px solid #000;text-align:left;font-size:8px }
			.danfe_item_cabecalho_tabela{ border-collapse:collapse;width:100%;margin:0;padding:0;border:1px solid #000 }
			footer {position: fixed; bottom: 0cm; left: 0cm; right: 0cm;height: 30px;}
		</style>
			<div class="danfe_pagina">
				<div class="danfe_pagina2">
					<table class="danfe_tabelas">
						<tr>
							<td>
								<table class="danfe_tabelas" style="min-height:60px;">
									<tr>
										<td class="danfe_celula_bordas" colspan="2">
											<p class="danfe_canhoto_texto">
												RECEBEMOS DE '.$cNome.' OS PRODUTOS CONSTANTES DA NOTA FISCAL INDICADA ABAIXO, EMISSÃO: '.$dataEmissao.' VALOR TOTAL R$ '.number_format((float)$valornf, 2, ',', '.').' 
												DESTINATÁRIO: '.$vita_razao.' - '.$vita_endereco.', '.$vita_endereco_numero.' - '.$vita_complemento.' '.$vita_bairro.' '.$vita_cidade.'
											</p>
										</td>
										<td class="danfe_celula_bordas" rowspan="2" style="width:10%;text-align:center">
											<strong>NF-e</strong>
											<h2 class="danfe_cabecalho_numero">N&ordm; '.number_format((float)$nota, 0, ',', '.').'</h2>
											<strong>S&eacute;rie '.$serie.'</strong>
										</td>
									</tr>
									<tr>
										<td class="danfe_canhoto_bordas">
											<p class="danfe_celula_titulo">Data de recebimento</p>
											<p class="danfe_celula_valor">&nbsp;</p>
										</td>
										<td class="danfe_canhoto_bordas">
											<p class="danfe_celula_titulo">Identifica&ccedil;&atilde;o e assinatura do recebedor</p>
											<p class="danfe_celula_valor">&nbsp;</p>
										</td>
									</tr>
								</table>
							</td>
							<td>&nbsp;</td>
							<td>
							</td>
						</tr>
					</table>
					<div class="danfe_linha_tracejada"></div>
					<table class="danfe_tabelas">
						<tr>
							<td rowspan="3" colspan="2" align="center" class="danfe_celula_bordas">
								<p style="font-size:9px;font-style:italic">IDENTIFICAÇÃO DO EMITENTE</p>
								<p style="font-size:12px;font-weight:bold">'.$for_nome_fantasia.'</p>
								<p style="font-size:10px;">'.$for_endereco.', '.$for_numero.'<br>
								'.$for_bairro.' - '.$for_cep.'<br>
								'.$for_cidade.' Fone: '.formataTelefone($for_telefoneddd. $for_telefone).'</p>
							</td>
							<td rowspan="3" class="danfe_celula_bordas" align="center">
								<p class="danfe_cabecalho_danfe">DANFE</p>
								<p class="danfe_cabecalho_danfe_texto">Documento Auxiliar da <br>Nota Fiscal Eletr&ocirc;nica</p>
								<table class="danfe_tabelas">
								<tr>
									<td nowrap class="danfe_cabecalho_entrada_saida">
									0-Entrada<br>
									1-Sa&iacute;da</td>
									<td class="danfe_cabecalho_entrada_saida_quadrado">'.$tpNF.'</td>
								</tr>
								</table>
								<p class="danfe_cabecalho_numero">N&ordm; '.number_format((float)$nota, 0, ',', '.').'</p>
								<p class="danfe_cabecalho_danfe_texto" style="font-weight:bold">Série: '.$serie.'<br>P&aacute;gina: 1 de 1</p>
							</td>
							<td class="danfe_celula_bordas" align="center">
								'.$codigoBarrasHtml.'
							</td>
						</tr>
						<tr>
							<td class="danfe_celula_bordas" align="center">
							<p class="danfe_celula_titulo">Chave de acesso</p>
							<p class="danfe_celula_valor" style="font-weight:bold">'.$chaveNFeFormatada.'</p>
							</td>
						</tr>
						<tr>
							<td class="danfe_celula_bordas" align="center">
							<p class="danfe_celula_titulo">Consulta de autenticidade no portal nacional da NF-e</p>
							<p class="danfe_celula_valor">
							<a href="http://www.nfe.fazenda.gov.br/portal" target="_blank">www.nfe.fazenda.gov.br/portal</a> 
							ou no site da Sefaz autorizadora</p>
							</td>
						</tr>
						<tr>
							<td colspan="3" class="danfe_celula_bordas">
							<p class="danfe_celula_titulo">Natureza da opera&ccedil;&atilde;o</p>
							<p class="danfe_celula_valor" style="text-align:center;font-weight:bold">'.$naturezaOp.'</p>
							</td>
							<td class="danfe_celula_bordas" align="center">
							<p class="danfe_celula_titulo">N&uacute;mero de protocolo de autoriza&ccedil;&atilde;o de uso da NF-e</p>
							<p class="danfe_celula_valor" style="font-weight:bold">Documento não original</p>
							</td>
						</tr>
					</table>
					<table class="danfe_tabelas">
						<tr>
							<td class="danfe_celula_bordas">
								<p class="danfe_celula_titulo">Inscri&ccedil;&atilde;o Estadual</p>
								<p class="danfe_celula_valor" style="font-weight:bold;text-align: center;">'.$for_inscestadual.'</p>
							</td>
							<td class="danfe_celula_bordas">
								<p class="danfe_celula_titulo">INSCRIÇÃO ESTADUAL DO SUBST. TRIBUT.</p>
								<p class="danfe_celula_valor">&nbsp;</p>
							</td>
							<td class="danfe_celula_bordas">
								<p class="danfe_celula_titulo">CNPJ</p>
								<p class="danfe_celula_valor" style="font-weight:bold;text-align: center;">'.formatarDocumento($doc).'</p>
							</td>
						</tr>
					</table>

					<h3 class="danfe_titulo_externo">DESTINATÁRIO / REMETENTE</h3>
					<table class="danfe_tabelas">
						<tr>
							<td>
								<table class="danfe_tabelas">
									<tr>
										<td class="danfe_celula_bordas" colspan="2">
											<p class="danfe_celula_titulo">Nome / Raz&atilde;o Social</p>
											<p class="danfe_celula_valor" style="font-weight:bold">'.$vita_razao.'</p>
										</td>
										<td class="danfe_celula_bordas">
											<p class="danfe_celula_titulo">CNPJ/CPF</p>
											<p class="danfe_celula_valor" style="font-weight:bold;text-align: center;">'.$vita_doc.'</p>
										</td>
										<td class="danfe_celula_bordas">
											<p class="danfe_celula_titulo">Data da Emissão</p>
											<p class="danfe_celula_valor" style="font-weight:bold;text-align: center;">'.$dataEmissao.'</p>
										</td>
									</tr>
									<tr>
										<td class="danfe_celula_bordas">
											<p class="danfe_celula_titulo">Endere&ccedil;o</p>
											<p class="danfe_celula_valor" style="font-weight:bold">'.$vita_endereco.', '.$vita_endereco_numero.' - '.$vita_complemento.'</p>
										</td>
										<td class="danfe_celula_bordas">
											<p class="danfe_celula_titulo">Bairro</p>
											<p class="danfe_celula_valor" style="font-weight:bold;text-align: center;">'.$vita_bairro.'</p>
										</td>
										<td class="danfe_celula_bordas">
											<p class="danfe_celula_titulo">CEP</p>
											<p class="danfe_celula_valor" style="font-weight:bold;text-align: center;">'.$vita_cep.'</p>
										</td>
										<td class="danfe_celula_bordas">
											<p class="danfe_celula_titulo">Data da Saida/Entrada</p>
											<p class="danfe_celula_valor" style="font-weight:bold;text-align: center;">'.$dataEmissao.'</p>
										</td>
									</tr>
									<tr>
										<td class="danfe_celula_bordas">
											<p class="danfe_celula_titulo">Munic&iacute;pio</p>
											<p class="danfe_celula_valor" style="font-weight:bold">'.$vita_cidade.'</p>
										</td>
										<td class="danfe_celula_bordas">
											<p class="danfe_celula_titulo">UF</p>
											<p class="danfe_celula_valor" style="font-weight:bold">'.$vita_estado.'</p>
										</td>
										<td class="danfe_celula_bordas">
											<p class="danfe_celula_titulo">Fone/Fax</p>
											<p class="danfe_celula_valor" style="font-weight:bold;text-align: center;">'.'('.$vita_telefone_ddd.') '.$vita_telefone_numero.'</p>
										</td>
										<td class="danfe_celula_bordas">
											<p class="danfe_celula_titulo">Inscrição Estadual</p>
											<p class="danfe_celula_valor" style="font-weight:bold;text-align: center;">'.$vita_ie.'</p>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>

					<h3 class="danfe_titulo_externo">FATURA / DUPLICATA</h3>';
					foreach($parcela_lista as $lista){
					$html .= '<div style="display: inline-block; margin: 1px; vertical-align: top;">
						<table class="" style="border: 1px black solid;font-size: 9px;">
							<tr class="" align="left">
								<td class="">
									Num. 
								</td>
								<td class="">
									<span style="float: right;font-weight: bold;">'.str_pad($lista['nSequencia'], 3, "0", STR_PAD_LEFT).'</span>
								</td>
							</tr>
							<tr class="" align="left">
								<td class="">
									Venc. 
								</td>
								<td class="">
									<span style="float: right;font-weight: bold;">'.$lista['dVencimento'].'</span>
								</td>
							</tr>
							<tr class="" align="left">
								<td class="">
									Valor &nbsp;&nbsp;
								</td>
								<td class="">
									<span style="float: right;font-weight: bold;">R$ '.number_format($lista['vParcela'], 2, ',', '.').'</span>
								</td>
							</tr>
						</table>
					</div>';
					}
				
					$html .= '<h3 class="danfe_titulo_externo">CÁLCULO DO IMPOSTO</h3>
					<table class="danfe_tabelas">
						<tr>
							<td class="danfe_celula_bordas" style="width: 15%;">
								<p class="danfe_celula_titulo">Base de c&aacute;lculo do ICMS</p>
								<p class="danfe_celula_valor" style="text-align: right;font-weight: bold;">'.number_format($baseicms, 2, ',', '.').'</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 15%;">
								<p class="danfe_celula_titulo">Valor do ICMS</p>
								<p class="danfe_celula_valor" style="text-align: right;font-weight: bold;">'.number_format($valoricms, 2, ',', '.').'</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 15%;">
								<p class="danfe_celula_titulo">Base de c&aacute;lculo do ICMS Subst.</p>
								<p class="danfe_celula_valor" style="text-align: right;font-weight: bold;">0,00</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 15%;">
								<p class="danfe_celula_titulo">Valor do ICMS Subst.</p>
								<p class="danfe_celula_valor" style="text-align: right;font-weight: bold;">0,00</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 15%;">
								<p class="danfe_celula_titulo">Valor Imp. Importação</p>
								<p class="danfe_celula_valor" style="text-align: right;font-weight: bold;">0,00</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 10%;">
								<p class="danfe_celula_titulo">Valor do PIS</p>
								<p class="danfe_celula_valor" style="text-align: right;font-weight: bold;">'.number_format($valorpis, 2, ',', '.').'</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 20%;">
								<p class="danfe_celula_titulo">Valor total dos produtos</p>
								<p class="danfe_celula_valor" style="text-align: right;font-weight: bold;">'.number_format($valortotalprod, 2, ',', '.').'</p>
							</td>
						</tr>
					</table>
					<table class="danfe_tabelas">
						<tr>
							<td class="danfe_celula_bordas" style="width: 15%;">
								<p class="danfe_celula_titulo">Valor do frete</p>
								<p class="danfe_celula_valor" style="text-align: right;font-weight: bold;">0,00</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 15%;">
								<p class="danfe_celula_titulo">Valor do seguro</p>
								<p class="danfe_celula_valor" style="text-align: right;font-weight: bold;">0,00</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 15%;">
								<p class="danfe_celula_titulo">Desconto</p>
								<p class="danfe_celula_valor" style="text-align: right;font-weight: bold;">0,00</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 15%;">
								<p class="danfe_celula_titulo">Outras despesas acess&oacute;rias</p>
								<p class="danfe_celula_valor" style="text-align: right;font-weight: bold;">0,00</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 15%;">
								<p class="danfe_celula_titulo">Valor total do IPI</p>
								<p class="danfe_celula_valor" style="text-align: right;font-weight: bold;">0,00</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 10%;">
								<p class="danfe_celula_titulo">Valor da cofins</p>
								<p class="danfe_celula_valor" style="text-align: right;font-weight: bold;">'.number_format($valorcofins, 2, ',', '.').'</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 20%;">
								<p class="danfe_celula_titulo">Valor total da nota</p>
								<p class="danfe_celula_valor" style="text-align: right;font-weight: bold;">'.number_format($valornf, 2, ',', '.').'</p>
							</td>
						</tr>
					</table>
					<h3 class="danfe_titulo_externo">TRANSPORTADOR / VOLUMES TRANSPORTADOS</h3>
					<table class="danfe_tabelas">
						<tr>
							<td class="danfe_celula_bordas" style="width: 25%;">
								<p class="danfe_celula_titulo" style="text-transform: uppercase;">Nome / Razão Social</p>
								<p class="danfe_celula_valor" style="font-weight: bold;">'.$transp_razao.'</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 15%;">
								<p class="danfe_celula_titulo" style="text-transform: uppercase;">Frete por conta</p>
								<p class="danfe_celula_valor" style="text-align: center;font-weight: bold;">'.$transp_tipo.' - '.$transp_tipo_desc.'</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 15%;">
								<p class="danfe_celula_titulo" style="text-transform: uppercase;">C&oacute;digo ANTT</p>
								<p class="danfe_celula_valor">&nbsp;</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 15%;">
								<p class="danfe_celula_titulo" style="text-transform: uppercase;">Placa do ve&iacute;culo</p>
								<p class="danfe_celula_valor">&nbsp;</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 10%;">
								<p class="danfe_celula_titulo">UF</p>
								<p class="danfe_celula_valor">&nbsp;</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 20%;">
								<p class="danfe_celula_titulo">CNPJ/CPF</p>
								<p class="danfe_celula_valor" style="text-align: center;font-weight: bold;">'.$transp_cnpj.'</p>
							</td>
						</tr>
					</table>
					<table class="danfe_tabelas">
						<tr>
							<td class="danfe_celula_bordas" style="width: 40%;">
								<p class="danfe_celula_titulo" style="text-transform: uppercase;">Endere&ccedil;o</p>
								<p class="danfe_celula_valor"></p>
							</td>
							<td class="danfe_celula_bordas" style="width: 30%;">
								<p class="danfe_celula_titulo" style="text-transform: uppercase;">Munic&iacute;pio</p>
								<p class="danfe_celula_valor"></p>
							</td>
							<td class="danfe_celula_bordas" style="width: 10%;">
								<p class="danfe_celula_titulo">UF</p>
								<p class="danfe_celula_valor"></p>
							</td>
							<td class="danfe_celula_bordas" style="width: 20%;">
								<p class="danfe_celula_titulo" style="text-transform: uppercase;">Inscri&ccedil;&atilde;o Estadual</p>
								<p class="danfe_celula_valor"></p>
							</td>
						</tr>
					</table>
					<table class="danfe_tabelas">
						<tr>
							<td class="danfe_celula_bordas" style="width: 10%;">
								<p class="danfe_celula_titulo" style="text-transform: uppercase;">Quantidade</p>
								<p class="danfe_celula_valor" style="text-align: center;font-weight: bold;">'.$transp_qtd.'</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 20%;">
								<p class="danfe_celula_titulo" style="text-transform: uppercase;">Esp&eacute;cie</p>
								<p class="danfe_celula_valor" style="text-align: center;font-weight: bold;">'.$transp_volume.'</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 10%;">
								<p class="danfe_celula_titulo" style="text-transform: uppercase;">Marca</p>
								<p class="danfe_celula_valor">&nbsp;</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 20%;">
								<p class="danfe_celula_titulo" style="text-transform: uppercase;">Numera&ccedil;&atilde;o</p>
								<p class="danfe_celula_valor">&nbsp;</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 20%;">
								<p class="danfe_celula_titulo" style="text-transform: uppercase;" >Peso bruto (KG)</p>
								<p class="danfe_celula_valor" style="text-align: right;font-weight: bold;">'.number_format($transp_peso_bruto, 3, ',', '.').'</p>
							</td>
							<td class="danfe_celula_bordas" style="width: 20%;">
								<p class="danfe_celula_titulo" style="text-transform: uppercase;">Peso l&iacute;quido (KG)</p>
								<p class="danfe_celula_valor" style="text-align: right;font-weight: bold;">'.number_format($transp_peso_liquido, 3, ',', '.').'</p>
							</td>
							
						</tr>
					</table>
					<h3 class="danfe_titulo_externo">DADOS DOS PRODUTOS / SERVIÇOS</h3>
					<table class="danfe_item_cabecalho_tabela" style="position: relative;">
						<tr style="">
							<td class="danfe_item_cabecalho" style="text-align: center;">CÓDIGO PRODUTO</td>
							<td class="danfe_item_cabecalho">DESCRIÇÃO DO PRODUTO / SERVIÇO</td>
							<td class="danfe_item_cabecalho">NCM/SH</td>
							<td class="danfe_item_cabecalho">CST</td>
							<td class="danfe_item_cabecalho">CFOP</td>
							<td class="danfe_item_cabecalho">UN</td>
							<td class="danfe_item_cabecalho">QUANT</td>
							<td class="danfe_item_cabecalho">VALOR UNIT</td>
							<td class="danfe_item_cabecalho">VALOR TOTAL</td>
							<td class="danfe_item_cabecalho">B. CÁLC ICMS</td>
							<td class="danfe_item_cabecalho">VALOR ICMS</td>
							<td class="danfe_item_cabecalho">VALOR IPI</td>
							<td class="danfe_item_cabecalho">ALIQ. ICMS</td>
							<td class="danfe_item_cabecalho">ALIQ. IPI</td>
						</tr>';
						$itens_cont = 0;
						foreach($itens as $i){
							$html .= '<tr class="danfe_item" style="font-size:10px">
								<td align="left" style="border-right: 1px black solid;text-align: center">'.$i['itensCabec']['cCodigoProduto'].'</td>
								<td align="left" style="border-right: 1px black solid;">'.$i['itensCabec']['cDescricaoProduto'].'</td>
								<td style="border-right: 1px black solid;">'.str_replace('.', '', $i['itensCabec']['cNCM']).'</td>
								<td style="border-right: 1px black solid;">000</td>
								<td style="border-right: 1px black solid;">'.$i['itensCabec']['cCFOP'].'</td>
								<td style="border-right: 1px black solid;">UN</td>
								<td style="border-right: 1px black solid;">'.(isset($i['itensCabec']['nQtdeNFe']) ? number_format($i['itensCabec']['nQtdeNFe'], 0, ',', '.') : 0).'</td>
								<td  align="right" style="border-right: 1px black solid;">'.(isset($i['itensCabec']['nPrecoUnit']) ? number_format($i['itensCabec']['nPrecoUnit'], 2, ',', '.') : 0).'</td>
								<td  align="right" style="border-right: 1px black solid;">'.(isset($i['itensCabec']['vTotalItem']) ? number_format($i['itensCabec']['vTotalItem'], 2, ',', '.') : 0).'</td>
								<td  align="right" style="border-right: 1px black solid;">'.(isset($i['itensCabec']['nBC']) ? number_format($i['itensICMS']['nBC'], 2, ',', '.') : 0).'</td>
								<td  align="right" style="border-right: 1px black solid;">'.(isset($i['itensCabec']['nValor']) ? number_format($i['itensICMS']['nValor'], 2, ',', '.') : 0).'</td>
								<td  align="right" style="border-right: 1px black solid;">'.(isset($i['itensCabec']['nValIPIDev']) ? number_format($i['itensIPI']['nValIPIDev'], 2, ',', '.') : 0).'</td>
								<td style="border-right: 1px black solid;">'.(isset($i['itensCabec']['nAliq']) ? number_format($i['itensICMS']['nAliq'], 2, ',', '.') : 0).'</td>
								<td>0,00</td>
							</tr>';
							$itens_cont++;
						}
						$altura = $itens_cont * 15;
						$altura_sobra = 350 - $altura;
						$html .= '<tr class="">
							<td style="border-right: 1px black solid;height:'.$altura_sobra.'px"></td>
							<td style="border-right: 1px black solid;"></td>
							<td style="border-right: 1px black solid;"></td>
							<td style="border-right: 1px black solid;"></td>
							<td style="border-right: 1px black solid;"></td>
							<td style="border-right: 1px black solid;"></td>
							<td style="border-right: 1px black solid;"></td>
							<td style="border-right: 1px black solid;"></td>
							<td style="border-right: 1px black solid;"></td>
							<td style="border-right: 1px black solid;"></td>
							<td style="border-right: 1px black solid;"></td>
							<td style="border-right: 1px black solid;"></td>
							<td style="border-right: 1px black solid;"></td>
							<td style="border-right: 1px black solid;"></td>
						</tr>
					</table>
				</div>
			</div>
			<footer>
				<div class="danfe_titulo_externo">DADOS ADICIONAIS</div>
					<table class="danfe_tabelas">
					<tr style="min-height:200px">
						<td class="danfe_celula_bordas" width="70%">
							<p class="danfe_celula_titulo">INFORMAÇÕES COMPLEMENTARES</p>
							<p class="danfe_celula_valor">'.$obs.'</p>
						</td>
						<td class="danfe_celula_bordas">
							<p class="danfe_celula_titulo">RESERVADO AO FISCO</p>
						</td>
					</tr>
					</table>
				</div>
			</footer>';

			$dompdf->loadHtml($html);
			$dompdf->render();
			//$dompdf->stream('documento.pdf');
			
			$nome_arquivo = 'Danfe_'.date('Y-m-d-H-i-s');
			$zip_file_name = $nome_arquivo.'.zip';
			//file_put_contents('file/'.$nome_arquivo.'pdf', $dompdf->output());

			$zip = new ZipArchive();
			if ($zip->open('file/'.$zip_file_name, ZipArchive::CREATE) === TRUE) {
				// Adicionar o arquivo PDF ao arquivo zip
				$zip->addFromString($nome_arquivo.'.pdf', $dompdf->output());
				// Fechar o arquivo zip
				$zip->close();
				
			} else {
				echo 'Falha ao criar o arquivo zip';
			}


			
