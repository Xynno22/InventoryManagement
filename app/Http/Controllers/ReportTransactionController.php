<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Type; 
use App\Models\Status;
use App\Models\Payment;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Http\Request;


class ReportTransactionController extends Controller
{
    public function salesReport(Request $request)
    {

        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $typeId    = $request->input('type_id');
        $customer  = $request->input('customer');
        $paymentId = $request->input('payment_id');
        $statusId  = $request->input('status_id');

        $transactions = collect();

        // Get all types, payments, and statuses for filter selects
        $types = Type::all();
        $payments = Payment::all();
        $statuses = Status::all();

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate)->endOfDay();

            $query = Transaction::with(['payment', 'status']);

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
        // Get all the filter inputs
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $typeId    = $request->input('type_id');
        $customer  = $request->input('customer');
        $paymentId = $request->input('payment_id');
        $statusId  = $request->input('status_id');
    
        // Get transactions based on filters
        $transactions = Transaction::with(['payment', 'status']); // No need to initialize collect()
    
        // Apply filters if they are provided
        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate);  // Ensure it is parsed correctly
            $endDate = Carbon::parse($endDate)->endOfDay();  // Ensure it includes the whole end day
    
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
    
}