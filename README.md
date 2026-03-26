# Desafio Técnico SESAB — Desenvolvedor PHP Sênior

Projeto desenvolvido como parte do processo seletivo para a vaga de **Desenvolvedor PHP Sênior** da Secretaria de Saúde do Estado da Bahia (SESAB).

---

## Visão Geral

Sistema de gerenciamento de **Usuários**, **Perfis** e **Endereços**, com CRUD completo para cada entidade, seguindo as regras de negócio do enunciado:

- Um usuário só pode ser vinculado a **um** perfil
- Um perfil pode possuir **um ou vários** usuários
- Um usuário pode ter **mais de um** endereço, e um mesmo endereço pode pertencer a **mais de um** usuário (N:N)

---

## Stack Tecnológica

| Camada | Tecnologia |
|---|---|
| Backend | PHP 8.3 + Laravel 13 |
| Banco de dados | SQLite (arquivo local, zero configuração) |
| Frontend | Vue 3 + TypeScript + Vite |
| UI | Vuetify 3 (Material Design) |
| Autenticação | Bearer Token (cache `file`) |

---

## Pré-requisitos

- PHP >= 8.2 com extensões `pdo_sqlite`, `mbstring`, `openssl`
- Composer >= 2
- Node.js >= 18 + npm

---

## Configuração e Execução

### 1. Clonar o repositório

```bash
git clone https://github.com/danijcode/teste_pratico_sesab.git
cd teste_pratico_sesab
```

### 2. Backend (Laravel)

```bash
cd backend

# Instalar dependências PHP
composer install

# Copiar arquivo de ambiente
cp .env.example .env

# Gerar chave da aplicação
php artisan key:generate

# Executar migrations + seeders (cria o banco SQLite automaticamente)
php artisan migrate --seed

# Iniciar o servidor de desenvolvimento
php artisan serve
```

> O backend estará disponível em `http://localhost:8000`

### 3. Frontend (Vue 3)

Em outro terminal:

```bash
cd frontend

# Instalar dependências Node
npm install

# Iniciar o servidor de desenvolvimento
npm run dev
```

> O frontend estará disponível em `http://localhost:5173`

---

## Credenciais de Acesso

> **Observação importante:** A autenticação via Bearer Token **não faz parte dos requisitos obrigatórios** do desafio. Foi implementada como um **diferencial adicional** para proteger os endpoints da API e tornar o sistema mais próximo de um ambiente real, evitando que as telas fiquem acessíveis sem qualquer controle de acesso.
>
> Os usuários abaixo são **exclusivamente de teste**, definidos em memória no `AuthController`. Eles não estão relacionados aos usuários do CRUD e existem apenas para permitir o acesso ao sistema durante a avaliação.

| Perfil | E-mail | Senha |
|---|---|---|
| Administrador | `admin@sesab.ba.gov.br` | `admin123` |
| Usuário | `usuario@sesab.ba.gov.br` | `usuario123` |

---

## Estrutura do Banco de Dados

```
perfis
├── id
├── nome (único)
└── timestamps

usuarios
├── id
├── nome
├── email (único)
├── cpf (único, formato 000.000.000-00)
├── perfil_id (FK → perfis)
└── timestamps

enderecos
├── id
├── logradouro
├── cep (formato 00000-000)
└── timestamps

usuario_endereco (pivot N:N)
├── usuario_id (FK → usuarios)
└── endereco_id (FK → enderecos)
```

---

## Endpoints da API

Todos os endpoints (exceto login) exigem o header:
```
Authorization: Bearer {token}
```

### Autenticação
| Método | Endpoint | Descrição |
|---|---|---|
| POST | `/api/auth/login` | Realiza login, retorna o token |
| POST | `/api/auth/logout` | Invalida o token atual |
| GET | `/api/auth/me` | Retorna o usuário autenticado |

### Perfis
| Método | Endpoint | Descrição |
|---|---|---|
| GET | `/api/perfis` | Lista todos os perfis |
| POST | `/api/perfis` | Cria novo perfil |
| GET | `/api/perfis/{id}` | Detalha um perfil |
| PUT | `/api/perfis/{id}` | Atualiza um perfil |
| DELETE | `/api/perfis/{id}` | Remove um perfil |

### Endereços
| Método | Endpoint | Descrição |
|---|---|---|
| GET | `/api/enderecos` | Lista endereços (paginado) |
| POST | `/api/enderecos` | Cria novo endereço |
| GET | `/api/enderecos/{id}` | Detalha um endereço |
| PUT | `/api/enderecos/{id}` | Atualiza um endereço |
| DELETE | `/api/enderecos/{id}` | Remove um endereço |

### Usuários
| Método | Endpoint | Descrição |
|---|---|---|
| GET | `/api/usuarios` | Lista usuários (paginado, com filtros) |
| POST | `/api/usuarios` | Cria novo usuário |
| GET | `/api/usuarios/{id}` | Detalha um usuário |
| PUT | `/api/usuarios/{id}` | Atualiza um usuário |
| DELETE | `/api/usuarios/{id}` | Remove um usuário |

**Filtros disponíveis em `GET /api/usuarios`:**
- `nome` — busca parcial por nome
- `cpf` — busca parcial por CPF
- `data_inicio` / `data_fim` — intervalo de data de cadastro
- `page` — número da página

---

## Dados de Exemplo (Seeders)

Ao executar `php artisan migrate --seed`, o banco é populado com:

**Perfis:** `ADMIN`, `USER`, `GESTOR`

**Usuários:**
| Nome | CPF | Perfil |
|---|---|---|
| Fulano de Tal | 111.022.354-4 | ADMIN |
| Ciclano de Silva | 111.022.234-5 | USER |
| Maria Silva | 130.046.464-6 | USER |

**Endereços:** `Rua tal / 41950-035` e `Travessa tal / 41200-050`, já vinculados aos usuários acima.

---

## Funcionalidades Implementadas

- [x] CRUD completo de Perfis (listagem, criação, edição, exclusão)
- [x] CRUD completo de Endereços (listagem, criação, edição, exclusão)
- [x] CRUD completo de Usuários (listagem, criação, edição, exclusão, detalhe)
- [x] Relacionamento N:N entre Usuários e Endereços
- [x] Paginação e filtros na listagem de Usuários
- [x] Validações com Form Requests no backend
- [x] API Resources para padronização das respostas JSON
- [x] Autenticação via Bearer Token (plus)
- [x] Interface responsiva com Vuetify 3 / Material Design
- [x] Persistência de sessão no frontend (localStorage)
- [x] Redirecionamento automático ao expirar o token
