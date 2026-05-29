<?php

header("Access-Control-Allow-Origin: *");

header("Content-Type: application/json; charset=UTF-8");

header("Access-Control-Allow-Methods: GET, POST");

header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'conexao.php';

$method = $_SERVER['REQUEST_METHOD'];

$action = $_GET['action'] ?? '';

switch($method) {

    // ====================================
    // SALVAR NO BANCO
    // ====================================

    case 'POST':

        // =========================
        // REDIRECT PARA OUTRO GRUPO
        // =========================

        if($action == 'redirect') {

            $data = json_decode(file_get_contents("php://input"));

            $payload = json_encode([
                "matricula" => $data->matricula,
                "nome" => $data->nome
            ]);

            // URL DO OUTRO GRUPO
            $url = "http://192.168.0.100:3333";

            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_POST, true);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);

            $response = curl_exec($ch);

            curl_close($ch);

            http_response_code(200);

            echo json_encode([
                "mensagem" => "Dados enviados para o próximo grupo com sucesso."
            ]);

            exit;
        }

        // =========================
        // SALVAR NORMAL
        // =========================

        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->nome) && !empty($data->email)) {

            $query = "INSERT INTO DadosPessoais
            (nome, email, celular, data_nascimento)
            VALUES
            (:nome, :email, :celular, :data_nascimento)";

            $stmt = $conn->prepare($query);

            $stmt->bindParam(":nome", $data->nome);

            $stmt->bindParam(":email", $data->email);

            $stmt->bindParam(":celular", $data->celular);

            $stmt->bindParam(":data_nascimento", $data->data_nascimento);

            if($stmt->execute()) {

                $matricula_gerada = $conn->lastInsertId();

                http_response_code(201);

                echo json_encode([

                    "mensagem" => "Dados criados com sucesso.",

                    "matricula" => $matricula_gerada

                ]);

            } else {

                http_response_code(503);

                echo json_encode([
                    "mensagem" => "Não foi possível criar o registro."
                ]);
            }

        } else {

            http_response_code(400);

            echo json_encode([
                "mensagem" => "Dados incompletos."
            ]);
        }

        break;

    // ====================================
    // BUSCAR MATRÍCULA
    // ====================================

    case 'GET':

        if(isset($_GET['matricula'])) {

            $query = "SELECT * FROM DadosPessoais
            WHERE matricula = :matricula
            LIMIT 0,1";

            $stmt = $conn->prepare($query);

            $stmt->bindParam(":matricula", $_GET['matricula']);

            $stmt->execute();

            if($stmt->rowCount() > 0) {

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                http_response_code(200);

                echo json_encode($row);

            } else {

                http_response_code(404);

                echo json_encode([
                    "mensagem" => "Usuário não encontrado."
                ]);
            }

        } else {

            http_response_code(400);

            echo json_encode([
                "mensagem" => "Informe a matrícula."
            ]);
        }

        break;

    default:

        http_response_code(405);

        echo json_encode([
            "mensagem" => "Método não permitido."
        ]);

        break;
}
?>