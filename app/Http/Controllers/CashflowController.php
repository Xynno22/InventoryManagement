<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Status;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\OperationalCost;
use App\Models\TransactionDetail;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class CashflowController extends Controller
{
    public function salesReport(Request $request)
    {
        // Cek siapa yang login
        if (auth('company')->check()) {
            $companyId = auth('company')->id();
        } elseif (auth('web')->check()) {
            $companyId = auth('web')->user()->company_id;
        } else {
            return abort(403, 'Unauthorized');
        }

        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $paymentId = $request->input('payment_id');
        $statusId  = $request->input('status_id');

        $transactions = collect();
        $operationalData = collect();
        $payments = Payment::all();
        $statuses = Status::all();

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate)->endOfDay();

            // 🔒 Query Transaction Details dengan validasi company_id
            $query = TransactionDetail::with(['product', 'transaction.status', 'transaction.payment'])
                ->whereHas('transaction', function ($q) use ($startDate, $endDate, $paymentId, $statusId, $companyId) {
                    $q->where('company_id', $companyId) // ✅ Validasi company
                    ->whereBetween('date', [$startDate, $endDate]);

                    if ($paymentId) {
                        $q->where('payment_id', $paymentId);
                    }

                    if ($statusId) {
                        $q->where('status_id', $statusId);
                    }
                });

            // 🔒 Query Operational Costs dengan validasi company_id
            $operationalCosts = OperationalCost::with('payment')
                ->where('company_id', $companyId) // ✅ Validasi company
                ->whereBetween('date', [$startDate, $endDate]);

            if ($paymentId) {
                $operationalCosts->where('payment_id', $paymentId);
            }

            // Map Operational Costs
            $operationalData = $operationalCosts->get()->map(function ($op) {
                return [
                    'date' => Carbon::parse($op->date)->format('Y-m-d'),
                    'product_name' => '-', // Tidak ada produk
                    'note' => $op->note,
                    'cash_in' => 0,
                    'cash_out' => $op->amount,
                    'payment_method' => $op->payment->name ?? '-',
                    'status' => 'success',
                ];
            });

            // Map Transaction Details
            $transactions = $query->get()->map(function ($detail) {
                $type = $detail->transaction->type_id;
                $note = $type == 2
                    ? 'Purchase ' . $detail->product->name . ' x' . $detail->quantity
                    : 'Sale ' . $detail->product->name . ' x' . $detail->quantity;

                return [
                    'date' => Carbon::parse($detail->transaction->date)->format('Y-m-d'),
                    'product_name' => $detail->product->name,
                    'note' => $note,
                    'cash_in' => $type == 1 ? $detail->price * $detail->quantity : 0,
                    'cash_out' => $type == 2 ? $detail->price * $detail->quantity : 0,
                    'payment_method' => $detail->transaction->payment->name ?? '-',
                    'status' => $detail->transaction->status->name ?? '-',
                ];
            });
        }

        // Gabungkan transaksi dan biaya operasional
        $transactions = $transactions->concat($operationalData);
        $totalCashIn = $transactions->sum('cash_in');
        $totalCashOut = $transactions->sum('cash_out');

        return view('report.cashflow', compact(
            'transactions', 'startDate', 'endDate',
            'payments', 'paymentId', 'statuses', 'statusId',
            'totalCashIn', 'totalCashOut'
        ));
    }




    public function exportPDF(Request $request)
    {

              // Cek siapa yang login
        if (auth('company')->check()) {
            $companyId = auth('company')->id();
        } elseif (auth('web')->check()) {
            $companyId = auth('web')->user()->company_id;
        } else {
            return abort(403, 'Unauthorized');
        }
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $paymentId = $request->input('payment_id');
        $statusId  = $request->input('status_id');

        $transactions = collect();
        $operationalData = collect();

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate)->endOfDay();

            $query = TransactionDetail::with(['product', 'transaction.status', 'transaction.payment'])
                ->whereHas('transaction', function ($q) use ($startDate, $endDate, $paymentId, $statusId, $companyId) {
                    $q->where('company_id', $companyId) // ✅ Validasi company
                    ->whereBetween('date', [$startDate, $endDate]);;

                    if ($paymentId) {
                        $q->where('payment_id', $paymentId);
                    }

                    if ($statusId) {
                        $q->where('status_id', $statusId);
                    }
                });

            $operationalCosts = OperationalCost::with('payment')
                ->where('company_id', $companyId) // ✅ Validasi company
                ->whereBetween('date', [$startDate, $endDate]);

            if ($paymentId) {
                $operationalCosts->where('payment_id', $paymentId);
            }

            $operationalCosts = $operationalCosts->get();

            $operationalData = $operationalCosts->map(function ($op) {
                return [
                    'date' => Carbon::parse($op->date)->format('Y-m-d'),
                    'product_name' => '-',
                    'note' => $op->note,
                    'cash_in' => 0,
                    'cash_out' => $op->amount,
                    'payment_method' => $op->payment->name ?? '-',
                    'status' => 'success',
                ];
            });

            $transactions = $query->get()->map(function ($detail) {
                $type = $detail->transaction->type_id;
                $note = $type == 2
                    ? 'Purchase ' . $detail->product->name . ' x' . $detail->quantity
                    : 'Sale ' . $detail->product->name . ' x' . $detail->quantity;
                return [
                    'date' => Carbon::parse($detail->transaction->date)->format('Y-m-d'),
                    'product_name' => $detail->product->name,
                    'note' => $note,
                    'cash_in' => $type == 1 ? $detail->price * $detail->quantity : 0,
                    'cash_out' => $type == 2 ? $detail->price * $detail->quantity : 0,
                    'payment_method' => $detail->transaction->payment->name ?? '-',
                    'status' => $detail->transaction->status->name ?? '-',
                ];
            });
        }

        $transactions = $transactions->concat($operationalData);
        $totalCashIn = $transactions->sum('cash_in');
        $totalCashOut = $transactions->sum('cash_out');
        $netCashFlow = $totalCashIn - $totalCashOut;

        $pdf = FacadePdf::loadView('report.cashflow-pdf', compact(
            'transactions', 'startDate', 'endDate', 'totalCashIn', 'totalCashOut', 'netCashFlow'
        ));

        $filename = 'cashflow_report_' . now()->format('d-m-Y_H-i') . '.pdf';

        return $pdf->download($filename);
    }


    public function exportExcel(Request $request)
    {
              // Cek siapa yang login
        if (auth('company')->check()) {
            $companyId = auth('company')->id();
        } elseif (auth('web')->check()) {
            $companyId = auth('web')->user()->company_id;
        } else {
            return abort(403, 'Unauthorized');
        }
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $paymentId = $request->input('payment_id');
        $statusId  = $request->input('status_id');

        $transactions = collect();
        $operationalData = collect();

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate)->endOfDay();

            $query = TransactionDetail::with(['product', 'transaction.status', 'transaction.payment'])
                ->whereHas('transaction', function ($q) use ($startDate, $endDate, $paymentId, $statusId, $companyId) {
                    $q->where('company_id', $companyId) // ✅ Validasi company
                    ->whereBetween('date', [$startDate, $endDate]);

                    if ($paymentId) {
                        $q->where('payment_id', $paymentId);
                    }

                    if ($statusId) {
                        $q->where('status_id', $statusId);
                    }
                });

            $operationalCosts = OperationalCost::with('payment')
                ->where('company_id', $companyId) // ✅ Validasi company
                ->whereBetween('date', [$startDate, $endDate]);

            if ($paymentId) {
                $operationalCosts->where('payment_id', $paymentId);
            }

            $operationalCosts = $operationalCosts->get();

            $operationalData = $operationalCosts->map(function ($op) {
                return [
                    'date' => Carbon::parse($op->date)->format('Y-m-d'),
                    'product_name' => '-',
                    'note' => $op->note,
                    'cash_in' => 0,
                    'cash_out' => $op->amount,
                    'payment_method' => $op->payment->name ?? '-',
                    'status' => 'success',
                ];
            });

            $transactions = $query->get()->map(function ($detail) {
                $type = $detail->transaction->type_id;
                $note = $type == 2
                    ? 'Purchase ' . $detail->product->name . ' x' . $detail->quantity
                    : 'Sale ' . $detail->product->name . ' x' . $detail->quantity;
                return [
                    'date' => Carbon::parse($detail->transaction->date)->format('Y-m-d'),
                    'product_name' => $detail->product->name,
                    'note' => $note,
                    'cash_in' => $type == 1 ? $detail->price * $detail->quantity : 0,
                    'cash_out' => $type == 2 ? $detail->price * $detail->quantity : 0,
                    'payment_method' => $detail->transaction->payment->name ?? '-',
                    'status' => $detail->transaction->status->name ?? '-',
                ];
            });
        }

        $transactions = $transactions->concat($operationalData);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Date')
            ->setCellValue('B1', 'Product Name')
            ->setCellValue('C1', 'Note')
            ->setCellValue('D1', 'Cash In')
            ->setCellValue('E1', 'Cash Out')
            ->setCellValue('F1', 'Payment')
            ->setCellValue('G1', 'Status');

        $sheet->getStyle('A1:G1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A1:G1')->getFill()->getStartColor()->setRGB('D3D3D3');

        $sheet->getColumnDimension('A')->setWidth(15);  // Adjust column 'A' width
        $sheet->getColumnDimension('B')->setWidth(30);  // Adjust column 'B' width
        $sheet->getColumnDimension('C')->setWidth(30);  // Adjust column 'C' width
        $sheet->getColumnDimension('D')->setWidth(15);  // Adjust column 'D' width
        $sheet->getColumnDimension('E')->setWidth(15);  // Adjust column 'E' width
        $sheet->getColumnDimension('F')->setWidth(20);  // Adjust column 'F' width
        $sheet->getColumnDimension('G')->setWidth(20);  // Adjust column 'G' width
        $row = 2;
        foreach ($transactions as $tx) {
            $sheet->setCellValue("A$row", $tx['date'])
                ->setCellValue("B$row", $tx['product_name'])
                ->setCellValue("C$row", $tx['note'])
                ->setCellValue("D$row", $tx['cash_in'])
                ->setCellValue("E$row", $tx['cash_out'])
                ->setCellValue("F$row", $tx['payment_method'])
                ->setCellValue("G$row", $tx['status']);
            $row++;
        }

        $sheet->getStyle('B2:C' . ($row - 1))->getAlignment()->setWrapText(true);

        $filename = 'cashflow_report_' . now()->format('d-m-Y_H-i') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

}
