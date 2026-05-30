<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesExport implements FromCollection, WithHeadings
{
    protected $sales;

    public function __construct($sales)
    {
        $this->sales = $sales;
    }

    public function collection()
    {
        return $this->sales->map(function ($sale) {
            return [
                'Invoice No' => $sale->invoice_no,
                'Customer' => $sale->customer->name ?? 'Walk-in',
                'Date' => $sale->sale_date->format('Y-m-d H:i'),
                'Subtotal' => $sale->subtotal,
                'Discount' => $sale->item_discount + $sale->order_discount,
                'Tax' => $sale->tax_amount,
                'Total' => $sale->total_amount,
                'Paid' => $sale->paid_amount,
                'Due' => $sale->due_amount,
                'Payment Method' => $sale->payment_method,
                'Status' => $sale->status,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Invoice No',
            'Customer',
            'Date',
            'Subtotal',
            'Discount',
            'Tax',
            'Total',
            'Paid',
            'Due',
            'Payment Method',
            'Status',
        ];
    }
}
