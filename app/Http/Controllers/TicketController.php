<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\DealersCtaConfigTemp;
use Illuminate\Support\Facades\File;

class TicketController extends Controller
{
    public function catalogue()
    {
        // $connection = session('db_connection', 'mysql');
        $ctaImages = DB::connection('mysql2')->table('dealers_cta_config')->select('button_image_url')->whereNotNull('button_image_url')->where('button_image_url', '!=', '')->groupBy('button_image_url')->paginate(9);
        
       
        return view('catalogue', compact('ctaImages'));
    }

    public function getWebsiteLink(Request $request)
    {
        $conn = session('db_connection', 'mysql'); // default 'mysql'
        $dealerCode = $request->get('dealer_code');

        // Example: Replace with your real DB lookup logic
        $dealer = DB::connection($conn)->table('fca_ore_dealer_info')->where('dlr_code', $dealerCode)->first();

        if ($dealer) {
            return response()->json(['website_link' => $dealer->dlr_web_addr]);
        } else {
            return response()->json(['website_link' => null]);
        }
    }


    public function saveConfig(Request $request)
    {
        $data = $request->all();

        DealersCtaConfigTemp::updateOrCreate(
            ['dealer_code' => $request->dealer_code],
            $data
        );

        return response()->json(['success' => true]);
    }

    public function upload_custom_part_image(Request $request) {
        $DealerCode = \Session::get('DealerCode');
        $names = [];    
        $totalParts = $request->input('total_parts');
    
        for ($i = 0; $i < $totalParts; $i++) {
            if ($request->hasFile("image.$i")) {
                $image = $request->file("image.$i");
                $filename = time() . '-' . $image->getClientOriginalName();
                $uploadedFile = Storage::disk('s3-accessories')->putFileAs($this->filepath, $image, $filename);
                array_push($names, $filename);
            } else {
                array_push($names, "");
            }
        }
        // $logDetails=array('dealer_code'=>$DealerCode,'tiles_module'=>'Custom Accessories','function'=>'add');
        // $audit = $this->auditlog($logDetails);
        return $names;
    }

    public function uploadSvgSingle(Request $request)
{
    $request->validate([
        'dealer_code' => 'required|integer',
        'button_text' => 'required|string',
        'svg'         => 'required|string',
        // 'border_radius' => 'nullable|integer' // Remove, not needed
    ]);

    $dealerCode = $request->dealer_code;
    $buttonText = preg_replace('/[^A-Za-z0-9_\-]/', '', $request->button_text);

    // Save SVG as received, do not modify border radius
    $svg = $request->svg;

    // File name
    $fileName = "{$buttonText}_{$dealerCode}.svg";

    // Upload to S3
    Storage::disk('s3')->put("inwidget/images/{$fileName}", $svg, [
        'ContentType' => 'image/svg+xml',
        'visibility' => 'public'
    ]);

    $fileUrl = "https://d1jougtdqdwy1v.cloudfront.net/inwidget/images/{$fileName}";

    // Save URL in all three columns
    DB::table('dealers_cta_config_temp')->updateOrInsert(
        ['dealer_code' => $dealerCode],
        [
            'button_text'           => $request->button_text,
            'button_image_url'      => $fileUrl,
            'button_image_url_vdp'  => $fileUrl,
            'button_image_url_cpov' => $fileUrl,
            'updated_at'            => now(),
            'created_at'            => now()
        ]
    );

    return response()->json([
        'success' => true,
        'file_url' => $fileUrl
    ]);
}


  public function uploadSvgTemp(Request $request)
    {

        if (!session()->has('db_connection')) {
            session(['db_connection' => 'mysql2']); // Default to E-Shop US
        }
\Log::info("Upload SVG Temp called.");
        

        // return view('dashboard');
        $connection = session('db_connection', 'mysql2');

        $request->validate([
            'dealer_code' => 'required|integer',
            'svg'         => 'required|string',
            // 'border_radius' => 'nullable|integer' // Remove, not needed
        ]);
\Log::info("Validation passed.");
        $dealerCode = $request->dealer_code;
        $buttonText = isset($request->button_text) ? preg_replace('/[^A-Za-z0-9_\-]/', '', $request->button_text) : '';

        // Save SVG as received, do not modify border radius
        $svg = $request->svg;

        // File path to store locally (public folder)
        $fileName = "{$buttonText}_{$dealerCode}.svg";
        $filePath = public_path("uploads/{$fileName}");

        // Make sure uploads folder exists
        if (!File::exists(public_path('uploads'))) {
            File::makeDirectory(public_path('uploads'), 0755, true);
        }
\Log::info("Uploading SVG for Dealer Code: {$dealerCode}, Button Text: {$buttonText}");
        // Save SVG locally
        File::put($filePath, $svg);

        // Path to store in DB
        $dbPath = url("uploads/{$fileName}"); // e.g., http://yourapp.test/uploads/Hai_60911.svg

        // Save URL in table
        DB::connection($connection)->table('dealers_cta_config_temp')->updateOrInsert(
            ['dealer_code' => $dealerCode],
            [
                'button_text'           => $request->button_text ? $request->button_text : '',
                'button_image_url'      => $dbPath,
                'button_image_url_vdp'  => $dbPath,
                'button_image_url_cpov' => $dbPath,
                'updated_at'            => now(),
                'created_at'            => now()
            ]
        );
    \Log::info("SVG uploaded and DB updated successfully.");

        return response()->json([
            'success' => true,
            'file_url' => $dbPath
        ]);
    }

    
}
