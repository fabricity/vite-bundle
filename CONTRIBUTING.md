# Contributing

Contributions are welcome! Please take a moment to read these guidelines before opening an issue or pull request.

## Reporting Issues

- Search existing issues before opening a new one.
- Include a clear description, steps to reproduce, and the expected vs actual behavior.

## Pull Requests

1. Fork the repository and create your branch from `main`.
2. Make your changes and ensure all checks pass (see below).
3. Open a pull request with a clear description of what you changed and why.

Keep pull requests focused — one feature or fix per PR.

## Development Setup

```bash
git clone https://github.com/fabricity/vite-bundle.git
cd vite-bundle
composer install
```

## Running the checks

```bash
# Unit & functional tests
composer phpunit

# Static analysis (PHPStan max)
composer phpstan

# Code style
composer phpcs
```

All three must pass before a pull request can be merged.

## Code Style

This project follows the [Symfony coding standards](https://symfony.com/doc/current/contributing/code/standards.html) enforced by PHP-CS-Fixer. Run `composer phpcs` to auto-fix any violations before committing.

## Commit Messages

Use short, imperative commit messages that describe **what** changed:

```
Add support for custom manifest path
Fix dev server detection when URL has trailing slash
```

## Code of Conduct

Please note that this project is released with a [Code of Conduct](CODE_OF_CONDUCT.md). By participating you agree to abide by its terms.
