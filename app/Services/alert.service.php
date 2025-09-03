<?php

class AlertService {

    public static function success($message){
        $_SESSION['flash_alert'] = [
            'type'=> 'success',
            'message' => $message,
            'icon' => 'fa-check-circle'
        ];
    }

    public static function error($message){
        $_SESSION['flash_alert'] = [
            'type'=> 'error',
            'message' => $message,
            'icon' => 'fa-exclamation-circle'
        ];
    }

    public static function warning($message) {
        $_SESSION['flash_alert'] = [
            'type' => 'warning',
            'message' => $message,
            'icon' => 'fa-exclamation-triangle'
        ];
    }
    
    public static function info($message) {
        $_SESSION['flash_alert'] = [
            'type' => 'info',
            'message' => $message,
            'icon' => 'fa-info-circle'
        ];
    }
    
    public static function get() {
        if (isset($_SESSION['flash_alert'])) {
            $alert = $_SESSION['flash_alert'];
            unset($_SESSION['flash_alert']);
            return $alert;
        }
        return null;
    }
    
    public static function render() {
        $alert = self::get();
        if ($alert) {
            echo '<link rel="stylesheet" href="../../Components/alert/alert.css">';
            echo '<div id="flash-alert" class="flash-alert flash-' . $alert['type'] . '" data-auto-hide="true">';
            echo '<i class="fas ' . $alert['icon'] . '"></i>';
            echo '<span>' . htmlspecialchars($alert['message']) . '</span>';
            echo '<button class="alert-close" onclick="closeAlert()">&times;</button>';
            echo '</div>';
        }
    }

}

?>