<?php

namespace App\Modules\User\Data;

use App\Core\Data\BaseData;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\TimeZone;

class UserData extends BaseData
{
    public function __construct(
        #[Required, Max(255)]
        public readonly string $name,

        #[Required, Email]
        public readonly string $email,

        #[Min(8)]
        public readonly ?string $password = null,

        public readonly ?string $google_id = null,
        public readonly ?string $avatar_url = null,

        #[In(['es', 'en', 'pt'])]
        public readonly string $preferred_locale = 'es',

        #[TimeZone]
        public readonly string $timezone = 'America/Mexico_City',
    ) {}
}
