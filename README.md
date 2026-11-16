# Agenda Cultural de Campo Grande (MS)

Trabalho da UC20 - Metodologias Ágeis (Faculdade Senac MS)

**Equipe:** Geovane Martins, Fabio Camargo e Gabriel Cavalieri

Plataforma que reúne os eventos culturais de Campo Grande. Os organizadores cadastram os eventos e só aparecem na agenda pública os que foram aprovados por um curador.

## Como rodar

1. Instale o PHP.
2. Abra o terminal na pasta do projeto.
3. Rode `php -S localhost:8000`.
4. Abra `http://localhost:8000` no navegador.

Os dados ficam salvos em `inc/dados.json`. Para voltar ao começo, apague esse arquivo.

## Contas

| Perfil | Nome | E-mail | Senha |
|---|---|---|---|
| Organizador | Gabriel Cavalieri | cavalieri2005@hotmail.com | (preencher) |
| Curador | Henrique Ribeiro | curador@gmail.com | (preencher) |
| Administrador | adm | adm@gmail.com | (preencher) |

Novas contas podem ser criadas na página de cadastro.

## Perfis

- **Visitante:** vê a agenda, filtra e abre os detalhes dos eventos aprovados, sem login.
- **Organizador:** cadastra eventos e acompanha o status dos próprios eventos.
- **Curador:** analisa os eventos pendentes, aprova ou reprova.
- **Administrador:** vê todos os eventos e gerencia curadores e categorias.

## Estrutura

- `index.php`, `eventos.php`, `evento.php`: área pública.
- `login.php`, `cadastro.php`, `conta.php`, `sair.php`: acesso.
- `organizador/`, `curador/`, `admin/`: painéis de cada perfil.
- `inc/`: dados (`seed.php`, `dados.php`, `dados.json`), regras de negócio (`regras.php`) e peças da interface (`ui.php`).
- `css/`: estilos.
- `img/`: imagens dos eventos.
