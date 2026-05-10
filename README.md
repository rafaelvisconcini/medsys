# MedSys — Sistema de Gestão para Clínicas Terapêuticas

Sistema web para gestão de clínicas multidisciplinares (TEA, TDAH, TOD), desenvolvido em Laravel 13. Cobre pacientes, agenda, prontuário eletrônico, financeiro e administração de equipe.

## Funcionalidades

- **Pacientes** — Cadastro completo com dados pessoais, responsáveis, convênios e diagnósticos (CID).
- **Agenda** — Agendamento de sessões por profissional, visualização semanal, bloqueios de horário e integração com prontuário.
- **Prontuário eletrônico** — Evoluções por especialidade, plano terapêutico, encaminhamentos e anexos (PDFs, imagens).
- **Financeiro** — Contratos, parcelas, cobranças avulsas, controle de inadimplência e exportação de relatórios em PDF.
- **Administração** — Gestão de usuários, profissionais, configuração de agenda e controle de acesso por perfil.
- **LGPD** — Armazenamento de anexos em disco privado com download autenticado; sem exposição direta de URLs.

## Perfis de Acesso

| Perfil | Acesso |
|---|---|
| Admin | Tudo, incluindo gestão de usuários e profissionais |
| Profissional | Agenda própria, prontuário dos seus pacientes, evoluções |
| Recepcionista | Pacientes, agenda (todos os profissionais), encaminhamentos |
| Financeiro | Módulo financeiro completo, relatórios PDF |

## Instalação

```bash
git clone https://github.com/rafaelvisconcini/medsys.git
cd medsys

# Dependências PHP
composer install

# Dependências JS
npm install

# Configuração de ambiente
cp .env.example .env
php artisan key:generate
```

Edite `.env` com as credenciais do banco de dados e então:

```bash
php artisan migrate --seed
npm run build
php artisan serve
```

Acesse `http://localhost:8000`. As credenciais do admin padrão são criadas pelo `AdminSeeder`.

### Filas e agendamento

```bash
php artisan queue:work --sleep=3 --tries=3   # Processamento de e-mails em background
php artisan schedule:run                      # Agendador (configurar no cron do servidor)
```

Cron recomendado para produção:
```
* * * * * cd /caminho/para/medsys && php artisan schedule:run >> /dev/null 2>&1
```

## Testes

```bash
php artisan test
```

75 testes cobrindo: controle de acesso por perfil, CRUD de pacientes/sessões/evoluções/encaminhamentos, regras de negócio financeiro (parcelas, pagamento, inadimplência), relatórios PDF e administração.

## Variáveis de Ambiente Relevantes

```env
APP_ENV=local
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite          # sqlite para dev, mysql para prod
# DB_HOST=127.0.0.1
# DB_DATABASE=medsys
# DB_USERNAME=root
# DB_PASSWORD=

FILESYSTEM_DISK=local         # Manter 'local' — anexos ficam em disco privado

MAIL_MAILER=log               # 'log' em dev, 'smtp' em prod
QUEUE_CONNECTION=database
```

## Estrutura do Projeto

```
app/
├── Enums/              # Especialidade, PerfilUsuario, StatusSessao...
├── Http/Controllers/
│   ├── Prontuario/     # EvolucaoController, PlanoController, AnexoController...
│   ├── Financeiro/     # ContratoController, CobrancaController, RelatorioController
│   └── Admin/          # AgendaConfigController, BloqueioController
├── Models/             # Paciente, Sessao, Evolucao, Contrato, Cobranca...
├── Policies/           # PacientePolicy, SessaoPolicy, EvolucaoPolicy
└── Providers/          # AuthServiceProvider (Gates)
resources/views/
├── layouts/            # app.blade.php
├── pacientes/
├── sessoes/
├── prontuarios/        # show, evolucoes/, planos/, encaminhamentos/, anexos/
├── financeiro/         # index, contratos/, relatorios/
└── admin/
database/
├── migrations/
└── seeders/
tests/Feature/          # AdminTest, ProntuarioTest, FinanceiroTest, EncaminhamentoTest...
```

## Stack

- **Backend:** Laravel 13, PHP 8.4
- **Frontend:** Bootstrap 5, Blade, Livewire 3, Vite
- **Banco de dados:** SQLite (desenvolvimento) / MySQL (produção)
- **PDF:** barryvdh/laravel-dompdf
- **Testes:** PHPUnit + Laravel HTTP tests

## Licença

Uso interno — todos os direitos reservados.
