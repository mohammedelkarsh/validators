<?php

declare(strict_types=1);

namespace Validators\Sa;

enum IdentityType: string
{
    case Citizen = 'citizen';
    case Resident = 'resident';
}
