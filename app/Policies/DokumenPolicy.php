<?php

namespace App\Policies;

use App\Enums\StatusDokumen;
use App\Enums\StatusPendaftaran;
use App\Models\Dokumen;
use App\Models\Pendaftaran;
use App\Models\User;

class DokumenPolicy
{
    public function view(User $user, Dokumen $dokumen): bool
    {
        return $user->isAdmin() || $this->owner($user, $dokumen);
    }

    public function create(User $user, Pendaftaran $pendaftaran): bool
    {
        return $pendaftaran->calonSiswa?->user_id === $user->id
            && $this->editable($pendaftaran);
    }

    public function update(User $user, Dokumen $dokumen): bool
    {
        return $this->owner($user, $dokumen)
            && $this->editable($dokumen->pendaftaran)
            && $dokumen->status !== StatusDokumen::APPROVED;
    }

    public function delete(User $user, Dokumen $dokumen): bool
    {
        return $this->update($user, $dokumen);
    }

    private function owner(User $user, Dokumen $dokumen): bool
    {
        return $dokumen->pendaftaran?->calonSiswa?->user_id === $user->id;
    }

    private function editable(?Pendaftaran $pendaftaran): bool
    {
        return $pendaftaran !== null
            && in_array($pendaftaran->status, [
                StatusPendaftaran::DRAFT,
                StatusPendaftaran::REJECTED,
                StatusPendaftaran::KUOTA_PENUH,
            ], true);
    }
}
