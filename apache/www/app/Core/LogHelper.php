<?php
declare(strict_types=1);

namespace Unibostu\Core;

class LogHelper {
    public static function log_error(string $message = "") {
        $trace = debug_backtrace(limit:1);
        error_log(sprintf("%s [%s:%d]", $message, $trace["file"] ?? "unknown", $trace["line"] ?? 0));
    }
}


?>
