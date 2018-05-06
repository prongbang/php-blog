<?php

class ViewUtil {

    public static function update($self, $request, $response, $args) {
        if(!isset($_SESSION['time'])) { 
            $_SESSION['time'] = time() + 30 * 60; // minutes
            BlogController::update($self, $request, $response, $args);
        } else {
            if($_SESSION['time'] <= time()) {
                $_SESSION['time'] = time() + 30 * 60; // minutes
                BlogController::update($self, $request, $response, $args);
            }
        }
    }

}
