# Script to add favicon link to all HTML files in the pages folder
$pagesDir = Join-Path $PSScriptRoot "..\pages"
$files = Get-ChildItem -Path $pagesDir -Filter "*.html" -Recurse

# Favicon link to add
$faviconLink = '<link rel="icon" type="image/png" href="/images/sortoutInnovation-icon/sortout-innovation-only-s.gif" />'

foreach ($file in $files) {
    Write-Host "Processing file: $($file.FullName)"
    
    try {
        # Read the file content
        $content = Get-Content -Path $file.FullName -Raw
        
        # Check if favicon link already exists
        if ($content -notmatch 'rel="icon".*sortout-innovation-only-s\.gif') {
            # First try: match after meta tags and before title
            if ($content -match '(?s)(<head>.*?)(<title>)') {
                $headContent = $matches[1]
                $titleTag = $matches[2]
                
                # Insert the favicon link before the title tag
                $newHeadContent = "$headContent$faviconLink`n    $titleTag"
                $newContent = $content -replace [regex]::Escape($matches[0]), $newHeadContent
                
                # Write the updated content back to the file
                Set-Content -Path $file.FullName -Value $newContent -NoNewline
                
                Write-Host "Added favicon to: $($file.Name)"
            }
            # Second try: match after head tag
            elseif ($content -match '(?s)(<head>)') {
                $headTag = $matches[1]
                
                # Insert the favicon link after the head tag
                $newHeadContent = "$headTag`n    $faviconLink"
                $newContent = $content -replace [regex]::Escape($headTag), $newHeadContent
                
                # Write the updated content back to the file
                Set-Content -Path $file.FullName -Value $newContent -NoNewline
                
                Write-Host "Added favicon to: $($file.Name) (after head tag)"
            }
            # Last resort: try to match after the last link tag
            elseif ($content -match '(?s)(.*<link[^>]*>)(?!</head>)(.*)') {
                $beforeLastLink = $matches[1]
                $afterLastLink = $matches[2]
                
                # Insert the favicon link after the last link tag
                $newContent = "$beforeLastLink`n    $faviconLink$afterLastLink"
                
                # Write the updated content back to the file
                Set-Content -Path $file.FullName -Value $newContent -NoNewline
                
                Write-Host "Added favicon to: $($file.Name) (after last link)"
            }
            else {
                Write-Host "Could not find proper insertion point in: $($file.Name)"
            }
        }
        else {
            Write-Host "Favicon already exists in: $($file.Name)"
        }
    }
    catch {
        Write-Host "Error processing $($file.Name): $_"
    }
}

Write-Host "All files processed successfully!" 