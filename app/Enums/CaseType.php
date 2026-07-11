<?php

declare(strict_types=1);

namespace App\Enums;

enum CaseType: string
{
    case Verzuim = 'verzuim';
    case ReintegratieSpoor1 = 're_integratie_spoor_1';
    case ReintegratieSpoor2 = 're_integratie_spoor_2';
    case Pmo = 'pmo';
    case Aanstellingskeuring = 'aanstellingskeuring';
    case Consult = 'consult';
    case Preventief = 'preventief';

    public function label(): string
    {
        return match ($this) {
            self::Verzuim => 'Ziekteverzuim',
            self::ReintegratieSpoor1 => 'Re-integratie spoor 1',
            self::ReintegratieSpoor2 => 'Re-integratie spoor 2',
            self::Pmo => 'Preventief Medisch Onderzoek',
            self::Aanstellingskeuring => 'Aanstellingskeuring',
            self::Consult => 'Open spreekuur / Consult',
            self::Preventief => 'Preventief dossier',
        };
    }

    /** Whether the employer portal should see cases of this type. */
    public function employerVisible(): bool
    {
        return match ($this) {
            self::Verzuim, self::ReintegratieSpoor1, self::ReintegratieSpoor2 => true,
            default => false,
        };
    }

    /** Whether the Wet Verbetering Poortwachter reintegration timeline applies to this case type. */
    public function hasReintegrationMilestones(): bool
    {
        return match ($this) {
            self::Verzuim, self::ReintegratieSpoor1, self::ReintegratieSpoor2 => true,
            default => false,
        };
    }
}
