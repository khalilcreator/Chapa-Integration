<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ChapaPaymentController extends Controller
{
    private $secretKey;
    private $baseUrl = 'https://api.chapa.co/v1/';
    public $userId =1;
    public function __construct()
    {
        $this->secretKey = env('CHAPA_SECRET_KEY');
    }

    public function initiatePayment(Request $request)
    {
        // return 1;
        // Log::info('Chapa Secret Key:', ['secretKey' => $this->secretKey]);
        // Log::info('Env Secret Key:', ['secretKey' => env('CHAPA_SECRET_KEY')]);


        // return $this->secretKey;

        $amount = $request->amount;
        $email = $request->email;
        $firstName = $request->firstName;
        $lastName = $request->lastName;
        $phoneNumber = $request->phoneNumber; // Optional
        $txRef = uniqid('TX_', true); // Generate a unique transaction reference
        $callbackUrl = route('chapa.callback');
        $returnUrl = route('payment.success'); // Or your desired return URL

        $data = [
            'amount' => $amount,
            'currency' => 'ETB',
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone_number' => $phoneNumber,
            'tx_ref' => $txRef,
            'callback_url' => $callbackUrl,
            'return_url' => $returnUrl,
            'customization' => [
                'title' => 'Payment Order',
                'description' => 'Thank you for your order.',
            ],
        ];

         $response = $this->makeChapaApiRequest('POST', 'transaction/initialize', $data);

        if ($response && $response->successful()) {
            $responseData = $response->json();
            if ($responseData['status'] === 'success') {
                // Store transaction details in your database (e.g., txRef, amount)
                // for later verification
                return redirect($responseData['data']['checkout_url']);
            } else {
                return redirect()->back()->with('error', $responseData['message']);
            }
        } else {
            return redirect()->back()->with('error', 'Failed to initiate payment.');
        }
    }

    public function paymentCallback(Request $request)
    {
        // Log all incoming request data to check for available parameters
        Log::info('Chapa callback parameters:', $request->all());

        // Update reference to trx_ref
        $reference = $request->get('trx_ref'); // Get the transaction reference (correct parameter name)

        // Check if reference is null
        if (!$reference) {
            Log::error('No reference found in callback.');
            return view('payment.failed'); // Or any other error handling
        }

        // Log the reference value
        Log::info('Chapa reference:', ['reference' => $reference]);

        // Verify the payment status
        $response = $this->makeChapaApiRequest('GET', "transaction/verify/$reference");

        // Log the Chapa API response for debugging
        Log::info('Chapa response:', ['response' => $response]);

        if ($response && $response->successful()) {
            $responseData = $response->json();

            if ($responseData['status'] === 'success') {
                // Extract transaction details
                $transactionDetails = [
                    'transaction_id' => $responseData['data']['tx_ref'] ?? null,
                    'amount' => $responseData['data']['amount'] ?? null,
                    'currency' => $responseData['data']['currency'] ?? null,
                    'email' => $responseData['data']['email'] ?? null,
                    'first_name' => $responseData['data']['first_name'] ?? null,
                    'last_name' => $responseData['data']['last_name'] ?? null,
                    'payment_method' => $responseData['data']['payment_method'] ?? 'N/A', // Payment method
                    'status' => $responseData['data']['status'] ?? null,
                    'created_at' => $responseData['data']['created_at'] ?? null, // Payment date
                    'updated_at' => $responseData['data']['updated_at'] ?? null,
                ];
                // session([
                //     'transactionDetails' => [
                //         'transaction_id' => $responseData['data']['tx_ref'] ?? null,
                //         'amount' => $responseData['data']['amount'] ?? null,
                //         'currency' => $responseData['data']['currency'] ?? null,
                //         'email' => $responseData['data']['email'] ?? null,
                //         'first_name' => $responseData['data']['first_name'] ?? null,
                //         'last_name' => $responseData['data']['last_name'] ?? null,
                //         'payment_method' => $responseData['data']['payment_method'] ?? 'N/A',
                //         'status' => $responseData['data']['status'] ?? null,
                //         'created_at' => $responseData['data']['created_at'] ?? null,
                //         'updated_at' => $responseData['data']['updated_at'] ?? null,
                //     ],
                //     'paymentStatus' => 'success', // Add status to session
                // ]);
                $userId = 1;
                Cache::put('user_' . $userId . '_transaction', $transactionDetails, now()->addMinutes(10));
                Log::info('Session Data in Callback:', session()->all());
                // return 'transactionDetails'.$transactionDetails;
                // return 'responseData'.$responseData;
                // Return the payment success view directly with the transaction details
                // return redirect()->route('payment.success', $transactionDetails);

            } else {
                // return view('payment.failed');
            }
        } else {
            Log::error('Chapa verification failed:', ['response' => $response]);
            return view('payment.failed');
        }
    }

    private function makeChapaApiRequest($method, $endpoint, $data = [])
    {
        $url = $this->baseUrl . $endpoint;
        $headers = [
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ];

        try {
            $response = Http::withOptions([
                'verify' => false, // Disable SSL verification
            ])->withHeaders($headers)->{$method}($url, $data);

            return $response;
        } catch (\Exception $e) {
            Log::error('Chapa Error:', ['message' => $e->getMessage()]);
            return null;
        }
    }
    // public function success(Request $request)
    // {

    //     // Fetch payment status from session
    //     $paymentStatus = session('paymentStatus', 'failed'); // Default to 'failed'

    //     // Fetch transaction details if status is success
    //     $transactionDetails = [];
    //     if ($paymentStatus === 'success') {
    //         $transactionDetails = session('transactionDetails', []);
    //     }
    //     Log::info('Session Data Before Redirect:', session()->all());
    //     Log::info('request:', $request->all());

    //     Log::info('details:', ['details' =>$transactionDetails]);
    //     Log::info('paymentStatus:', ['paymentStatus' => $paymentStatus]);
    //     // session()->forget(['transactionDetails', 'paymentStatus']);

    //     // Show success or failed view based on status
    //     if ($paymentStatus === 'success') {
    //         return view('payment.success', compact('transactionDetails'));
    //     } else {
    //         return view('payment.failed'); // Render failed page if status is not success
    //     }
    // }
    public function success(Request $request)
    {
        $userId = 1;

        // Fetch transaction details from cache
        $transactionDetails = Cache::get('user_' . $userId . '_transaction');

        // Check if data exists in cache
        if ($transactionDetails) {
            // Use the retrieved data
            return view('payment.success', compact('transactionDetails'));
        } else {
            // Handle the case where data is not found in cache
            return view('payment.failed');
        }
    }
    // public function success(Request $request)
    // {
    //     $transactionDetails = $request->all(); // This will retrieve the data from the query parameters

    //     // You can now pass these details to the view as well
    //     return view('payment.success', compact('transactionDetails'));
    // }

    // Display failed page after payment failure
    public function failed()
    {
        return view('payment.failed');
    }
    public function test(){
        return 'test';
    }
    public function getAllTransactions()
    {
        $allTransactions = [];
        $nextPageUrl = 'https://api.chapa.co/v1/transactions?page=1'; // Start with the first page

        while ($nextPageUrl) {
            // Make the API request to the next page
            $response = Http::withOptions([
                'verify' => false, // Disable SSL verification
            ])->withHeaders([
                'Authorization' => 'Bearer ' . env('CHAPA_SECRET_KEY')
            ])->get($nextPageUrl);

            // Check if the response was successful
            if ($response->successful()) {
                // Append the transactions from this page to the allTransactions array
                $transactions = $response->json()['data']['transactions'];
                $allTransactions = array_merge($allTransactions, $transactions);

                // Get the next page URL from the response
                $nextPageUrl = $response->json()['data']['pagination']['next_page_url'];
            } else {
                // If the response fails, log the error and break out of the loop
                Log::error('Failed to fetch transaction data', ['response' => $response->body()]);
                return redirect()->back()->with('error', 'Failed to fetch transaction data.');
            }
        }

        // Return the transactions to the view
        return view('index', compact('allTransactions'));
    }
}
