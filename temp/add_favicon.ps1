# Script to add favicon link to all HTML files in the pages folder
$pagesDir = Join-Path $PSScriptRoot "..\pages"
$files = Get-ChildItem -Path $pagesDir -Filter "*.html" -Recurse

# Favicon link to add
$faviconLink = '<link rel="icon" type="image/png" href="/images/sortoutInnovation-icon/sortout-innovation-only-s.gif" />'

foreach ($file in $files) {
    Write-Host "Processing file: $($file.FullName)"
    
    # Read the file content
    $content = Get-Content -Path $file.FullName -Raw
    
    # Check if favicon link already exists
    if ($content -notmatch 'rel="icon".*sortout-innovation-only-s\.gif') {
        # Find the position to insert the favicon link (after the last meta tag or before the title tag)
        if ($content -match '(?s)(<head>.*?)(<title>)') {
            $headContent = $matches[1]
            $titleTag = $matches[2]
            
            # Insert the favicon link before the title tag
            $newHeadContent = "$headContent$faviconLink`n    $titleTag"
            $content = $content -replace [regex]::Escape($matches[0]), $newHeadContent
            
            # Write the updated content back to the file
            Set-Content -Path $file.FullName -Value $content
            
            Write-Host "Added favicon to: $($file.Name)"
        }
        else {
            Write-Host "Could not find proper insertion point in: $($file.Name)"
        }
    }
    else {
        Write-Host "Favicon already exists in: $($file.Name)"
    }
}

Write-Host "All files processed successfully!" 