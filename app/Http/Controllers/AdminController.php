<?php

namespace App\Http\Controllers;

use App\Models\User;

use App\Models\Hobby;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\StreamedResponse;

// use App\Exports\UsersExport;
// use App\Imports\UsersImport;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::with('hobbies')->get();
        return view('admin.dashboard', compact('users'));
    }

    public function exportCsv()
    {
        $users = User::with('hobbies')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Username');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Mobile');
        $sheet->setCellValue('E1', 'Role');
        $sheet->setCellValue('F1', 'Hobbies');
        $sheet->setCellValue('G1', 'Profile Image');

        // Add data
        $row = 2;
        foreach ($users as $user) {
            $hobbies = $user->hobbies->pluck('hobby')->implode(', ');

            $sheet->setCellValue('A' . $row, $user->id);
            $sheet->setCellValue('B' . $row, $user->username);
            $sheet->setCellValue('C' . $row, $user->email);
            $sheet->setCellValue('D' . $row, $user->mobile);
            $sheet->setCellValue('E' . $row, $user->role);
            $sheet->setCellValue('F' . $row, $hobbies);
            $sheet->setCellValue('G' . $row, $user->profile_image);
            $row++;
        }

        // Export as CSV
        $writer = new Csv($spreadsheet);
        $fileName = 'users.csv';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment;filename="' . $fileName . '"',
        ]);
    }

    public function uploadCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathName());
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        // Skip the header row (first row)
        foreach ($data as $key => $row) {
            if ($key === 0) {
                continue;
            }

            $user = User::create([
                'username' => $row[1],
                'email' => $row[2],
                'mobile' => $row[3],
                'password' => Hash::make('password'), // Set a default password, you can customize it
                'role' => $row[4],
                'profile_image' => $row[6],
            ]);

            // Handle hobbies (assuming hobbies are comma-separated in the CSV)
            $hobbies = explode(',', $row[5]);
            foreach ($hobbies as $hobbyName) {
                $hobby = new Hobby(['hobby' => trim($hobbyName)]);
                $user->hobbies()->save($hobby);
            }
        }

        return redirect()->back()->with('success', 'CSV file imported successfully.');
    }
}
