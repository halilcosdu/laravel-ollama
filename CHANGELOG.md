# Changelog

All notable changes to `laravel-ollama` will be documented in this file.

## Unreleased

### Added
- **5 new Ollama endpoints:**
  - `embed(string|array $input)` — `POST /api/embed` (the replacement for the deprecated `/api/embeddings`; accepts a single string or an array of inputs).
  - `ps()` — `GET /api/ps` (list running models).
  - `version()` — `GET /api/version` (running Ollama server version).
  - `push()` — `POST /api/push` (push a model to a registry).
  - `create(string $modelfile)` — `POST /api/create` (create a model from a Modelfile).
- **Tool calling (function calling)** — new fluent `tools(array $tools)` setter, forwarded to `/api/chat` only when set.
- **Chat forwarding** — `chat()` now forwards `keep_alive` (via the existing `keepAlive()` setter) in addition to format/options/stream.

### Deprecated
- `embeddings()` now carries a `@deprecated` docblock; it still calls `POST /api/embeddings` for backwards compatibility. Use `embed()` for new code. Removal planned for v2.0.

### Tests
- +9 `Http::fake` tests covering every new endpoint, tool forwarding, conditional `tools` omission, and `keep_alive` forwarding on chat. 17 → 26 tests (+2 skipped integration).

## v1.0.1 - 2026-07-03

### Added
- **Real test coverage.** Tests previously made live HTTP calls against a running Ollama server (CI installed Ollama and pulled `llama3`). Replaced the default suite with `Http::fake` + `assertSent` coverage for every endpoint (generate, chat, tags, show, copy, delete, pull, embeddings), plus a Guzzle `MockHandler`-based test for the streaming branch of `MakesHttpRequests`. `Http::preventStrayRequests()` keeps the suite hermetic.
- **Optional integration suite** — live Ollama smoke tests moved behind an `OLLAMA_INTEGRATION=1` flag (`@group integration`), skipped by default.

### Changed
- **CI no longer installs Ollama** or pulls/runs `llama3`; tests are now pure unit tests.
- **CI matrix** now also tests Laravel 10.x (composer.json claimed it but CI never ran it). Matrix: PHP 8.2/8.3/8.4 × Laravel 10/11/12/13.
- Bumped GitHub Actions: `actions/checkout` v6, `stefanzweifel/git-auto-commit-action` v7, `dependabot/fetch-metadata` v3.0.0. Supersedes dependabot #12/#13/#14/#15.

### Fixed
- **PHPStan was broken** by an invalid `checkMissingIterableValueType` option in `phpstan.neon.dist`; removed it and regenerated the baseline.
- **Uninitialized typed properties** on the `Ollama` class (`$agent`, `$prompt`, `$options`) were accessed before being set; they now default to safe empty values, so `ask()`/`chat()` no longer error when optional setters aren't called.
- `MakesHttpRequests` now resolves its streaming Guzzle client through the container, so the streaming path can be tested with a `MockHandler`.

### Documentation
- README: dropped the false "Covers all the endpoints of the Ollama API" claim; added an honest "Current limitations" section.

## v1.0.0 - 2026-04-09

### What's Changed

- Added Laravel 12 and Laravel 13 support
- Added PHP 8.4 support
- Updated CI workflow to test against Laravel 13, 12, 11 with PHP 8.4, 8.3, 8.2
- Updated dev dependencies for broader version compatibility (Pest 3, PHPStan 2, Larastan 3, Orchestra Testbench 11)

## v0.0.1 - 2024-05-03

This is the pre-release.

### What's Changed

* Bump dependabot/fetch-metadata from 1.6.0 to 2.0.0 by @dependabot in https://github.com/halilcosdu/laravel-ollama/pull/1

### New Contributors

* @dependabot made their first contribution in https://github.com/halilcosdu/laravel-ollama/pull/1

**Full Changelog**: https://github.com/halilcosdu/laravel-ollama/commits/v0.0.1
