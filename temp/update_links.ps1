# Script to update Find Talent links in all HTML files
$pagesDir = Join-Path $PSScriptRoot "..\pages"
$files = Get-ChildItem -Path $pagesDir -Filter "*.html" -Recurse

foreach ($file in $files) {
    Write-Host "Processing file: $($file.FullName)"
    
    # Read the file content
    $content = Get-Content -Path $file.FullName -Raw
    
    # Replace in navigation bar
    $content = $content -replace '<a href="/pages/talent.page/talent.html" class="nav-link"', '<a href="/modal_agency.php" class="nav-link"'
    $content = $content -replace '<a href="/pages/talent.page/talent.css" class="nav-link"', '<a href="/modal_agency.php" class="nav-link"'
    
    # Replace in footer
    $content = $content -replace '<li><a href="/pages/talent.page/talent.html">Find Talent</a></li>', '<li><a href="/modal_agency.php">Find Talent</a></li>'
    
    # Write the updated content back to the file
    Set-Content -Path $file.FullName -Value $content
    
    Write-Host "Updated: $($file.Name)"
}

Write-Host "All files processed successfully!" 