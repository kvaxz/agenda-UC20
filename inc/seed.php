<?php
function diaDaqui($n, $hora)
{
    return mktime($hora, 0, 0, (int) date('n'), (int) date('j') + $n, (int) date('Y'));
}

function criarDadosIniciais()
{
    return [
        'categorias' => [
            ['id' => 1, 'nome' => 'Shows', 'ativa' => true],
            ['id' => 2, 'nome' => 'Feiras e gastronomia', 'ativa' => true],
            ['id' => 3, 'nome' => 'Festivais', 'ativa' => true],
            ['id' => 4, 'nome' => 'Exposições', 'ativa' => true],
            ['id' => 5, 'nome' => 'Turismo', 'ativa' => true],
        ],
        'usuarios' => [],
        'eventos' => [
            ['id' => 1, 'organizadorId' => 0, 'status' => 'aprovado', 'destaque' => true, 'categoriaId' => 3,
                'nome' => 'Festival Pantaneiro de Música',
                'descricao' => 'Três palcos com artistas de Mato Grosso do Sul, do chamamé ao rock regional, em uma noite inteira ao ar livre no parque.',
                'inicio' => diaDaqui(2, 17), 'fim' => diaDaqui(2, 23),
                'local' => 'Parque das Nações Indígenas', 'endereco' => 'Av. Afonso Pena, Jardim dos Estados',
                'imagens' => ['nacoes.png'], 'ingresso' => ['tipo' => 'pago', 'valor' => 40]],
            ['id' => 2, 'organizadorId' => 0, 'status' => 'aprovado', 'destaque' => true, 'categoriaId' => 2,
                'nome' => 'Noite do Sobá na Feira Central',
                'descricao' => 'Degustação de sobá, chipa e pratos da culinária sul-mato-grossense com música ao vivo nas barracas.',
                'inicio' => diaDaqui(1, 18), 'fim' => diaDaqui(1, 22),
                'local' => 'Feira Central', 'endereco' => 'Rua 14 de Julho, Centro',
                'imagens' => ['feira-central.jpg'], 'ingresso' => ['tipo' => 'gratuito', 'valor' => 0]],
            ['id' => 3, 'organizadorId' => 0, 'status' => 'aprovado', 'destaque' => false, 'categoriaId' => 5,
                'nome' => 'Entardecer no Bioparque Pantanal',
                'descricao' => 'Visita guiada ao fim da tarde pelos aquários e pela trilha de biodiversidade, com conversa sobre a fauna do Pantanal.',
                'inicio' => diaDaqui(4, 16), 'fim' => diaDaqui(4, 19),
                'local' => 'Bioparque Pantanal', 'endereco' => 'Rua Ceará, Miguel Couto',
                'imagens' => ['bioparque.webp'], 'ingresso' => ['tipo' => 'pago', 'valor' => 35]],
            ['id' => 4, 'organizadorId' => 0, 'status' => 'aprovado', 'destaque' => false, 'categoriaId' => 1,
                'nome' => 'Viola de Cocho ao pôr do sol',
                'descricao' => 'Roda de viola de cocho e cururu à beira do Anhanduí, para ver o sol cair com música de raiz.',
                'inicio' => diaDaqui(6, 17), 'fim' => diaDaqui(6, 20),
                'local' => 'Orla Morena', 'endereco' => 'Av. Ernesto Geisel, Orla Morena',
                'imagens' => ['orla.webp'], 'ingresso' => ['tipo' => 'gratuito', 'valor' => 0]],
            ['id' => 5, 'organizadorId' => 0, 'status' => 'aprovado', 'destaque' => false, 'categoriaId' => 4,
                'nome' => 'Pantanal em Foco: exposição fotográfica',
                'descricao' => 'Quarenta fotografias de fotógrafos locais sobre a cheia, a seca e os povos do Pantanal.',
                'inicio' => diaDaqui(9, 9), 'fim' => diaDaqui(20, 18),
                'local' => 'Parque das Nações Indígenas', 'endereco' => 'Av. Afonso Pena, Jardim dos Estados',
                'imagens' => ['nacoes.png'], 'ingresso' => ['tipo' => 'gratuito', 'valor' => 0]],
            ['id' => 6, 'organizadorId' => 0, 'status' => 'aprovado', 'destaque' => false, 'categoriaId' => 3,
                'nome' => 'Festival de Inverno da Feira',
                'descricao' => 'Edição anterior do festival de caldos e música de viola.',
                'inicio' => diaDaqui(-12, 18), 'fim' => diaDaqui(-12, 22),
                'local' => 'Feira Central', 'endereco' => 'Rua 14 de Julho, Centro',
                'imagens' => ['feira'], 'ingresso' => ['tipo' => 'gratuito', 'valor' => 0]],
        ],
    ];
}
