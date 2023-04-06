<?php

/**
 * Gate way.
 * Routes a request to the expected route based on available 
 * parameters that matches routes existing on this system.
 * 
 * PHP Version: 8.1.3
 * 
 * @category Web_Application
 * @package  Gokolect_API_Service
 * @author   Tamunobarasinipiri Samuel Joseph <joseph.samuel@cinfores.com>
 * @license  MIT License
 * @link     https://gokolect.test
 */


if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header('Access-Control-Allow-Credentials: true');
    header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers:Origin, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, accept");
}

$response = null;

if (isset($_FILES) && isset($_POST['data']) && isset($_POST['action'])) {
    
    if ($_POST['action'] === 'items') {
        $response = uploadItems($_POST['data'], $_POST['imageFileType'], $_POST['dir']);
    } else if ($_POST['action'] === 'profile') {
        $response = uploadProfile($_POST['data'], $_POST['imageFileType'], $_POST['dir']);
    } else {
        $response = ["statuscode" => -1, "status" => "No action specified ". $_POST['action']];
    }

} else {
    $response = ["statuscode" => -1, "status" =>"Error : File not uploaded to remote server."];
}

/**
 * Upload team and palyer images
 * 
 * @param string $data          Object of data parsed
 * @param string $file          Object file details
 * @param string $imageFileType directory name to upload image
 * @param string $dir           directory name to upload image
 * 
 * @return mix
 */
function uploadItems($data, $imageFileType, $dir)
{    
    $dt = strtotime(date('Ymd'));
    $target_dirt = dirt($dir);
    $target_dir = strtolower($target_dirt.DIRECTORY_SEPARATOR);
    $uploadOk = null;
    
    $filename = str_replace(' ', '', strtolower($dt."_".$data)).".".$imageFileType;
    $target_file = $target_dir. str_replace(' ', '', strtolower($dt."_".$data)).".".$imageFileType;    
    
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); 
    } 
    
    if (!file_exists($target_file)) {
        $uploadOk = 1;
    }
    
    if ($uploadOk != 1) {
        $response = ['status' => $uploadOk, 'statuscode' => $uploadOk];
    } else {
        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {    
            $response = ['status' => "Uploaded", 'statuscode' => 200, 'filename' =>$filename, 'target_dir' => $target_dir];
        } else {
            $response = ['status' => "upload failed", 'statuscode' => -1];   
        }
    }
    return $response;        
} 

/**
 * Upload team and palyer images
 * 
 * @param string $data          Object of data parsed
 * @param string $file          Object file details
 * @param string $imageFileType directory name to upload image
 * @param string $dir           directory name to upload image
 * 
 * @return mix
 */
function uploadProfile($data, $imageFileType, $dir)
{                       
    $target_dirt = dirt($dir);
    $dt = strtotime('now');
    $target_dir = strtolower($target_dirt.DIRECTORY_SEPARATOR.strtolower(str_replace(' ', '', $dir)). DIRECTORY_SEPARATOR);
    $uploadOk = 1;
    $name = "gkpf". $dt .$data;
    $filename = str_replace(' ', '', strtolower($dt."_".$data)).".".$imageFileType;
    $target_file = strtolower($target_dir . str_replace(' ', '', $name).".".$imageFileType);
    
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); 
    } 
    
    if (file_exists($target_file)) {
        $uploadOk = 1;
    }

    if ($uploadOk != 1) {
        $response = ['status' => true, 'statuscode' => $uploadOk];
    } else {        
        if (move_uploaded_file($_FILES['file']["tmp_name"], $target_file)) {                          
            $response = ['status' => "Uploaded", 'statuscode' => 200, 'filename' =>$filename, 'target_dir' => $dir];
        } else {
            $response = ['status' => "Unable to upload the image...", 'statuscode' => -1];   
        }
    }
    return $response;        
}

/**
 * Check directory for upload
 * 
 * @param string $dir the path to the directory.
 * 
 * @return string
 */
function dirt($dir)
{
    $new_dir = str_replace(' ', '', $dir);
    $applpicsdir = 'file_server/';

    if (!is_dir($applpicsdir)) {
        $applpicsdir = "file_server/";
    } else {
        $applpicsdir = (is_link($applpicsdir)?readlink($applpicsdir):$applpicsdir)."/{$new_dir}";
        // $applpicsdir = (is_link($applpicsdir)?readlink($applpicsdir):$applpicsdir)."/{$_SERVER['HTTP_HOST']}/{$new_dir}";
    }
    $user_dir = getRelativePath("$applpicsdir/");
    return $user_dir = (is_link($user_dir)?readlink($user_dir):$user_dir);
}

/**
 * Get relative path of address
 * 
 * @param string $path a string of directory path or url
 * 
 * @return string
 */
function getRelativePath($path) 
{
    $path = preg_replace("#/+\.?/+#", "/", str_replace("\\", "/", $path));
    $dirs = explode("/", rtrim(preg_replace('#^(\./)+#', '', $path), '/'));

    $offset = 0;
    $sub = 0;
    $subOffset = 0;
    $root = "";

    if (empty($dirs[0])) {
        $root = "/";
        $dirs = array_splice($dirs, 1);
    } else if (preg_match("#[A-Za-z]:#", $dirs[0])) {
        $root = strtoupper($dirs[0]) . "/";
        $dirs = array_splice($dirs, 1);
    }

    $newDirs = array();
    foreach ($dirs as $dir) {
        if ($dir !== "..") {
            $subOffset--;
            $newDirs[++$offset] = $dir;
        } else {
            $subOffset++;
            if (--$offset < 0) {
                $offset = 0;
                if ($subOffset > $sub) {
                    $sub++;
                }
            }
        }
    }

    if (empty($root)) {
        $root = str_repeat("../", $sub);
    }
    return $root . implode("/", array_slice($newDirs, 0, $offset));
}

echo json_encode($response);