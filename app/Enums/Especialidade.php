<?php

namespace App\Enums;

enum Especialidade: string
{
    case Fisioterapia       = 'fisioterapia';
    case Fonoaudiologia     = 'fonoaudiologia';
    case Psicologia         = 'psicologia';
    case Psicopedagogia     = 'psicopedagogia';
    case TerapiaOcupacional = 'terapia_ocupacional';

    public function label(): string
    {
        return match($this) {
            self::Fisioterapia       => 'Fisioterapia',
            self::Fonoaudiologia     => 'Fonoaudiologia',
            self::Psicologia         => 'Psicologia',
            self::Psicopedagogia     => 'Psicopedagogia',
            self::TerapiaOcupacional => 'Terapia Ocupacional',
        };
    }

    public function registro(): string
    {
        return match($this) {
            self::Fisioterapia       => 'CREFITO',
            self::Fonoaudiologia     => 'CRFa',
            self::Psicologia         => 'CRP',
            self::Psicopedagogia     => 'ABPp',
            self::TerapiaOcupacional => 'CREFITO',
        };
    }
}
