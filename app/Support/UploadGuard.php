<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

/**
 * Lightweight content checks for uploaded binaries. Confirms a file really is
 * what its extension claims (by magic bytes), so a disguised script can't slip
 * in under a .exe/.ico name. Files are stored on the private disk (outside the
 * web root) with random names and are never executed server-side, so these
 * checks are defense-in-depth on top of that.
 */
class UploadGuard
{
    /** True if the file begins with the Windows PE "MZ" signature. */
    public static function isWindowsExe(UploadedFile $file): bool
    {
        return self::startsWith($file, "MZ");
    }

    /** True if the file begins with the Windows ICONDIR header (00 00 01 00). */
    public static function isIco(UploadedFile $file): bool
    {
        return self::startsWith($file, "\x00\x00\x01\x00");
    }

    private static function startsWith(UploadedFile $file, string $magic): bool
    {
        $path = $file->getRealPath();
        if (!$path || !is_readable($path)) {
            return false;
        }
        $fh = @fopen($path, 'rb');
        if (!$fh) {
            return false;
        }
        $head = fread($fh, strlen($magic));
        fclose($fh);

        return $head === $magic;
    }
}
