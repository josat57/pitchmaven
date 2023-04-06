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

if (isset($_POST['theFile']) && $_POST['action'] == 'getFile') {
    $response = getUploadedImages($_POST['dir'], $_POST['theFile']);
} else if (isset($_POST['theFile']) && $_POST['action'] == 'deletefile') {
    $response = deleteUploadedImages($_POST['dir'], $_POST['theFile']);
} else {
    $response = ["statuscode" => -1, "status" =>"Error : File not uploaded to remote server."];
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

/**
 * Get uploaded images
 * 
 * @param string $dir     the directory of file to retrieve.
 * @param string $theFile the name of the file to retrieve.
 * 
 * @return string
 */
function getUploadedImages($dir, $theFile) 
{      
    $applpicsdir = dirt($dir);
    $dirt = $applpicsdir;
    $retval = [];
    if (substr($dirt, -1) != "/") {
        $dirt .= "/";
    } 
    die(var_dump($dirt.=$dir));
    if (is_dir($dirt.=$dir)) {                            
        if ($handle = opendir($dirt)) {
            while (false !== ($file = readdir($handle))) {                
                if ($file !== "." && $file !== "..") {
                    $imgfile = file_get_contents($dirt."/".$thefile);
                    $retval = ['statuscode' => 200, 'status' => "", 'photo' => base64_encode($imgfile)];         
                } else {
                    $retval = ['statuscode' => -1, 'status' => "File not found",'photo' => null];
                }
            }            
            closedir($handle);
        } else {
            $retval = ['statuscode' => -1, 'status' => "File not found",'photo' => null];
        }
    } else {
        $retval = ['statuscode' => -1, 'status' => "File not found",'photo' => null];
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