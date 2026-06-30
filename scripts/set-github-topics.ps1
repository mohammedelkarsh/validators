# GitHub Topics improve search/discovery on github.com/explore
param(
    [string]$Owner = "mohammedelkarsh"
)

$topicMap = @{
    "validators" = @("php", "validation", "composer", "packagist", "saudi-arabia", "egypt", "uae", "monorepo")
    "validators-core" = @("php", "validation", "composer", "packagist")
    "validators-sa" = @("php", "validation", "saudi-arabia", "national-id", "iban", "composer")
    "validators-eg" = @("php", "validation", "egypt", "national-id", "composer")
    "validators-ae" = @("php", "validation", "uae", "emirates-id", "iban", "composer")
    "validators-laravel-sa" = @("php", "laravel", "validation", "saudi-arabia", "composer")
    "validators-laravel-eg" = @("php", "laravel", "validation", "egypt", "composer")
    "validators-laravel-ae" = @("php", "laravel", "validation", "uae", "composer")
    "validators-codeigniter-sa" = @("php", "codeigniter", "validation", "saudi-arabia", "composer")
    "validators-codeigniter-eg" = @("php", "codeigniter", "validation", "egypt", "composer")
    "validators-codeigniter-ae" = @("php", "codeigniter", "validation", "uae", "composer")
}

foreach ($repo in $topicMap.Keys) {
    $topics = $topicMap[$repo] -join ", "
    Write-Host "Topics $Owner/$repo => $topics"
    $args = @("api", "--method", "PUT", "repos/$Owner/$repo/topics", "-H", "Accept: application/vnd.github+json")
    foreach ($topic in $topicMap[$repo]) {
        $args += "-f"
        $args += "names[]=$topic"
    }
    & gh @args
}

Write-Host "Done."
