<?php

/**
 * Gate way.
 * Routes a request to the expected route based on available 
 * parameters that matches routes existing on this system.
 * 
 * PHP Version: 8.1.3
 * 
 * @category Web_Application
 * @package  PitchMaven_API_Service
 * @author   Tamunobarasinipiri Samuel Joseph <joseph.samuel@cinfores.com>
 * @license  MIT License
 * @link     https://pitchmaven.bootqlass.com
 */

error_reporting(E_ALL && E_NOTICE);
ini_set('display_errors', 1);

header('Access-Control-Allow-Origin: https://pitchmavenapi.bootqlass.com');
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");

header('Content-Type: application/json');

// $method = $_SERVER['REQUEST_METHOD'];
// if ($method == "OPTIONS") {
// header('Access-Control-Allow-Origin: https://gokolectapp.bootqlass.com');
// header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
// header("HTTP/1.1 200 OK");
// die();
// }

$response = $_POST;

if (isset($_POST['file_name']) && isset($_FILES['file']['tmp_name']) && isset($_POST['action'])) {    
    if ($_POST['action'] === 'items') {
        $response = uploadItems($_POST, $_FILES);
    } else if ($_POST['action'] === 'profile') {
        $response = uploadProfile($_POST, $_FILES);
    } else if ($_POST['action'] === 'getfile') {
        $response = getUploadedImages($_POST['dir'], $_POST['thefile']);
    } else if ($_POST['action'] === 'deletefile') {
        $response = deleteUploadedImages($_POST['dir'], $_POST['thefile']);
    } else {
        $response = ["statuscode" => -1, "status" => "No action specified ". $_POST['action']];
    }
} else {
    $response = ["statuscode" => -1, "status" =>"Error : File not uploaded to remote server.", "data" => $_POST, "file"=>$_FILES];
}



// save uploaded file
    
// $tempt_file = base64_decode($_POST['fileData']);
// $file_dir = $uploadDir.$_POST['fileName'];
// die(var_dump($tempt_file, $file_dir));
// if (is_dir($uploadDir)) {
//     if (move_uploaded_file($tempt_file, $file_dir)) {
//         $response = ['statuscode' => 0, 'status' => " Uploaded! "];
//     } else {
//         $response = ["statuscode" => -1, "status" =>" Error : File not moved "];
//     }
    // file_put_contents(
    //     $uploadDir. $_POST['fileName'],
    //     base64_decode($_POST['fileData'])
    // );
// } else {
//     $response = ["statuscode" => -1, "status" =>" Error : Directory not found "];
// }


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
function uploadItems($data, $file)
{        
    $target_dirt = dirt($data["dir"]);
    $target_dir = strtolower($target_dirt.DIRECTORY_SEPARATOR);

    $uploadOk = null;
   
    $target_file = strtolower($target_dir . $data["file_name"]);    
    
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); 
    }     
    
    if (!file_exists($target_file)) {
        $uploadOk = 1;
    }
    
    if ($uploadOk != 1) {
        $response = ['status' => $uploadOk, 'statuscode' => $uploadOk];
    } else {
        if (move_uploaded_file($file['file']['tmp_name'], $target_file)) {                          
            $response = ['status' => "Uploaded", 'statuscode' => 200, 'filename' =>$data["file_name"], 'target_dir' => $data["dir"]];
        } else {
            $response = ['status' => "Unable to upload the image...", 'statuscode' => -1, 'file' => $file, "data" => $data];   
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
function uploadProfile($data, $file)
{         
    $target_dirt = dirt($data["dir"]);
    $target_dir = strtolower($target_dirt.DIRECTORY_SEPARATOR);
    $uploadOk = 1;
    $target_file = strtolower($target_dir . $data["file_name"]);
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); 
    } 
    
    if (file_exists($target_file)) {
        $uploadOk = 1;
    }

    if ($uploadOk != 1) {
        $response = ['status' => true, 'statuscode' => $uploadOk];
    } else {
        if (move_uploaded_file($file['file']['tmp_name'], $target_file)) {                        
            $response = ['status' => "Uploaded", 'statuscode' => 200, 'filename' =>$data["file_name"], 'target_dir' => $data["dir"]];
        } else {
            $response = ['status' => "Unable to upload the image...", 'statuscode' => -1, 'file' => $file, "data" => $data];   
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
        $applpicsdir = "file_server/".$dir;
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

/**
 * Get uploaded images
 * 
 * @param string $dir     image directory
 * @param string $thefile image
 * 
 * @return string
 */
function getUploadedImages($dir, $thefile) 
{      
    $applpicsdir = dirt($dir);
    $dirt = $applpicsdir;
    $retval = [];
    if (substr($dirt, -1) != "/") {
        $dirt .= "/";
    } 

    if (is_dir($dirt.=$dir)) {                            
        if ($handle = opendir($dirt)) {
            while (false !== ($file = readdir($handle))) {                
                if ($file !== "." && $file !== "..") {
                    $imgfile = file_get_contents($dirt."/".$thefile);
                    $retval['photo'] = base64_encode($imgfile);         
                } else {
                    $retval['photo'] = null;
                }
            }            
            closedir($handle);
        } else {
            $retval['photo'] = null;
        }
    } else {
        $retval['photo'] = null;
    }
    return $retval;
}

/**
 * Delete uploaded images
 * 
 * @param string $dir     image directory
 * @param string $thefile image
 * 
 * @return string
 */
function deleteUploadedImages($dir, $thefile) 
{      
    $applpicsdir = dirt($dir);
    $dirt = $applpicsdir;
    $retval = "";
    if (substr($dirt, -1) != "/") {
        $dirt .= "/";
    }
    if (is_dir($dirt.=$dir) && $handle = opendir($dirt)) {
        while (false !== ($file = readdir($handle))) {                
            if ($file !== "." && $file !== "..") {
                $imgfile = $dirt."/".$thefile;                    
                if (file_exists($imgfile)) {
                    $retval = unlink($imgfile);
                } else {
                    $retval = 10;
                }       
            }
        }            
        closedir($handle); 
    }
    return $retval;
}

echo json_encode($response);