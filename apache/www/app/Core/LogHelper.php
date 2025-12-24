<?php
declare(strict_types=1);

namespace Unibostu\Core;

class LogHelper {
    public static function logError(string $message = "") {
        $trace = debug_backtrace(limit:1);
        error_log(sprintf("%s [%s:%d]", $message, $trace[0]["file"] ?? "unknown", $trace[0]["line"] ?? -1));
    }
}


?>
