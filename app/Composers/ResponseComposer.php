<?php declare(strict_types=1);

namespace App\Composers;

use Illuminate\Routing\Redirector;
use Illuminate\Contracts\Translation\Translator;

final class ResponseComposer
{
    public function __construct(
        public Redirector $redirect,
        public Translator $translator
    ) {}
}
