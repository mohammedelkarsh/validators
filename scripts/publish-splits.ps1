# Publish monorepo packages to split GitHub repos (required for Packagist.org).
param(
    [string]$Version = "v1.0.1",
    [string]$Owner = "mohammedelkarsh"
)

$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

$packages = @("core", "sa", "eg", "ae", "laravel", "codeigniter")

foreach ($package in $packages) {
    $branch = "split/$package"
    $remote = "https://github.com/$Owner/validators-$package.git"
    $prefix = "packages/$package"

    Write-Host "Splitting $prefix ..."
    git subtree split --prefix=$prefix -b $branch

    if (-not (gh repo view "$Owner/validators-$package" 2>$null)) {
        gh repo create "$Owner/validators-$package" --public --description "validators/$package — see monorepo for development."
    }

    Write-Host "Pushing $remote ..."
    git push $remote "${branch}:main" --force
    git tag -f $Version $branch
    git push $remote $Version --force
}

Write-Host "Done. Submit each split repo on Packagist:"
foreach ($package in $packages) {
    Write-Host "  https://github.com/$Owner/validators-$package"
}
