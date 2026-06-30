# Publish monorepo packages to split GitHub repos (required for Packagist.org).
param(
    [string]$Version = "v1.0.3",
    [string]$Owner = "mohammedelkarsh"
)

$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

$packages = @(
    "core", "sa", "eg", "ae",
    "laravel-sa", "laravel-eg", "laravel-ae",
    "codeigniter-sa", "codeigniter-eg", "codeigniter-ae"
)

foreach ($package in $packages) {
    $branch = "split/$package"
    $remote = "https://github.com/$Owner/validators-$package.git"
    $prefix = "packages/$package"
    $repo = "$Owner/validators-$package"

    Write-Host "Splitting $prefix ..."
    git subtree split --prefix=$prefix -b $branch

    gh repo view $repo 1>$null 2>$null
    if ($LASTEXITCODE -ne 0) {
        gh repo create $repo --public --description "validators/$package split package."
    }

    Write-Host "Pushing $remote ..."
    git push $remote "${branch}:main" --force
    git tag -f $Version $branch
    git push $remote $Version --force
}

Write-Host "Done. Submit each split repo on Packagist:"
foreach ($package in $packages) {
    $url = "https://github.com/$Owner/validators-$package"
    Write-Host "  $url"
}

Write-Host ""
Write-Host "Creating GitHub Releases (version badge on repo page) ..."
powershell -File (Join-Path $PSScriptRoot "create-github-releases.ps1") -Version $Version -Owner $Owner

Write-Host ""
Write-Host "Deprecated (delete from Packagist if present): validators/laravel, validators/codeigniter"
Write-Host "  https://github.com/$Owner/validators-laravel"
Write-Host "  https://github.com/$Owner/validators-codeigniter"
