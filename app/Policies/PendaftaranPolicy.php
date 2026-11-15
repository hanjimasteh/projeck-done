<?php

namespace App\Policies;

use App\Enums\StatusPendaftaran;
use App\Models\Pendaftaran;
use App\Models\User;

class PendaftaranPolicy
{
    public function view(User $user, Pendaftaran $pendaftaran): bool
    {
        return $pendaftaran->calonSiswa?->user_id === $user->id;
    }

    public function update(User $user, Pendaftaran $pendaftaran): bool
    {
        return $this->view($user, $pendaftaran)
            && in_array($pendaftaran->status, [
                StatusPendaftaran::DRAFT,
                StatusPendaftaran::REJECTED,
                StatusPendaftaran::KUOTA_PENUH,
            ], true);
    }

    public function submit(User $user, Pendaftaran $pendaftaran): bool
    {
        return $this->update($user, $pendaftaran);
    }
}
