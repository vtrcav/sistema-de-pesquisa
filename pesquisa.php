<?php

    if(empty($_GET['pesquisa'])){
        header("Location: index.php");
        exit;
    }

    if(empty($_GET['seletor'])){
        header("Location: index.php");
        exit;
    }

    $termo = "%".trim($_GET['pesquisa'])."%";
    $seletor = $_GET['seletor'];

    $conexaodb = new PDO('mysql:host=localhost;dbname=basehosp', 'root', '');

    if($seletor == 'atendimento'){
    $parametro = $conexaodb->prepare('SELECT * FROM `atendimentos` WHERE `nome` LIKE :termo OR `cns` LIKE :termo OR `prontuario` LIKE :termo OR `dtatend` LIKE :termo');

    }else if($seletor == 'internacao'){
    $parametro = $conexaodb->prepare('SELECT * FROM `internacao` WHERE `nome` LIKE :termo OR `cns` LIKE :termo OR `prontuario` LIKE :termo OR `dtatend` LIKE :termo');
  
    }else if($seletor == 'agendamento'){
    $parametro = $conexaodb->prepare('SELECT * FROM `agendamentosambulatorial` WHERE `nome` LIKE :termo OR `cns` LIKE :termo OR `prontuario` LIKE :termo OR `dtatend` LIKE :termo');
   
    }

    $parametro->bindParam(':termo', $termo, PDO::PARAM_STR);
    $parametro->execute();
    
    $resultados = $parametro->fetchAll(PDO::FETCH_ASSOC); 
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_GET['pesquisa'] ?> - Arquivos Hospital Nina Rodrigues</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="main">
        <div>
            <img src="brasao.png" alt="" width="15%" class="center">
    <h3>GOVERNO DO ESTADO DO MARANHÃO<br>SECRETARIA DE ESTADO DA SAÚDE<br>HOSPITAL NINA RODRIGUES</h3>      
    <h2>Exibindo resultados para <em><u><?php echo strtoupper($_GET['pesquisa']) ?></u></em></h2>
    <div class="link"><a href="index.php">Nova pesquisa</a></div>
    <?php
        if (count($resultados)){
            if($seletor === 'agendamento'){
    ?>        
            <table>
                <tbody>
                    <tr>
                        <th>PRONTUÁRIO</th>
                        <th>NOME</th>
                        <th>MÃE</th>
                        <th>ADMISSÃO</th>
                        <th>HORA</th>
                        <th>TIPO</th>
                        <th>CNS</th>
                    </tr>    
    <?php
            }
            else{
    ?>
            <table>
                <tbody>
                    <tr>
                        <th>PRONTUÁRIO</th>
                        <th>NOME</th>
                        <th>SETOR</th>
                        <th>ADMISSÃO</th>
                        <th>HORA</th>
                        <th>ALTA</th>
                        <th>TIPO</th>
                        <th>CNS</th>
                    </tr>
    <?php
            }
            foreach($resultados as $Resultado){
                if($seletor === 'agendamento'){
    ?>            
                    <tr>
                        <td><?php echo $Resultado['PRONTUARIO'] ?></td> 
                        <td><?php echo $Resultado['NOME'] ?></td>
                        <td><?php echo $Resultado['MAE'] ?></td>
                        <td><?php $date = ($Resultado['DTATEND']); 
                        if (!is_null($date)){ 
                            echo date("d-m-Y", strtotime($date));
                            } 
                            ?></td>
                        <td><?php $hratend = ($Resultado['HRATEND']); echo date("h:i", strtotime($hratend)); ?></td>
                        <td><?php echo strtoupper($_GET['seletor']) ?></td>
                        <td><?php echo $Resultado['CNS'] ?></td>    
                    </tr>
    <?php
                }else{
                    ?>
                    <tr>
                        <td><?php echo $Resultado['PRONTUARIO'] ?></td>
                        <td><?php echo $Resultado['NOME'] ?></td>
                        <td><?php echo $Resultado['MAE'] ?></td>
                        <td><?php echo $Resultado['SETOR'] ?></td>    
                        <td><?php 
                        
                        $dtatend = ($Resultado['DTATEND']); 
                        if (!is_null($dtatend))
                        { 
                            $jUnixDate = strtotime($dtatend);
                            if ($jUnixDate > 0)
                            {
                                echo date('d-m-Y', $jUnixDate);
                            }
                        }
                            ?></td>  
                        <td><?php $hratend = ($Resultado['HRATEND']); echo date("h:i", strtotime($hratend)); ?></td>
                        <td><?php 
                        
                        $dtcheckout = ($Resultado['DTCHECKOUT']);
                        if (!is_null($dtcheckout))
                        { 
                            $jUnixDate = strtotime($dtcheckout);
                            if ($jUnixDate > 0)
                            {
                                echo date('d-m-Y', $jUnixDate);
                            }
                        }
                            ?></td> 
                        <td><?php echo strtoupper($_GET['seletor']) ?></td>  
                        <td><?php echo $Resultado['CNS'] ?></td>   
                    </tr>
                    <?php    
                }
        } 
    }else{
        ?>
        <p>Não foram encontrados resultados.</p>
    <?php
    }
    ?>    
    </tbody>
    </table>
</div>
</div>
</body>
</html>