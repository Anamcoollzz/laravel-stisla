@echo off
setlocal

set "ROOT=%~dp0"
if "%ROOT:~-1%"=="\" set "ROOT=%ROOT:~0,-1%"

php -r "$repos = ['%ROOT%', '%ROOT%\stisla-package']; foreach ($repos as $repo) { if (!is_dir($repo . '/.git')) continue; $name = basename($repo); $lines = array_filter(explode(\"\n\", trim((string) shell_exec('git -C ' . escapeshellarg($repo) . ' -c core.safecrlf=false status --porcelain')))); $discarded = 0; $kept = 0; foreach ($lines as $line) { if (!preg_match('/^\s*M\s+(.+)$/', $line, $m)) continue; $file = trim($m[1], \"\\\" \t\r\n\"); $arg = escapeshellarg($file); exec('git -C ' . escapeshellarg($repo) . ' -c core.safecrlf=false diff --ignore-cr-at-eol --quiet -- ' . $arg, $out, $code); if ($code === 0) { exec('git -C ' . escapeshellarg($repo) . ' -c core.safecrlf=false checkout -- ' . $arg); $discarded++; } else { $kept++; } } echo \"[{$name}] Discarded CRLF-only: {$discarded}, Kept real changes: {$kept}\n\"; }"

exit /b 0
