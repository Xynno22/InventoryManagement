<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Type; 
use App\Models\Status;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class ReportTransactionController extends Controller
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
        $typeId    = $request->input('type_id');
        $customer  = $request->input('customer');
        $paymentId = $request->input('payment_id');
        $statusId  = $request->input('status_id');

        $transactions = collect();

        $types = Type::all();
        $payments = Payment::all();
        $statuses = Status::all();

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate)->endOfDay();

            $query = Transaction::with(['payment', 'status'])
                ->where('company_id', $companyId); // 🔒 Tambahkan validasi company

            if ($typeId) {
                $query->where('type_id', $typeId);
            }

            if ($customer) {
                $query->where('customer_name', 'LIKE', "%$customer%");
            }

            if ($paymentId) {
                $query->where('payment_id', $paymentId);
            }

            if ($statusId) {
                $query->where('status_id', $statusId);
            }

            $transactions = $query->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->get();
        }

        return view('report.sales', compact(
            'transactions', 'startDate', 'endDate', 'typeId', 'types',
            'customer', 'payments', 'paymentId', 'statuses', 'statusId'
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
        $typeId    = $request->input('type_id');
        $customer  = $request->input('customer');
        $paymentId = $request->input('payment_id');
        $statusId  = $request->input('status_id');
    
        // Get transactions based on filters
        $transactions = Transaction::with(['payment', 'status'])->where('company_id', $companyId);
    
        // Apply filters if they are provided
        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate)->endOfDay();
            $transactions = $transactions->whereBetween('date', [$startDate, $endDate]);
        }
    
        if ($typeId) {
            $transactions->where('type_id', $typeId);
        }
    
        if ($customer) {
            $transactions->where('customer_name', 'LIKE', "%$customer%");
        }
    
        if ($paymentId) {
            $transactions->where('payment_id', $paymentId);
        }
    
        if ($statusId) {
            $transactions->where('status_id', $statusId);
        }
    
        // Fetch the transactions
        $transactions = $transactions->orderBy('date', 'desc')->get();
    
        // Format the start and end dates for the filename
        $startDateFormatted = $startDate->format('d-m-Y');
        $endDateFormatted = $endDate->format('d-m-Y');
    
        // Create the filename dynamically
        $filename = 'transaction_report_' . $startDateFormatted . '_to_' . $endDateFormatted . '.pdf';
    
        // Load the PDF view with the filtered data
        $pdf = FacadePdf::loadView('report.sales-pdf', compact(
            'transactions', 'startDate', 'endDate', 'typeId', 'customer', 'paymentId', 'statusId'
        ));
    
        // Return the PDF as a download with a dynamic filename
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
        $typeId    = $request->input('type_id');
        $customer  = $request->input('customer');
        $paymentId = $request->input('payment_id');
        $statusId  = $request->input('status_id');

        $query = Transaction::with(['payment', 'status'])->where('company_id', $companyId);

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        if ($typeId) {
            $query->where('type_id', $typeId);
        }

        if ($customer) {
            $query->where('customer_name', 'LIKE', "%$customer%");
        }

        if ($paymentId) {
            $query->where('payment_id', $paymentId);
        }

        if ($statusId) {
            $query->where('status_id', $statusId);
        }

        $transactions = $query->orderBy('date', 'desc')->get();

        // Create a new Spreadsheet instance
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set the headings in the first row
        $sheet->setCellValue('A1', 'Date')
            ->setCellValue('B1', 'Invoice')
            ->setCellValue('C1', 'Customer')
            ->setCellValue('D1', 'Total')
            ->setCellValue('E1', 'Payment')
            ->setCellValue('F1', 'Status');

            $sheet->getStyle('A1:F1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
            $sheet->getStyle('A1:F1')->getFill()->getStartColor()->setRGB('D3D3D3');  // Light gray color

        // Adjust column widths to make cells larger
        $sheet->getColumnDimension('A')->setWidth(20);  // Adjust column 'A' width
        $sheet->getColumnDimension('B')->setWidth(20);  // Adjust column 'B' width
        $sheet->getColumnDimension('C')->setWidth(30);  // Adjust column 'C' width
        $sheet->getColumnDimension('D')->setWidth(15);  // Adjust column 'D' width
        $sheet->getColumnDimension('E')->setWidth(20);  // Adjust column 'E' width
        $sheet->getColumnDimension('F')->setWidth(20);  // Adjust column 'F' width

        // Populate the data rows starting from row 2
        $row = 2;
        foreach ($transactions as $tx) {
            $formattedPrice = 'Rp ' . number_format($tx->total_price, 0, ',', '.');

            $sheet->setCellValue("A$row", Carbon::parse($tx->date)->format('d/m/Y H:i'))
                ->setCellValue("B$row", $tx->voucher_code)
                ->setCellValue("C$row", $tx->customer_name)
                ->setCellValue("D$row", $formattedPrice)
                ->setCellValue("E$row", $tx->payment->name ?? '-')
                ->setCellValue("F$row", $tx->status->name ?? '-');
                $sheet->getStyle("D$row")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $row++;
        }

        // Generate the filename
        $filename = 'transaction_report_' . now()->format('d-m-Y_H-i') . '.xlsx';

        // Create a Writer instance and save the file
        $writer = new Xlsx($spreadsheet);

        // Save the file directly to memory and return it as a download
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
