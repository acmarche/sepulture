# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

See also `AGENTS.md` (Symfony conventions: attributes over YAML, autowiring, Flex recipes via `composer require`, migrations instead of `schema:update`, tests with `WebTestCase`/`KernelTestCase`). Its rules apply here.

## What this is

"Gestion patrimoniale des cimetières": a cemetery heritage inventory app by the Ville de Marche-en-Famenne (cemeteries, sepultures, defunts, ossuaires, SIHL / "1945" heritage classifications, Région wallonne exports). UI and domain vocabulary are in French.

## Current state: port in progress (Symfony 7.3 → 8.1)

The repo is a Symfony 8.1 skeleton (PHP 8.5 locally) into which the legacy app has been copied as a bundle. The kernel boots, `lint:container`, `lint:twig` and `doctrine:schema:validate --skip-sync` pass, and `/login` renders. Individual pages have not been exercised against a database yet. Things to know:

- Bundle config files must use `$containerConfigurator->extension('<name>', [...])`. The `Symfony\Config\*Config` builder classes don't exist in Symfony 8, and bundles must not rely on `config/reference.php`.
- The legacy third-party bundles are installed and their Flex recipes ran (`allow-contrib: true`): knplabs/doctrine-behaviors **3.1** (needs `doctrine/collections` 2.x, so don't upgrade it to 3.x), vich-uploader 3, liip-imagine, knp-snappy (`WKHTMLTOPDF_PATH`), phpspreadsheet, mati365/ckeditor5-symfony (CKEditor 5, self-hosted via AssetMapper in git-ignored `assets/vendor/ckeditor5`, reinstalled by a composer auto-script; `sepulture` preset in the bundle's `config/packages/ckeditor5.php`, used by `PageType`), ux-dropzone, twig/intl-extra.
- Doctrine uses `underscore_number_aware` naming, matching the legacy schema. The real database is **MariaDB/MySQL** (`DATABASE_URL=mysql…` in `.env.local`, `utf8mb4`), not the PostgreSQL in `.env`/`compose.yaml`.
- Security (providers, role hierarchy, `dev` and `main` firewalls) lives only in the bundle's `security.php`. `config/packages/security.yaml` keeps just password hashers and `access_control`. Don't add a `main` firewall there: it would deep-merge into the bundle's.
- `Doctrine/TablePrefix.php` still uses `ClassMetadataInfo`, which was removed in ORM 3. It isn't wired as a listener, so it doesn't break the boot.
- The bundle's own env vars (`SEPULTURE_*`, `GA_TRACKING`, `RECAPTCHA_*`) have empty defaults in `.env`. Real values go in `.env.local`.

Reference material (git-ignored, under `data/`): the old app's `composer.json`, `importmap.php` and `config/` (Symfony 7.4), plus `sepulture.sql`, a ~117 MB MariaDB dump of production data. The old importmap has JS deps (bootstrap, tabler icons, lightgallery, stimulus components) that aren't in the new one yet. `src/AcMarche/Sepulture/src/Migration/update.sql` holds legacy hand-written schema changes (e.g. `slugname` → `slug`).

## Architecture

- **Two PSR-4 roots** (`composer.json`): `App\` → `src/App` (only `Kernel.php`) and `AcMarche\Sepulture\` → `src/AcMarche/Sepulture/src`. All application code lives in the bundle. `src/Controller`, `src/Entity`, `src/Repository` are empty skeleton dirs, and `config/services.yaml` registers no `App\` services.
- **`SepultureBundle`** (`AbstractBundle`, registered in `config/bundles.php`):
  - `getPath()` is `src/AcMarche/Sepulture`, so its `config/`, `templates/`, `public/` are siblings of `src/`.
  - `loadExtension()` imports `config/services.php`: autowire + autoconfigure everything under the bundle's `src/` except `Entity`. It also defines the `acmarche_sepulture_*` parameters from env vars `SEPULTURE_EMAIL`, `SEPULTURE_VILLE`, `RECAPTCHA_SITE_KEY`, `RECAPTCHA_SECRET_KEY`, and upload dirs under `public/uploads/{sepultures,cimetieres}`.
  - `prependExtension()` imports `config/packages/*.php` (doctrine, imagine, security, vich_uploader, twig). Bundle-level framework config goes there, not in `config/packages/`.
- **Routing**: `config/routes.yaml` loads attribute routes from `src/AcMarche/Sepulture/src/Controller/`.
- **Templates**: Twig namespace `@AcMarcheSepulture` → `src/AcMarche/Sepulture/templates`. The form theme is `bootstrap_5_layout`. Twig globals are `bootcdn`, `ga_tracking` (`GA_TRACKING`) and `sepulture_ville`.
- **Doctrine**: attribute mappings for `AcMarche\Sepulture\Entity` (alias `AcMarcheSepulture`), plus a custom DQL `any_value` function (`DoctrineExtensions/AnyValue.php`). Repositories use `Doctrine/OrmCrudTrait` (`insert`/`persist`/`flush`/`remove`). `Sepulture` is the central entity (unique `parcelle`, ManyToOne `Cimetiere`, ManyToMany `TypeSepulture`/`Sihl`/`Materiaux`/`Visuel`, `Defunt` children). `History` + `History/HistoryUtils` record field-level change logs.
- **Exports**: `Service/{PdfFactory,XlsFactory,ZipFactory,Rw}` plus `ExportController` and the `ExportCommand` console command produce PDFs (Snappy/wkhtmltopdf, `templates/export/*`), spreadsheets and Région wallonne (RW / SIHL / 1945) reports.
- Public commenting (`Commentaire`) is protected by Google reCAPTCHA (`Captcha/Captcha.php`), and notifications go through `Service/Mailer.php`.

## Commands

```bash
symfony serve -d                       # or php -S / your vhost; app is served from public/
symfony console about                  # or bin/console
symfony console debug:router
symfony console lint:container
symfony console lint:twig src/AcMarche/Sepulture/templates
symfony console make:migration && symfony console doctrine:migrations:migrate
php bin/phpunit                        # all tests (tests/KernelBootTest.php = boot smoke test)
php bin/phpunit --filter SomeTest      # single test
```

PHPUnit (`phpunit.dist.xml`) runs with `failOnDeprecation`, `failOnNotice` and `failOnWarning`, so deprecations count as failures. The test DB name is suffixed `_test`.
