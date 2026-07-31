<?php

namespace App\Support;

/**
 * Niveis de acesso do sistema (8 perfis) e as permissoes associadas.
 * Usado pelo seeder e pelas policies/middleware.
 */
class Roles
{
    public const ADMIN       = 'admin';
    public const ANALISTA    = 'analista';
    public const FINANCEIRO  = 'financeiro';
    public const ATENDIMENTO = 'atendimento';
    public const LOCACAO     = 'locacao';
    public const MANUTENCAO  = 'manutencao';
    public const VISTORIA    = 'vistoria';
    public const CONSULTA    = 'consulta';

    public const LABELS = [
        self::ADMIN       => 'Administrador',
        self::ANALISTA    => 'Analista',
        self::FINANCEIRO  => 'Financeiro',
        self::ATENDIMENTO => 'Atendimento',
        self::LOCACAO     => 'Locação',
        self::MANUTENCAO  => 'Manutenção',
        self::VISTORIA    => 'Vistoria',
        self::CONSULTA    => 'Consulta',
    ];

    /** Permissoes disponiveis no sistema (fase atual: base + analise/aquisicao). */
    public const PERMISSIONS = [
        'analyses.view',
        'analyses.manage',
        'clients.view',
        'clients.manage',
        'banks.view',
        'banks.manage',
        'fleet.view',
        'fleet.manage',
        'users.manage',
        'audit.view',
    ];

    /** Mapa de permissoes por perfil. */
    public static function permissionsFor(string $role): array
    {
        return match ($role) {
            self::ADMIN => self::PERMISSIONS, // acesso total
            self::ANALISTA => [
                'analyses.view', 'analyses.manage',
                'clients.view', 'clients.manage',
                'banks.view', 'fleet.view',
            ],
            self::FINANCEIRO => [
                'analyses.view', 'clients.view',
                'banks.view', 'banks.manage', 'fleet.view',
            ],
            self::ATENDIMENTO => [
                'analyses.view', 'clients.view', 'clients.manage',
            ],
            self::LOCACAO => [
                'fleet.view', 'fleet.manage', 'clients.view', 'analyses.view',
            ],
            self::MANUTENCAO => [
                'fleet.view',
            ],
            self::VISTORIA => [
                'fleet.view',
            ],
            self::CONSULTA => [
                'analyses.view', 'clients.view', 'banks.view', 'fleet.view',
            ],
            default => [],
        };
    }
}
