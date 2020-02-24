<?php


    function formatCnpjCpf($value){
        $cnpj_cpf = preg_replace("/\D/", '', $value);
        
        if (strlen($cnpj_cpf) === 11) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
        } 
        
        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
    }
    
    function procuraEnderecoCNPJ($cnpj, $linha){
        $url = "http://ws.hubdodesenvolvedor.com.br/v2/cnpj/?cnpj=".$cnpj."&token=77220620WEkHPaQoXV139419488";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch,CURLOPT_TIMEOUT,450);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,10);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = @json_decode($result);

        if(@$result -> return == "OK" ){
            $linha[] = formatCnpjCpf(@$result -> result -> numero_de_inscricao);
            $linha[] = @$result -> result -> logradouro;
            $linha[] = @$result -> result -> numero;
            $linha[] = @$result -> result -> complemento;
            $linha[] = @$result -> result -> cep;
            $linha[] = @$result -> result -> bairro;
            $linha[] = @$result -> result -> municipio;

        }
        return $linha; 
    }

    $fileWrite = fopen('file.csv', 'a+');
    $fileCSV = file('inf_cadastral_fie.csv');
    set_time_limit ( 1000 );


    //print_r($fileCSV);

    for ($i = 1; $i < count($fileCSV); $i++ ){
        $linha = explode(";", $fileCSV[$i]);
        $cnpj = $linha[1];
        $cnpj = str_replace(".", "", $cnpj );
        $cnpj = str_replace("-", "", $cnpj );
        $cnpj = str_replace("/", "", $cnpj );

        $dadosEndereco = procuraEnderecoCNPJ($cnpj, $linha);

        print_r($dadosEndereco);
        echo "<br>";

        fputcsv($fileWrite, $dadosEndereco, ";");

    }
    
    fclose($fileWrite);
    fclose($fileCSV);

// Exemplo de scrip para exibir os nomes obtidos no arquivo CSV de exemplo

// Abrir arquivo para leitura

?>