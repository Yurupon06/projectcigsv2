<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\User;
use App\Models\Member;
use App\Models\Order;
use App\Models\OrderComplement;
use App\Models\ApplicationSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Mpdf\Mpdf;

class ReportController extends Controller
{
    public function index(Request $request){
        $app = ApplicationSetting::first();
        $filter = $request->input('filter', 'Hari');
        
        $startDate = Carbon::today();
        $endDate = Carbon::today();
    
        if ($filter == 'Hari') {
            $startDate = Carbon::yesterday();
            $endDate = Carbon::now();
        } elseif ($filter == 'Minggu') {
            $startDate = Carbon::now()->subWeek();
            $endDate = Carbon::now();
        } elseif ($filter == 'Bulan') {
            $startDate = Carbon::now()->subMonth(); 
            $endDate = Carbon::now();
        }
    
        $allDates = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $allDates[$currentDate->format('Y-m-d')] = 0; 
            $currentDate->addDay();
        }
    
        $payments = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total_payment')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total_payment', 'date')
            ->toArray(); 
    
        
        $users = User::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total_user')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total_user', 'date')
            ->toArray(); 
    
        $members = Member::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total_member')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total_member', 'date')
            ->toArray(); 

        $orders = Order::where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total_order')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total_order', 'date')
            ->toArray(); 

        $orderComplements = OrderComplement::where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total_order_complement')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total_order_complement', 'date')
            ->toArray(); 
    
        $paymentData = array_merge($allDates, $payments); 
        $userData = array_merge($allDates, $users); 
        $memberData = array_merge($allDates, $members); 
        $orderData = array_merge($allDates, $orders); 
        $orderComplementData = array_merge($allDates, $orderComplements); 
    
        $chartData = [
            'type' => 'line',
            'data' => [
                'labels' => array_keys($allDates), 
                'datasets' => [
                    [
                        'label' => 'Payments',
                        'data' => array_values($paymentData),
                        'borderColor' => 'rgba(75, 192, 192, 1)',
                        'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                        
                        
                    ],
                    [
                        'label' => 'Order Member',
                        'data' => array_values($orderData),
                        'borderColor' => 'rgba(245, 40, 145, 1)',
                        'backgroundColor' => 'rgba(245, 40, 145, 0.2)',
                        
                        
                    ],
                    [
                        'label' => 'Order Complement',
                        'data' => array_values($orderComplementData),
                        'borderColor' => 'rgba(175, 140, 221, 1)',
                        'backgroundColor' => 'rgba(175, 140, 221, 0.2)',
                        
                        
                    ],
                    [
                        'label' => 'Users',
                        'data' => array_values($userData),
                        'borderColor' => 'rgba(54, 162, 235, 1)',
                        'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                        
                        
                    ],
                    [
                        'label' => 'Members',
                        'data' => array_values($memberData),
                        'borderColor' => 'rgba(255, 206, 86, 1)',
                        'backgroundColor' => 'rgba(255, 206, 86, 0.2)',
                        
                        
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                
                'scales' => [
                    'x' => [
                        'title' => [
                            'display' => true,
                        ],
                        
                    ],
                    'y' => [
                        'title' => [
                            'display' => true,
                        ],
                        'beginAtZero' => true
                    ]
                ]
            ]
        ];
    
        $chartDataJson = json_encode($chartData);

        $quickChartUrl = "https://quickchart.io/chart?c=" . urlencode($chartDataJson);

        return view('report-admin.index', compact('quickChartUrl', 'app', 'filter', 'startDate', 'endDate', 'chartDataJson'));
    }

    public function report(Request $request) {
        $filter = $request->input('filter', 'Hari'); // Ambil filter dari request

        $startDate = Carbon::today(); 
        $endDate = Carbon::today();  
    
        // Menentukan data berdasarkan filter
        if ($filter == 'Hari') {
            $payments = Payment::whereDate('created_at', Carbon::today())->get();
            $user = User::whereDate('created_at', Carbon::today())->get();
            $member = Member::whereDate('created_at', Carbon::today())->get();
            $startDate = Carbon::yesterday()->format('d-m-Y');
            $endDate = Carbon::today()->format('d-m-Y');
        } elseif ($filter == 'Minggu') {
            $payments = Payment::whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()])->get();
            $user = User::whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()])->get();
            $member = Member::whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()])->get();
            $startDate = Carbon::now()->subWeek()->format('d-m-Y');
            $endDate = Carbon::now()->format('d-m-Y');
        } elseif ($filter == 'Bulan') {
            $payments = Payment::whereMonth('created_at', Carbon::now()->month)->get();
            $user = User::whereMonth('created_at', Carbon::now()->month)->get();
            $member = Member::whereMonth('created_at', Carbon::now()->month)->get();
            $startDate = Carbon::now()->startOfMonth()->format('d-m-Y');
            $endDate = Carbon::now()->format('d-m-Y');
        }
    
        // Menghitung total
        $totalMember = $member->count();
        $totalUser = $user->count();
        $total = $payments->sum('amount');
        $totalAmount = number_format($total);
        $totalPayment = $payments->count();
    
        // Menggabungkan pesan

    
        // Menyimpan PDF
        $pdfPath = $this->generatePdf($filter);
        // dd($pdfPath); // Mengambil path PDF yang dihasilkan
        $adminUsers = User::where('role', 'admin')->get(); 
        if ($adminUsers->isNotEmpty()) {
            try {
                foreach ($adminUsers as $admin) {
                    if ($admin->phone) { 
                        $phone = $admin->phone;
                        $app = ApplicationSetting::first();
                        $message = "Data *$app->app_name* pada *$filter* ini dari *$startDate* - *$endDate* :\n" .
                        "Total sales : *Rp $totalAmount*\n" .
                        "New user : *$totalUser*\n" .
                        "New member : *$totalMember*\n" .
                        "Here is your report:";
        
                        $apiText = Http::baseUrl($app->japati_url)
                        ->withToken($app->japati_token)
                        ->post('/api/send-message', [
                            'gateway' => $app->japati_gateway,
                            'number' => $phone,
                            'type' => 'text',
                            'message' => $message,
                        ]);
    
                        $apiCustomer = Http::baseUrl($app->japati_url)
                        ->withToken($app->japati_token)
                        ->post('/api/send-message', [
                            'gateway' => $app->japati_gateway,
                            'number' => $phone,
                            'type' => 'media',
                            'message' => $message, // Pesan yang menyertakan semua informasi
                            'media_file' => $pdfPath,
                        ]);
                    }
                }
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'Something went wrong, try again later.');
            }
        } else {
            return redirect()->back()->with('error', 'No admin users found.');
        }
        

        if (!$apiCustomer->ok()) {
            logger($apiCustomer->json());
            return redirect()->back()->with('error', 'Failed to send message');
        }
    
        return redirect()->route('report.index')->with('success', 'Message and report sent successfully!');
    }
    
    
    


     public function generateChartData($filter) {
        $app = ApplicationSetting::first();
        $startDate = Carbon::today();
        $endDate = Carbon::today();
    
        if ($filter == 'Hari') {
            $startDate = Carbon::yesterday();
            $endDate = Carbon::now();
        } elseif ($filter == 'Minggu') {
            $startDate = Carbon::now()->subWeek();
            $endDate = Carbon::now();
        } elseif ($filter == 'Bulan') {
            $startDate = Carbon::now()->subMonth(); 
            $endDate = Carbon::now();
        }
    
        $allDates = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $allDates[$currentDate->format('Y-m-d')] = 0; 
            $currentDate->addDay();
        }
    
        $payments = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total_payment')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total_payment', 'date')
            ->toArray(); 
    
        $users = User::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total_user')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total_user', 'date')
            ->toArray(); 
    
        $members = Member::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total_member')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total_member', 'date')
            ->toArray(); 
    
        $orders = Order::where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total_order')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total_order', 'date')
            ->toArray(); 
    
        $orderComplements = OrderComplement::where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total_order_complement')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total_order_complement', 'date')
            ->toArray(); 
    
        $paymentData = array_merge($allDates, $payments); 
        $userData = array_merge($allDates, $users); 
        $memberData = array_merge($allDates, $members); 
        $orderData = array_merge($allDates, $orders); 
        $orderComplementData = array_merge($allDates, $orderComplements); 
    
        return [
            'chartData' => [
                'type' => 'line',
                'data' => [
                    'labels' => array_keys($allDates), 
                    'datasets' => [
                        [
                            'label' => 'Payments',
                            'data' => array_values($paymentData),
                            'borderColor' => 'rgba(75, 192, 192, 1)',
                            'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                        ],
                        [
                            'label' => 'Order Member',
                            'data' => array_values($orderData),
                            'borderColor' => 'rgba(245, 40, 145, 1)',
                            'backgroundColor' => 'rgba(245, 40, 145, 0.2)',
                        ],
                        [
                            'label' => 'Order Complement',
                            'data' => array_values($orderComplementData),
                            'borderColor' => 'rgba(175, 140, 221, 1)',
                            'backgroundColor' => 'rgba(175, 140, 221, 0.2)',
                        ],
                        [
                            'label' => 'Users',
                            'data' => array_values($userData),
                            'borderColor' => 'rgba(54, 162, 235, 1)',
                            'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                        ],
                        [
                            'label' => 'Members',
                            'data' => array_values($memberData),
                            'borderColor' => 'rgba(255, 206, 86, 1)',
                            'backgroundColor' => 'rgba(255, 206, 86, 0.2)',
                        ]
                    ]
                ],
                'options' => [
                    'responsive' => true,
                    'scales' => [
                        'x' => [
                            'title' => [
                                'display' => true,
                                'text' => 'Tanggal'
                            ],
                        ],
                        'y' => [
                            'title' => [
                                'display' => true,
                            ],
                            'beginAtZero' => true
                        ]
                    ]
                ]
            ],
            'app' => $app,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'paymentData' => $paymentData,
            'userData' => $userData,
            'memberData' => $memberData,
            'orderData' => $orderData,
            'orderComplementData' => $orderComplementData
        ];
    }


    public function generatePdf($filter = 'Bulan') {
        // $filter sudah diterima sebagai parameter
        $chartDataResponse = $this->generateChartData($filter);
        $chartDataJson = json_encode($chartDataResponse['chartData']);
        $quickChartUrl = "https://quickchart.io/chart?c=" . urlencode($chartDataJson);
        
        $html = view('report-admin.pdf', array_merge($chartDataResponse, compact('quickChartUrl', 'filter')))->render();
    
        // Tentukan path untuk menyimpan PDF
        $pdfDirectory = public_path('storage/reports');
        if (!file_exists($pdfDirectory)) {
            mkdir($pdfDirectory, 0755, true);
        }

        $pdfName = '/Report_' . now()->format('YmdHis') . '.pdf';
    
        $pdfPath = $pdfDirectory . $pdfName;
    
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        
        // Simpan PDF ke file
        $mpdf->Output($pdfPath, 'F');
    
        // Streaming PDF ke browser
        // $mpdf->Output('Report_' . now()->format('YmdHis') . '.pdf', 'I');
    
        // URL untuk mengakses file PDF (jika diperlukan)
        return asset('storage/reports'. $pdfName);
        
    }
    
    
    
    
    
    
    
    
    

    
    
    
    
    
}
