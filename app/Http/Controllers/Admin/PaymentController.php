<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Enum\MessageTypeEnum;
use App\Http\Controllers\Enum\OrderStatusEnum;
use App\Http\Controllers\Enum\PaymentStatusEnum;
use App\Model\SellerPayment;
use App\Model\SubOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class PaymentController extends Controller
{
    // Payment List
    public function paymentList(){
        $month = date('m');
        $year = date('Y');

        if(!empty($_GET['month'])){
            $date_explode = explode('-',$_GET['month']);
            $month = $date_explode[0];
            $year = $date_explode[1];
        }

        $payments = SubOrder::where('status',OrderStatusEnum::CLAIMED)
            ->select(
                'sub_orders.*'
                ,DB::raw('count(*) as sub_order_count')
                ,DB::raw("group_concat(sub_orders.id SEPARATOR ',') as sub_order_id")
            )
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->orderBy('sub_orders.created_at','desc')
            ->groupBy('seller_id')
            ->get();

        return view('/admin/payment/list')->with('payments',$payments);
    }

    // Payment Final List
    public function paymentFinalList(Request $request){

        $status = PaymentStatusEnum::PENDING;
        if ($request->status != null) $status = $request->status;

//        dd($status);

        $month = date('m');
        $year = date('Y');

        if(!empty($_GET['month'])){
            $date_explode = explode('-',$_GET['month']);
            $month = $date_explode[0];
            $year = $date_explode[1];
        }

        $payments = SellerPayment::orderBy('created_at','desc')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', $status)
            ->get();



        return view('/admin/payment/final-list')->with('payments',$payments)->with('status', $status);
    }

    // Payment Finalized
    public function paymentFinalized(Request $request){
        try{
            DB::beginTransaction();




            $seller_payment = new SellerPayment();
            $seller_payment->seller_id = decrypt($request->seller_id);
            $seller_payment->amount = decrypt($request->amount_paid);
            $seller_payment->commission_charged = decrypt($request->commission);
            $seller_payment->status = PaymentStatusEnum::PENDING;
            $seller_payment->payment_for_the_month = date('Y-m-d',strtotime('30-'.decrypt($request->payment_month)));
            $seller_payment->save();


            if(isset($seller_payment) && !empty(decrypt($request->sub_orders))){
                $sub_order_ids = explode(',',decrypt($request->sub_orders));
                foreach($sub_order_ids as $sub_order_id){
                    $sub_order = SubOrder::find($sub_order_id);
                    $sub_order->seller_payment_id = $seller_payment->id;
                    $sub_order->status = OrderStatusEnum::FINALIZED;
                    $sub_order->save();

                }
            }


            DB::commit();
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . 'Payment Successfull.');
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }



    // Payment Final Status
    public function paymentFinalStatus(Request $request){
        try{
            DB::beginTransaction();
            $payment = SellerPayment::find($request->payment_id);
            $payment->status = $request->status;
            $payment->comment = $request->comment;
            $payment->save();
            DB::commit();
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::SUCCESS . 'Updated Successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . $e->getMessage());
        }
    }

    // statistics
    public function statistics()
    {
      $month = date('m');
      $year = date('Y');

      if(!empty($_GET['month'])){
          $date_explode = explode('-',$_GET['month']);
          $month = $date_explode[0];
          $year = $date_explode[1];
      }

      $payments = SellerPayment::whereMonth('created_at', $month)
          ->whereYear('created_at', $year)->get();

      return view('/admin/payment/statistics')->with('payments',$payments);
    }

    // Payment Export
    public function paymentExport(Request $request)
    {
        $month = 0;
        $year = 0;

        if(!empty($request->export)){
            $date_explode = explode('-',$request->export);
            $month = $date_explode[0];
            $year = $date_explode[1];
        }

        $payments = SellerPayment::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)->get();



        $pdf_html = '';

        if(!empty($payments[0])) {

          $pdf_html .= '<style>
          .table { border-collapse: collapse; }
          .table th,
          .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #999;
          }

          .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #999;
          }

          .table tbody + tbody {
            border-top: 2px solid #999;
          }

          .table .table {
            background-color: #fff;
          }


          .table-bordered {
            border: 1px solid #999;
          }

          .table-bordered th,
          .table-bordered td {
            border: 1px solid #999;
          }

          .table-bordered thead th,
          .table-bordered thead td {
            border-bottom-width: 2px;
          }
          </style>';

          $pdf_html .= '<h4 style="margin: 0 0 5px;">Final Payment List</h4>';
          $pdf_html .= '<table class="table table-bordered table-hover">
              <thead>
              <tr>
                  <th width="50">Orders ID</th>
                  <th width="200">Seller</th>
                  <th width="100">Amount</th>
                  <th width="80">Commission</th>
                  <th width="130">No. of Sub Orders</th>
                  <th width="200">Comment</th>
                  <th width="100">Status</th>
              </thead>
              <tbody>';


                foreach($payments as $payment) {
                    $orders = '';
                    $first = 0;
                    $sub_orders = $payment->getPaymentSubOrder;
                    foreach($sub_orders as $sub_order){
                        if($first == 0){
                            $orders = $sub_order->order_id;
                            $first = 1;
                        }
                        else {
                            $orders .= ', '.$sub_order->order_id;
                        }
                    }


                  $pdf_html .= '<tr>';
                    $pdf_html .= '<td>'.$orders.'</td>';
                      $pdf_html .= '<td class="tabledit-view-mode">
                          <a href="'.url('admin/product/seller/'.$payment->getSeller->id.'/product/list').'">'.$payment->getSeller->getUser->username.'</a>
                      </td>';
                      $pdf_html .= '<td>'.env('CURRENCY_SYMBOL').number_format($payment->amount,2).'</td>';
                      $pdf_html .= '<td>'.env('CURRENCY_SYMBOL').number_format($payment->commission_charged,2).'</td>';
                      $pdf_html .= '<td class="text-center">'.$payment->getPaymentSubOrder->count().'</td>';
                      $pdf_html .= '<td>'.$payment->comment.'</td>';
                      $pdf_html .= '<td>';
                              if($payment->status == PaymentStatusEnum::PENDING) $pdf_html .= 'Pending';
                              elseif($payment->status == PaymentStatusEnum::REJECTED) $pdf_html .= 'Rejected';
                              elseif($payment->status == PaymentStatusEnum::COMPLETED) $pdf_html .= 'Completed';
                          $pdf_html .= '</td>
                  </tr>';
                }


              $pdf_html .= '</tbody>
          </table>';

      }
      else {
        return redirect()->back()->with('TOASTR_MESSAGE', MessageTypeEnum::ERROR . 'Nothing to export');
      }

      $mpdf = new \mPDF();
      $mpdf->WriteHTML($pdf_html);
      $mpdf->Output($request->export.'-payment-final.pdf','D');

    }
}
