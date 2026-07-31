# Campos Soluções Veicular — Sistema Integrado

Sistema de gestão veicular integrado. Esta primeira entrega cobre a **Base** do sistema
e o módulo de **Análise e Aquisição de Veículos** (o núcleo do sistema).

> Identidade própria. O sistema Auto Alívio foi usado apenas como referência de lógica/fluxo —
> nenhum código, texto, marca ou identidade visual foi copiado.

## O que já está pronto nesta entrega

### Base do sistema
- Login seguro (senhas criptografadas)
- **8 níveis de acesso**: Administrador, Analista, Financeiro, Atendimento, Locação,
  Manutenção, Vistoria e Consulta — cada um com permissões próprias
- **Log de auditoria**: registra automaticamente quem criou/alterou/excluiu cada
  registro, com data e horário
- **Painel principal** com indicadores de análises, frota, clientes e valores
- Layout responsivo (funciona no computador e no celular)

### Módulo de Análise e Aquisição
- Cadastro de **clientes** e de **bancos/financeiras** (com % estimado de desconto para quitação)
- Cadastro completo da análise: veículo, valor FIPE, banco, parcelas (pagas/atrasadas/restantes),
  valor da parcela, dívida bruta, quitação, multas, IPVA, valor solicitado pelo cliente,
  comissão, transporte, manutenção estimada e demais custos
- **Cálculo automático** (em tempo real na tela):
  - Custo total da aquisição
  - Percentual em relação à FIPE
  - Margem estimada (R$ e %)
  - Valor máximo recomendado para proposta
  - Indicação de **viabilidade** do negócio (viável / não viável)
- Status, histórico de movimentações, observações
- Anexos: fotos e documentos
- **Relatório em PDF** da análise
- **Converter análise em veículo da frota** — aproveita automaticamente todos os dados cadastrados

## Tecnologias
- PHP 8.3 + Laravel 11
- Banco de dados: SQLite (desenvolvimento) — pronto para MySQL/PostgreSQL em produção
- Tailwind CSS + Alpine.js (interface responsiva)
- DomPDF (relatórios) · spatie/laravel-permission (perfis de acesso)

## Como rodar localmente

```bash
composer install
npm install && npm run build

cp .env.example .env          # se ainda não existir
php artisan key:generate

php artisan migrate --seed    # cria as tabelas e dados de exemplo
php artisan storage:link      # habilita anexos

php artisan serve
```

Acesse http://127.0.0.1:8000

### Usuários de exemplo (criados pelo seed)
| Perfil        | E-mail                          | Senha       |
|---------------|---------------------------------|-------------|
| Administrador | admin@camposveicular.com.br     | admin123    |
| Analista      | analista@camposveicular.com.br  | analista123 |

> Troque essas senhas em produção.

## Usando MySQL em produção
No arquivo `.env`, troque a conexão:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=campos_veicular
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```
Depois rode `php artisan migrate --seed`. As migrações são compatíveis com MySQL/PostgreSQL.

## Próximas etapas do projeto
2. Gestão de Frota e Locação (contratos, cobranças, manutenção, vistorias, sinistros, rentabilidade)
3. Área do Locatário (PWA)
4. Integrações futuras (FIPE, WhatsApp, Pix, assinatura eletrônica, nota fiscal, rastreadores)
