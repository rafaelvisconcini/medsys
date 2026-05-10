# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project: MedSys

Patient management system for a therapeutic center (TEA/TDAH/TOD children).
Laravel 13, PHP 8.4, SQLite (dev) / MySQL (prod), Bootstrap 5, Livewire 3, Vite.

## Commands

```bash
# Development server (port 8080)
php8.4 artisan serve --port=8080

# Build frontend assets (Node.js v22 required — use: export PATH=/usr/local/bin:$PATH)
npm run build

# Run migrations
php8.4 artisan migrate

# Seed admin user (Lucylady Visconcini)
php8.4 artisan db:seed --class=AdminSeeder

# Process queued jobs (emails)
php8.4 artisan queue:work --sleep=3 --tries=3

# Send reminders manually (runs automatically at 18h via scheduler)
php8.4 artisan sessoes:lembretes

# Run scheduler (requires cron: * * * * * php artisan schedule:run)
php8.4 artisan schedule:run
```

## Architecture

### Auth & Roles
- `PerfilUsuario` enum: `admin`, `profissional`, `recepcionista`, `financeiro`
- Only `admin` (Lucylady) can access profissional/user management and agenda config
- `ForcarTrocaSenha` middleware redirects to `/senha/trocar` on first login
- Gates: `admin`, `acessar-financeiro`, `ver-prontuario`, `agendar`, `gerenciar-pacientes`
- Policies: `PacientePolicy`, `SessaoPolicy`, `EvolucaoPolicy` (row-level isolation)

### Key Models & Table Names
Models with non-obvious table names (Laravel can't auto-pluralize Portuguese):
- `Sessao` → `sessoes`
- `Profissional` → `profissionais`
- `AgendaConfiguracao` → `agenda_configuracoes`
- `AgendaBloqueio` → `agenda_bloqueios`
- `ContaReceber` → `contas_receber`

### Enums
- `Especialidade`: fisioterapia, fonoaudiologia, psicologia, psicopedagogia, terapia_ocupacional
- `PerfilUsuario`: admin, profissional, recepcionista, financeiro

### Email / Queue
- `MAIL_MAILER=log` in dev (logs to `storage/logs/laravel.log`) — change to `smtp` in prod
- `QUEUE_CONNECTION=database` — jobs table already migrated
- `SessaoObserver` fires `SessaoConfirmadaMail` on session create or status→confirmada
- `sessoes:lembretes` command sends `SessaoLembreteMail` for next-day sessions (scheduled 18h)
- Emails only sent if `paciente.responsavel_email` is filled

### Prontuário
- Auto-created on patient registration: number = `{YEAR}{ID_PADDED_6}`
- Each professional sees only their own `Evolucao` entries (EvolucaoPolicy)
- Financeiro sees only patient name + responsible contact (PacientePolicy + show-financeiro view)

### AgendaService
- `horarioLivre()` uses PHP-level overlap check (compatible with SQLite + MySQL)
- `gerarSlots()` respects `agenda_configuracoes` (per-weekday) and `agenda_bloqueios` (date ranges)

### Node.js
Node v22 is installed at `/usr/local/bin/node` via `n`. Must `export PATH=/usr/local/bin:$PATH` before npm commands if using a shell that defaults to system Node.
