# Create GitHub Releases from existing git tags (shows version badge on repo page).
param(
    [string]$Version = "v1.0.3",
    [string]$Owner = "mohammedelkarsh"
)

$monorepoNotes = @"
## validators $Version

See the full changelog: https://github.com/$Owner/validators/blob/main/CHANGELOG.md

Install from Packagist, for example: composer require validators/laravel-sa
"@

$splitNotes = @"
## $Version

Split package from the [validators monorepo](https://github.com/$Owner/validators).

Install: see package README on [Packagist](https://packagist.org/packages/validators/).

Changelog: https://github.com/$Owner/validators/blob/main/CHANGELOG.md
"@

$repos = @(
    @{ Repo = "validators"; Notes = $monorepoNotes }
    @{ Repo = "validators-core"; Notes = $splitNotes }
    @{ Repo = "validators-sa"; Notes = $splitNotes }
    @{ Repo = "validators-eg"; Notes = $splitNotes }
    @{ Repo = "validators-ae"; Notes = $splitNotes }
    @{ Repo = "validators-laravel-sa"; Notes = $splitNotes }
    @{ Repo = "validators-laravel-eg"; Notes = $splitNotes }
    @{ Repo = "validators-laravel-ae"; Notes = $splitNotes }
    @{ Repo = "validators-codeigniter-sa"; Notes = $splitNotes }
    @{ Repo = "validators-codeigniter-eg"; Notes = $splitNotes }
    @{ Repo = "validators-codeigniter-ae"; Notes = $splitNotes }
)

foreach ($item in $repos) {
    $repo = "$Owner/$($item.Repo)"
    gh release view $Version --repo $repo 1>$null 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "Skip $repo - release $Version already exists"
        continue
    }

    Write-Host "Creating release $Version on $repo ..."
    gh release create $Version --repo $repo --title $Version --notes $item.Notes
}

Write-Host "Done."
