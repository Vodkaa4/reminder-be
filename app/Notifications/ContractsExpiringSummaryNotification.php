<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class ContractsExpiringSummaryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Collection $critical;
    public array $warnings;

    /**
     * @param Collection $critical
     * @param array $warnings Array of grouped collections, e.g. [ 30 => Collection, 15 => Collection ]
     */
    public function __construct(Collection $critical, array $warnings)
    {
        $this->critical = $critical;
        $this->warnings = $warnings;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('📋 Laporan Status Kontrak Karyawan - PT Eksonindo MPI')
            ->greeting('Halo HRD,')
            ->line('Berikut adalah rekap harian untuk kontrak karyawan yang mendekati atau telah melewati masa kadaluarsa berdasarkan aturan sistem:')
            ->line('');

        // Critical (<= 7)
        $mail->line('🔴 **KONTRAK KRITIS (≤ 7 Hari / Sudah Habis):**');
        if ($this->critical->isEmpty()) {
            $mail->line('- ✅ Aman (Tidak ada)');
        } else {
            foreach ($this->critical as $emp) {
                $days = today()->diffInDays($emp->contract_end, false);
                $statusText = $days < 0 ? "Sudah lewat " . abs($days) . " hari" : "Sisa {$days} hari";
                $mail->line("- **{$emp->name}** (NIP: {$emp->nip}) - Berakhir: {$emp->contract_end->format('d F Y')} ({$statusText})");
            }
        }
        $mail->line('');

        // Loop over dynamic warnings
        // Key represents the "days_before" rule.
        ksort($this->warnings);
        foreach ($this->warnings as $days => $collection) {
            if ($collection->isNotEmpty()) {
                $mail->line("🟡 **PERINGATAN {$days} HARI:**");
                foreach ($collection as $emp) {
                    $mail->line("- **{$emp->name}** (NIP: {$emp->nip}) - Berakhir: {$emp->contract_end->format('d F Y')}");
                }
                $mail->line('');
            }
        }

        $mail->line('📝 **Tindakan yang diperlukan:**')
            ->line('- Pekerja di kategori KRITIS harus segera diproses perpanjangannya karena sistem akan terus mengirim notifikasi tiap hari sampai statusnya diperbarui.')
            ->line('- Update tanggal kontrak di sistem admin jika diperpanjang.');

        return $mail->action('Buka Dashboard Admin', config('app.url').'/admin')
            ->line('')
            ->line('Terima kasih.')
            ->line('')
            ->line('*Email otomatis pengingat Kontrak Karyawan - Sistem PT Eksonindo MPI.*');
    }
}
