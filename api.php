<?php

    // Get the HTTP request method.
    $verb = $_SERVER['REQUEST_METHOD'];

    // Get all endpoint data.
    $request = $_GET['request'];

    // Determine the endpoint (action).
    $action = (strpos($request, '/') !== false) ? explode('/', $request)[1] : $request;

    // Define booleans to verify our desired set of endpoints.
    $is_add = ($verb === 'POST' && $action == 'add') ? true : false;
    $is_get_one = ($verb === 'GET' && $action === 'get' && $ip !== null);
    $is_get_all = ($verb === 'GET' && $action === 'all');
    $is_delete_all = ($verb === 'DELETE' && $action === 'all');

    // Check if any of the desired endpoints are occuring.
    if ($is_add || $is_get_one || $is_get_all || $is_delete_all) {

        // Get our JSON list of IPs from a local file.
        $ip_list = file_get_contents("ips.json");

        // If the file is empty, create an empty JSON list.
        if ($ip_list === "") {
            $ip_list = "[]";
        }

        // Decode json into array.
        $ip_list = json_decode($ip_list);

        // ----- Endpoint - Add new IPs (/ip/add). ----- //
        if ($is_add) {

            // PLceholder for a list of IPs to be added.
            $new_ip_list = "";

            //Since the posted data is not JSON, it will not be captured within $_POST.  Instead, use php://input.
            if ($verb === "POST" && count(file_get_contents("php://input")) > 0) {
                $new_ip_list = file_get_contents("php://input");
            }

            // Decode the new IP list into an array.
            $new_ip_list = json_decode($new_ip_list);


            // Loop over the list of new ips.
            foreach ($new_ip_list as &$new_ip) {
                if(array_search($new_ip, $ip_list) === false) {
                    array_push($ip_list, $new_ip);
                }
            }
            $ip_list = json_encode($ip_list);

            $file = fopen('ips.json', 'w');
            fwrite($file, $ip_list);
            fclose($file);
        }

        // ----- Endpoint - Verify a requested IP (/ip/get). ----- //
        if ($is_get_one) {

            // Get the IP being requested.
            $ip = (strpos($request, '/') !== false) ? explode('/', $request)[2] : null;

            if (array_search($ip,$ip_list) !== false) {
                header("Content-Type:text/plain");
                echo $ip;
            } else {
                header('HTTP/1.1 500 Internal Server Error');
            }
        }

        // ----- Endpoint - Get all IPs (/ip/all). ----- //
        if ($is_get_all) {
            header('Content-type: text/json');
            header('Content-type: application/json');
            echo json_encode($ip_list);
        }

        // ----- Endpoint - Delete all IPs (/ip/all) ----- //
        if ($is_delete_all) {
            $file = fopen('ips.json', 'w');
            fwrite($file, '[]');
            fclose($file);
        }
    }
?>