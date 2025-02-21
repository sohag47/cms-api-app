<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class FileUploadService
{
    /**
     * Upload a file to the specified directory.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string $disk
     * @return string
     */
    public function uploadFile($request)
    {
        // upload new file
        $outputFileName = null;
        if ($request->hasFile('files')) {
            $file_path = $request->path_prefix.'/'.$request->field_name;
            $original_file = $request->file('files');
            $original_name = $original_file->getClientOriginalName();

            $filename = $file_path.'/'.uniqid() .'_'.$original_name;
            $path = $original_file->storeAs('public', $filename);
            $outputFileName = Storage::url($path);
        }
        return $outputFileName;
    }

    /**
     * Delete a file from storage.
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public function deleteFile($file_path): string
    {
        $file_path_format = explode("/", $file_path);

        if (count($file_path_format) > 0 && ($file_path_format[0] === "" && $file_path_format[1] === "storage")) {
            $original_file_path = "";
            foreach ($file_path_format as $index => $directory) {
                if ($index != 0 && $index != 1) {
                    $original_file_path = $original_file_path.'/'.$directory;
                }
            }

            if(!empty($original_file_path)){
                // Ensure the file path is not empty

                if (Storage::disk('public')->exists($original_file_path)) {
                    // Check if the file exists and delete it

                    Storage::disk('public')->delete($original_file_path);
                    return "File Deleted Successfully";
                }
                throw new \Exception("Incorrect File Path!");
            }
            throw new \Exception("Incorrect File Path!");
        }else{
            throw new \Exception("Incorrect File Path!");
        }
    }
}
