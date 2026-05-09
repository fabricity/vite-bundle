# Fabricity Vite Bundle

[![Code Quality](https://github.com/fabricity/vite-bundle/actions/workflows/code-quality.yml/badge.svg?branch=main)](https://github.com/fabricity/vite-bundle/actions/workflows/code-quality.yml)
[![PHPUnit](https://github.com/fabricity/vite-bundle/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/fabricity/vite-bundle/actions/workflows/tests.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-max-brightgreen)](https://phpstan.org)

A lightweight Symfony bundle that integrates [Vite](https://vitejs.dev) into your Symfony application. It automatically detects the Vite dev server, reads the production manifest, and exposes simple Twig globals for loading compiled assets.

## Features

- Automatic Vite dev server detection
- Production manifest (`manifest.json`) parsing
- Multiple independent build support (e.g. `frontend` / `backend`)
- Symfony Asset `VersionStrategyInterface` integration
- Twig globals (`vite.dev`, `vite.devClient`) for conditional script loading

## Requirements

- PHP 8.4+
- Symfony 6.4, 7.4, or 8.0

## Installation

```bash
composer require fabricity/vite-bundle
```

Register the bundle in `config/bundles.php`:

```php
return [
    // ...
    Fabricity\Bundle\ViteBundle\FabricityViteBundle::class => ['all' => true],
];
```

## Configuration

Create `config/packages/fabricity_vite.yaml`:

```yaml
fabricity_vite:
    # Path to your public directory (default: %kernel.project_dir%/public)
    public_dir: '%kernel.project_dir%/public'

    # Vite dev server URL (omit in production)
    server: 'http://localhost:5173'

    # One entry per Vite build output
    builds:
        frontend:
            build_dir: build/frontend
            # manifest_path defaults to .vite/manifest.json
        backend:
            build_dir: build/backend
```

Each entry under `builds` registers a Symfony Asset version strategy service named `fabricity_vite.version_strategy.<name>`.

### Multiple builds

You can define as many builds as you need. This is useful when you have separate Vite configs for different parts of your application.

## Twig integration

The bundle registers a Twig extension that exposes a global `vite` variable:

| Variable | Type | Description |
|---|---|---|
| `vite.dev` | `bool` | `true` when the Vite dev server is reachable |
| `vite.devClient` | `string\|null` | URL to the Vite HMR client (`/@vite/client`) |

Use it in your base template to conditionally load the dev client:

```twig
{% if vite.dev %}
    <script type="module" src="{{ vite.devClient }}"></script>
{% endif %}
```

## Asset version strategy

Register the bundle's version strategy on a Symfony Asset package in `config/packages/assets.yaml`:

```yaml
framework:
    assets:
        packages:
            frontend:
                version_strategy: 'fabricity_vite.version_strategy.frontend'
            backend:
                version_strategy: 'fabricity_vite.version_strategy.backend'
```

Then use the standard Symfony `asset()` helper in Twig:

```twig
{# In dev mode: points to http://localhost:5173/src/main.js #}
{# In prod mode: resolves via manifest to /build/frontend/assets/main-abc123.js #}
<script type="module" src="{{ asset('src/main.js', 'frontend') }}"></script>
<link rel="stylesheet" href="{{ asset('src/main.css', 'frontend') }}">
```

### CSS co-located with JS

When Vite bundles CSS alongside a JS entry point, you can reference the CSS file using its `.css` extension even though it is listed under a `.js` key in the manifest:

```twig
<link rel="stylesheet" href="{{ asset('src/app.css', 'frontend') }}">
```

The bundle automatically resolves `src/app.css` → `src/app.js` → `css[0]` from the manifest.

## Full example

```twig
{# templates/base.html.twig #}
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="{{ asset('src/main.css', 'frontend') }}">
</head>
<body>
    {% block body %}{% endblock %}

    {% if vite.dev %}
        <script type="module" src="{{ vite.devClient }}"></script>
    {% endif %}
    <script type="module" src="{{ asset('src/main.js', 'frontend') }}"></script>
</body>
</html>
```

## License

MIT — see [LICENSE](LICENSE).
