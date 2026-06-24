<?php

namespace App\Http\Controllers;

use App\Models\Permit;
use App\Models\ReminderLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Download laporan permit sebagai PDF.
     */
    public function permitPdf(Request $request)
    {
        $query = Permit::query();

        $filterParts = [];

        if ($request->filled('status')) {
            $query->where('status', $request->status);
            $filterParts[] = 'Status: ' . ucfirst($request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', 'like', '%' . $request->type . '%');
            $filterParts[] = 'Type: ' . $request->type;
        }

        if ($request->filled('expires_from')) {
            $query->whereDate('expires_at', '>=', $request->expires_from);
            $filterParts[] = 'Expires from: ' . $request->expires_from;
        }

        if ($request->filled('expires_until')) {
            $query->whereDate('expires_at', '<=', $request->expires_until);
            $filterParts[] = 'Expires until: ' . $request->expires_until;
        }

        $permits    = $query->orderBy('expires_at', 'asc')->get();
        $filterInfo = implode(' | ', $filterParts);

        $pdf = Pdf::loadView('reports.permits-pdf', compact('permits', 'filterInfo'))
            ->setPaper('a4', 'landscape');

        $filename = 'Laporan_Permit_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Download laporan reminder log sebagai PDF.
     */
    public function reminderLogPdf(Request $request)
    {
        $query = ReminderLog::query();

        $filterParts = [];

        if ($request->filled('entity')) {
            $query->where('entity', $request->entity);
            $filterParts[] = 'Entity: ' . ucfirst($request->entity);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
            $filterParts[] = 'Status: ' . ucfirst($request->status);
        }

        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->created_from);
            $filterParts[] = 'From: ' . $request->created_from;
        }

        if ($request->filled('created_until')) {
            $query->whereDate('created_at', '<=', $request->created_until);
            $filterParts[] = 'Until: ' . $request->created_until;
        }

        $logs       = $query->orderBy('created_at', 'desc')->get();
        $filterInfo = implode(' | ', $filterParts);

        $pdf = Pdf::loadView('reports.reminder-logs-pdf', compact('logs', 'filterInfo'))
            ->setPaper('a4', 'landscape');

        $filename = 'Laporan_ReminderLog_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }
    /**
     * Download laporan karyawan sebagai PDF.
     */
    public function employeePdf(Request $request)
    {
        $query = \App\Models\Employee::query();

        $filterParts = [];

        if ($request->filled('is_permanent')) {
            $isPermanent = $request->is_permanent == '1';
            $query->where('is_permanent', $isPermanent);
            $filterParts[] = 'Status: ' . ($isPermanent ? 'Permanent' : 'Contract');
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
            $filterParts[] = 'Name: ' . $request->name;
        }

        if ($request->filled('nip')) {
            $query->where('nip', 'like', '%' . $request->nip . '%');
            $filterParts[] = 'NIP: ' . $request->nip;
        }

        if ($request->filled('contract_end_from')) {
            $query->whereDate('contract_end', '>=', $request->contract_end_from);
            $filterParts[] = 'Contract End from: ' . $request->contract_end_from;
        }

        if ($request->filled('contract_end_until')) {
            $query->whereDate('contract_end', '<=', $request->contract_end_until);
            $filterParts[] = 'Contract End until: ' . $request->contract_end_until;
        }

        $employees  = $query->orderBy('name', 'asc')->get();
        $filterInfo = implode(' | ', $filterParts);

        $pdf = Pdf::loadView('reports.employees-pdf', compact('employees', 'filterInfo'))
            ->setPaper('a4', 'landscape');

        $filename = 'Laporan_Karyawan_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }
}
