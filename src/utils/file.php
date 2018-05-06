<?php 

namespace App\Utils;

use \DateTime;
use App\Utils\IPUtil;

class FileUtil {

    public static function create_from_base64($image_base64, $dir, $path, $target_dir) { 
        $image = str_replace('data:image/png;base64,', '', $image_base64);
        $image = str_replace(' ', '+', $image);
        $image = base64_decode($image);
        $hostname = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $file_name = time() . '.jpg';
        $file = $path . $target_dir .  $file_name;
        $success = file_put_contents($dir. $file, $image);
        return array(
            'path'=> $file,
            'src' => $hostname ."/". $target_dir . $file_name,
            'status'=>$success
        );
    }

    /**
     * Get Extension
     * @param type $filename
     * @return string
     */
    public static function get_extension($filename) {
        $tmp = explode('.', $filename);
        $f = end($tmp);
        return $f;
    }

    /**
     * Generate Name Photo
     * @param type $index
     * @return string
     */
    public static function generate_name($index) {
        $date = new DateTime();
        $prefix = "";
        $center = $date->getTimestamp();
        $suffix = $index;
        return $prefix . $center . $suffix;
    }

    /**
     * Security Check File Extension in Upload
     * @param type $filename
     * @return boolean
     */
    public static function check_file_extension($filename) {
        $allowed = array('gif', 'png', 'jpg', 'jpeg');
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (in_array($ext, $allowed)) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Reset Array Files
     * @param type $file_post
     * @return type
     */
    public static function reset_array_files(&$file_post) {
        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);
        for ($i = 0; $i < $file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }
        return $file_ary;
    }

    /**
     * Multi Upload File
     * @param type $FILE
     * @param type $target_dir
     * @return array
     */
    public static function uploads($FILE, $target_dir) {
        $status = array();

        if ($FILE) {

            $file_ary = FileUtil::reset_array_files($FILE);

            for ($i = 0; $i < count($file_ary); $i++) {

                $filename = FileUtil::generate_name($i + 1) . "." . FileUtil::get_extension($file_ary[$i]['name']);
                $target_file = $target_dir . basename($filename);
                
                if (FileUtil::check_file_extension($filename)) {

                    if (move_uploaded_file($file_ary[$i]["tmp_name"], $target_file)) {
                        $status[$i]["src"] = $target_file;
                        $status[$i]["name"] = $file_ary[$i]['name'];
                        $status[$i]["status"] = "Success";
                    } else {
                        $status[$i]["src"] = "";
                        $status[$i]["name"] = $file_ary[$i]['name'];
                        $status[$i]["status"] = "Fail";
                    }
                } else {
                    $status[$i]["src"] = "";
                    $status[$i]["name"] = $file_ary[$i]['name'];
                    $status[$i]["status"] = "Invalid";
                }
            }
        }
        return $status;
    }

    /**
     * Single Upload File
     * @param type $FILE
     * @param array
     */
    public static function upload($FILE, $dir, $path, $target_dir) {
        $status = array();

        if (!empty($FILE)) {

            $filename = FileUtil::generate_name(1) . "." . FileUtil::get_extension($FILE['name']);
            $file_path = $target_dir . basename($filename);
            $target_file = $dir . $path . $file_path;
            
            if (FileUtil::check_file_extension($filename)) { 
                if (!is_dir($dir . $path . $target_dir)) mkdir($dir. $path.$target_dir, 0755, true);
                if (move_uploaded_file($FILE["tmp_name"], $target_file)) {
                    $hostname = IPUtil::getHostName();
                    $status["src"] = $hostname ."/". $target_dir . $filename;
                    $status["name"] = $filename;
                    $status['path'] = $file_path;
                    $status["status"] = "Success";
                } else {
                    $status["src"] = "";
                    $status['path'] = "";
                    $status["name"] = $FILE['name'];
                    $status["status"] = "Fail";
                }
            } else {
                $status["src"] = "";
                $status['path'] = "";
                $status["name"] = $FILE['name'];
                $status["status"] = "Invalid";
            }
        }
        return $status;
    }

    public static function upload_image($file, $slug) {
        $fullFilename = "";
        if (!empty($file) && !empty($slug)) {
            $filename = $file['name'];
            if (FileUtil::check_file_extension($filename)) { 
                $ext = FileUtil::get_extension($filename);
                $filename = DateUtil::timestamp();
                $fullPath = $slug.'/'.DateUtil::dateYyyyMmDd().'/'.$filename.'.'.$ext;

                $target_dir = "storage/".$slug.'/'.DateUtil::dateYyyyMmDd().'/';
                // move uploaded file from temp to uploads directory
                $result = FileUtil::upload($file, __DIR__, "/../../public/", $target_dir);

                if($result['status'] == "Success") {
                    $fullFilename = IPUtil::getHostName() .'/'. $result['path'];
                } else {
                    $status = 'Upload Fail: Unknown error occurred!';
                    $fullFilename = $status;
                }
            } else {
                $status = 'Upload Fail: Unsupported file format or It is too large to upload!';
                $fullFilename = $status;
            }
        } else {
            $status = 'Upload Fail: Unsupported file format or It is too large to upload!';
            $fullFilename = $status;
        }
        return "<script> parent.setImageValue('".$fullFilename."'); </script>";
    }

    public static function delete($dir, $filename) {
        $status = false;
        if (file_exists($dir . $filename)) {
            unlink($dir . $filename);
            $status = true;
        }
        return $status;
    }

}