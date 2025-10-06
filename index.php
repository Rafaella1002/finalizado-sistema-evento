<?php
//Armazenar as mensagens no sistema 
$ok = '';
$erro = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $titulo = trim($_POST['titulo'] ?? '');
      $data = trim($_POST['data'] ?? '');
        $hora = trim($_POST['hora'] ?? '');
          $local = trim($_POST['local'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');

       // Validação se algum campo obrigatório estiver vázio Exibe mensagem 
       if(!$titulo || !$data || !$hora || !$local || !$descricao ){
          $erro = "Preencha todos os campos!";
       }

       //Upload imagem 
       $caminhoImagem = '';
       if(empty($erro) && !empty($_FILES['imagem']['name']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK){
          
        //tamanho maximo de uma imagem 2mb
        $tamMax = 2*1024*1024;
        // Se o arquivo for > Que o limite 
        if($_FILES['imagem']['size'] > $tamMax){
            $erro = "imagem ta grande em !! (Máx. 2mb)";
        }else{
            // criamos uma pasta para armazenar as ikmagens caso não exista 
            //MIgramos para esta pasta 
     

            $nomeOriginal = basename((string)$_FILES['imagem']['name']);
            $nomeOriginal = preg_replace('/[^A-Za-z0-9_.-]/','_', $nomeOriginal);
            $nomeFinal = time().'_' .$nomeOriginal;

            $destino = __DIR__. '/uploads/'.$nomeFinal;

            //Mover arquivo para a pasta uploads
            if(move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)){
                $caminhoImagem = 'uploads/'.$nomeFinal;
            }else{
                $erro = "Falho ao salvar a imagem.";
            }

        }
        if(empty($erro)){
            $linha = $titulo. "|" .$data. "|" .$local. "|" .$descricao. "|" .$caminhoImagem.PHP_EOL;

            // Salvar os valores no bloco de notas
            file_put_contents(__DIR__."/eventos.txt",$linha, FILE_APPEND | LOCK_EX);
            echo "<script>alert('DEU CERTOOO!!😉')</script>";
        }
     }    
}
//Carregar Lista
$eventos = [];
$arquivo = __DIR__ . "/eventos.txt";
 
//Verifica se o arquivo existe
if (is_file($arquivo)) {
    // Ler arquivo por linha
    $linhas = file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    // faz o reverso puff.. para mostrar os primeiros registros sendo mais recentes
    $linhas = array_reverse($linhas);
 
    //Percorre cada linha e quebramos por | em diferentes elementos
 
    foreach ($linhas as $linha) {
        // Explode na |
        $p = explode('|', $linha);
        //Monta os pdeços de forma organizada
        $eventos[] = [
            'titulo' => $p[0],
            'data' => $p[1],
            'hora' => $p[2],
            'local' => $p[3],
            'descricao' => $p[4],
            'imagem' => $p[5],
        ];
    }
}
 
?>
<!DOCTYPE html>
<html lang="pt-br">
 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema</title>
  <link rel="stylesheet" href="style.css" type="text/css">
</head>
 
<body>
    <div class="bg"></div>
    <div class="container">
        <header class="topo">
            <h1 class="logo">Eventos Rio Preto</h1>
            <p class="sub">Todos os Eventos em Jão José do Rio Preto</p>
        </header>
 
        <section class="grid">
            <div class="card">
                <h2 class="card_titulo">Cadastrar Atividade</h2>

                <?php if(!empty($erro)) : ?>
                    <div class="aviso erro"><?= $erro ?></div>
                    <?php endif; ?>

                <form  class="form" method="post" enctype="multipart/form-data">
 
                    <label for="campo" class="campo">
                        <span>Título</span>
                        <input type="text" name="titulo" id="titulo" require>
                    </label>
 
                    <div class="dupla">
                        <label for="campo" class="campo">
                            <span>Data</span>
                            <input type="date" name="data" id="data" require>
                        </label>
 
                        <label for="campo" class="campo">
                            <span>Hora</span>
                            <input type="time" name="hora" id="hora" require>
                        </label>
 
                    </div>
 
                    <label for="campo" class="campo">
                        <span>Local</span>
                        <input type="text" name="local" id="local" require>
                    </label>
 
                    <label class="campo">
                        <span>Descrição</span>
                        <textarea name="descricao" rows="5" require></textarea>
                    </label>
 
                    <label class="campo">
                        <span>Imagem do Evento(Opcional)</span>
                        <input type="file" name="imagem" accept=".jpg,.jpeg,.png,.webp">
                    </label>
                    <button type="submit" class="botao_primario">Salvar</button>
 
 
                </form>

            <div class="card">
                <h2 class="card_titulo">Atividade Cadastradas</h2>
                <?php if(empty($eventos)): ?>
                    <p class="muted">Sem registro ! , Quer ver o que ?</p>
                    <?php else: ?>
                        <div class="Lista_eventos">
                        <?php foreach ($eventos as $e):
                        $titulo = $e['titulo'];
                        $data = $e['data'];
                        $hora = $e['hora'];
                        $descricao = nl2br($e['descricao']);
                        $local = $e['local'];
                        $imagem = $e['imagem'];
                        ?>
                        <article>
                            <img class="thumb" src="<?= $imagem ?>">
                            <h3 class="ev_titulo"><?= $titulo ?></h3>
                            <p class="ev_meta">
                                data : <?= $data ?>
                                Hora : <?= $data ?>
                                Local : <?= $local ?>
                            </p>
                            <p><?= $descricao ?></p>
                        </article>
                        <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
            </div>
        </section>
    </div>
      
    <footer class="rodape">
        <div class="container">
            <p>Feito em PHP💙</p>
        </div>
    </footer>
 
</body>
 
</html>