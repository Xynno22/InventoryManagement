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
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class profitlossController extends Controller
{
    public function salesReport(Request $request)
    {
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

            // Query data cash flow
            $query = TransactionDetail::with(['product', 'transaction.status', 'transaction.payment'])
                ->whereHas('transaction', function ($q) use ($startDate, $endDate, $paymentId, $statusId) {
                    $q->whereBetween('date', [$startDate, $endDate]);

                    if ($paymentId) {
                        $q->where('payment_id', $paymentId);
                    }

                    if ($statusId) {
                        $q->where('status_id', $statusId);
                    }
                });

            // Operational Costs Query
            $operationalCosts = OperationalCost::with('payment')
                ->whereBetween('date', [$startDate, $endDate]);

            if ($paymentId) {
                $operationalCosts->where('payment_id', $paymentId);
            }

            // Fetch operational costs
            $operationalCosts = $operationalCosts->get();

            // Map Operational Costs data
            $operationalData = $operationalCosts->map(function ($op) {
                return [
                    'date' => Carbon::parse($op->date)->format('Y-m-d'),
                    'product_name' => '-', // Tidak ada produk di operasional
                    'note' => $op->note,
                    'cash_in' => 0,
                    'cash_out' => $op->amount, // Pengeluaran
                    'payment_method' => $op->payment->name ?? '-',
                    'status' => 'success', // Bisa juga simpan note di sini
                ];
            });

            // Query Transaction Details
            $transactions = $query->get()->map(function ($detail) {
                $type = $detail->transaction->type_id;
                $note = '';
                if ($type == 2) {
                    $note = 'Purchase ' . $detail->product->name . ' x' . $detail->quantity;
                } elseif ($type == 1) {
                    $note = 'Sale ' . $detail->product->name . ' x' . $detail->quantity;
                }
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

    // Gabungkan transaksi dengan biaya operasional
    $transactions = $transactions->concat($operationalData);

    $totalCashIn = $transactions->sum('cash_in');
    $totalCashOut = $transactions->sum('cash_out');

    return view('report.profitloss', compact(
        'transactions', 'startDate', 'endDate',
        'payments', 'paymentId', 'statuses', 'statusId', 'totalCashIn', 'totalCashOut'
    ));
}



    public function exportPDF(Request $request)
    {
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
                ->whereHas('transaction', function ($q) use ($startDate, $endDate, $paymentId, $statusId) {
                    $q->whereBetween('date', [$startDate, $endDate]);

                    if ($paymentId) {
                        $q->where('payment_id', $paymentId);
                    }

                    if ($statusId) {
                        $q->where('status_id', $statusId);
                    }
                });

            $operationalCosts = OperationalCost::with('payment')
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

        $pdf = FacadePdf::loadView('report.profitlossPDF', compact(
            'transactions', 'startDate', 'endDate', 'totalCashIn', 'totalCashOut', 'netCashFlow'
        ));

        $filename = 'Profit Loss Statement_' . now()->format('d-m-Y_H-i') . '.pdf';

        return $pdf->download($filename);
    }


    public function exportExcel(Request $request)
    {
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
                ->whereHas('transaction', function ($q) use ($startDate, $endDate, $paymentId, $statusId) {
                    $q->whereBetween('date', [$startDate, $endDate]);

                    if ($paymentId) {
                        $q->where('payment_id', $paymentId);
                    }

                    if ($statusId) {
                        $q->where('status_id', $statusId);
                    }
                });

            $operationalCosts = OperationalCost::with('payment')
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
                // Hitung total cash in, cash out, dan net cash flow
        $totalCashIn = $transactions->sum('cash_in');
        $totalCashOut = $transactions->sum('cash_out');
        $netCashFlow = $totalCashIn - $totalCashOut;

        // Baris kosong
        $row++;

        // Total Cash In
        $sheet->setCellValue("F$row", 'Total Cash In');
        $sheet->setCellValue("G$row", $totalCashIn);
        $row++;

        // Total Cash Out
        $sheet->setCellValue("F$row", 'Total Cash Out');
        $sheet->setCellValue("G$row", $totalCashOut);
        $row++;

        // Profit
        $sheet->setCellValue("F$row", 'Profit');
        $sheet->setCellValue("G$row", $netCashFlow);

        // Apply styling ke area F:G untuk 3 baris terakhir
        $summaryStart = $row - 2;
        $summaryEnd = $row;

        $styleRange = "F{$summaryStart}:G{$summaryEnd}";

        $sheet->getStyle($styleRange)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F2F2F2'], // abu-abu terang
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Format angka di kolom G dengan ribuan
        $sheet->getStyle("G{$summaryStart}:G{$summaryEnd}")
            ->getNumberFormat()->setFormatCode('#,##0');

        

        // Style tebal untuk total
        $sheet->getStyle("C" . ($row - 2) . ":F$row")->getFont()->setBold(true);


        $sheet->getStyle('B2:C' . ($row - 1))->getAlignment()->setWrapText(true);

        $filename = 'Profit Loss Statement_' . now()->format('d-m-Y_H-i') . '.xlsx';

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
