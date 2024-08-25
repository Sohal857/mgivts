<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class VehicleDataController extends Controller
{
    public function index()
    {
        // Get all table names
        $tables = DB::select('SHOW TABLES');
        
        // Get the database name dynamically
        $databaseName = DB::getDatabaseName();

        $latestData = [];

        foreach ($tables as $table) {
            // The table name is in the form of `Tables_in_databaseName`
            $tableName = $table->{'Tables_in_' . $databaseName};

            // Skip any system or unrelated tables if necessary
            if (preg_match('/^\d{10,}$/', $tableName)) {
                // Escape the table name with backticks
                $result = DB::select("
                    SELECT *
                    FROM `$tableName`
                    ORDER BY time DESC
                    LIMIT 1
                ");

                if (!empty($result)) {
                    $latestData[] = $result[0];
                }
            }
        }

        // Log the latest data for debugging
        Log::info('Latest Vehicle Data: ', ['latestData' => $latestData]);

        return view('vehicle-data.index', ['latestData' => $latestData]);
    }
}