$paths = @(
    "C:\xampp\php\php.exe",
    "C:\php\php.exe",
    "C:\tools\php\php.exe",
    "C:\Program Files\php\php.exe"
)

foreach ($path in $paths) {
    if (Test-Path $path) {
        Write-Output "Found: $path"
        exit 0
    }
}

# Try wildcards for versioned folders
$wildcards = @(
    "C:\laragon\bin\php\php*\php.exe",
    "C:\wamp64\bin\php\php*\php.exe",
    "C:\Users\*\.config\herd\bin\php.exe"
)

foreach ($pattern in $wildcards) {
    $found = Get-ChildItem -Path $pattern -ErrorAction SilentlyContinue | Select-Object -First 1
    if ($found) {
        Write-Output "Found: $($found.FullName)"
        exit 0
    }
}

Write-Output "Not found"
