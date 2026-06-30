<?php

declare(strict_types=1);

return [
    'sa.national_id.required' => 'رقم الهوية مطلوب.',
    'sa.national_id.invalid_length' => 'رقم الهوية يجب أن يكون 10 أرقام بالضبط.',
    'sa.national_id.invalid_prefix' => 'رقم الهوية يجب أن يبدأ بـ 1 (مواطن) أو 2 (مقيم).',
    'sa.national_id.invalid_checksum' => 'رقم الهوية غير صحيح (خطأ في التحقق).',
    'sa.national_id.invalid' => 'رقم الهوية يجب أن يكون رقم هوية وطنية أو إقامة سعودية صحيحاً.',

    'sa.mobile.required' => 'رقم الجوال مطلوب.',
    'sa.mobile.invalid_format' => 'رقم الجوال يجب أن يكون رقماً سعودياً صحيحاً (05XXXXXXXX).',
    'sa.mobile.invalid' => 'رقم الجوال يجب أن يكون رقم جوال سعودي صحيحاً.',

    'sa.iban.required' => 'رقم الآيبان مطلوب.',
    'sa.iban.invalid_country' => 'رقم الآيبان يجب أن يبدأ بـ SA.',
    'sa.iban.invalid_length' => 'الآيبان السعودي يجب أن يكون 24 حرفاً بالضبط.',
    'sa.iban.invalid_characters' => 'رقم الآيبان يحتوي على أحرف غير صالحة.',
    'sa.iban.invalid_checksum' => 'رقم الآيبان غير صحيح (خطأ في التحقق).',
    'sa.iban.invalid' => 'رقم الآيبان يجب أن يكون آيباناً سعودياً صحيحاً.',
];
