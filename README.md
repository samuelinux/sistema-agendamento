# Sistema de Agendamento de Horários

Sistema completo de agendamento desenvolvido em Laravel 11 com autenticação por celular, modais interativos e painel administrativo.

## 🚀 Características

- **Autenticação Simples**: Login apenas com número de celular
- **Interface Moderna**: Design responsivo com TailwindCSS
- **Modais Interativos**: Seleção de serviços, dias e horários
- **Painel Administrativo**: Gerenciamento completo do sistema
- **Validação Inteligente**: Impede agendamentos no passado
- **Múltiplos Agendamentos**: Permite vários horários no mesmo dia

## 📋 Funcionalidades

### Para Clientes
- Login rápido com celular
- Seleção de serviços disponíveis
- Escolha de dias com vagas
- Seleção de horários livres
- Visualização de agendamentos

### Para Administradores
- Dashboard com estatísticas
- Gerenciamento de serviços
- Configuração de horários disponíveis
- Controle de horário de almoço
- Visualização de agendamentos

## 🛠️ Tecnologias Utilizadas

- **Backend**: Laravel 11
- **Frontend**: TailwindCSS + Alpine.js
- **Banco de Dados**: MySQL/MariaDB
- **Autenticação**: Sistema customizado por celular

## 📦 Instalação

### Pré-requisitos
- PHP 8.1 ou superior
- Composer
- MySQL/MariaDB
- Node.js (opcional)

### Passos de Instalação

1. **Clone o projeto**
```bash
git clone <url-do-repositorio>
cd agendamento-sistema
```

2. **Instale as dependências**
```bash
composer install
```

3. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure o banco de dados**
Edite o arquivo `.env` com suas credenciais:
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=agendamento_db
DB_USERNAME=agendamento_user
DB_PASSWORD=senha123
```

5. **Execute as migrations e seeders**
```bash
php artisan migrate:fresh --seed
```

6. **Inicie o servidor**
```bash
php artisan serve
```

## 👥 Usuários de Teste

O sistema vem com usuários pré-configurados:

### Administrador
- **Celular**: 11999999999
- **Nome**: Administrador
- **Acesso**: Painel admin completo

### Cliente
- **Celular**: 11988888888
- **Nome**: João Silva
- **Acesso**: Sistema de agendamento

## 🗂️ Estrutura do Banco de Dados

### Tabelas Principais

- **users**: Usuários do sistema
- **servicos**: Serviços oferecidos
- **horarios_disponiveis**: Horários de funcionamento
- **agendamentos**: Agendamentos realizados
- **configuracoes**: Configurações do sistema

## 🎯 Como Usar

### Acesso Cliente
1. Acesse a página inicial
2. Digite seu número de celular
3. Se for novo usuário, informe seu nome
4. Selecione o serviço desejado
5. Escolha o dia disponível
6. Selecione o horário livre
7. Confirme o agendamento

### Acesso Admin
1. Faça login com celular de admin
2. Acesse `/admin` ou clique no painel
3. Gerencie serviços, horários e configurações
4. Visualize estatísticas no dashboard

## ⚙️ Configurações

### Horários de Funcionamento
- Segunda a Sexta: 8:00-12:00 e 14:00-18:00
- Sábado: 8:00-16:00
- Domingo: Fechado

### Horário de Almoço
- Início: 12:00
- Fim: 14:00

## 🔧 Personalização

### Adicionando Novos Serviços
1. Acesse o painel admin
2. Vá em "Serviços" > "Novo Serviço"
3. Preencha nome, descrição, preço e duração
4. Ative o serviço

### Configurando Horários
1. Acesse "Horários" no painel admin
2. Configure dias da semana e horários
3. Defina horários de almoço nas configurações

## 📱 Responsividade

O sistema é totalmente responsivo e funciona em:
- Desktop
- Tablets
- Smartphones

## 🔒 Segurança

- Proteção CSRF em todos os formulários
- Middleware de autenticação
- Validação de dados no backend
- Sanitização de entradas

## 🐛 Solução de Problemas

### Erro 419 - Page Expired
- Limpe o cache: `php artisan cache:clear`
- Regenere a chave: `php artisan key:generate`

### Problemas de Banco
- Verifique as credenciais no `.env`
- Execute: `php artisan migrate:fresh --seed`

### Problemas de Permissão
```bash
chown -R www-data:www-data /var/www/sistema_de_agendamento/storage /var/www/sistema_de_agendamento/bootstrap/cache && chmod -R 775 /var/www/sistema_de_agendamento/storage /var/www/sistema_de_agendamento/bootstrap/cache

```

## 📞 Suporte

Para suporte técnico ou dúvidas sobre o sistema, consulte a documentação ou entre em contato com o desenvolvedor.

## 📄 Licença

Este projeto é proprietário e desenvolvido especificamente para o cliente.

---

**Desenvolvido com ❤️ usando Laravel 11**
