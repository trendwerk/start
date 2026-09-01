# Start

WordPress starter project for custom theme development with Composer, Vite, Tailwind CSS, Prettier and Deployer.

## Essentials

- **Package managers:** Composer (PHP), npm (frontend)
- **Build:** `npm start` (install + Vite) | `npm run dev` (Vite) | `npm run build`
- **Format:** `npm run format` (Prettier, including PHP)
- **Install:** copy `.env.example` to `.env`, copy `auth.json.example` to `auth.json`, then `composer install` and `npm install`
- **Local:** webroot `web`; `WP_HOME` e.g. `http://www.start.test`
- **Config:** `application.php` and `.env` (no `config/environments/*.php`)
- **Custom code:** Theme `web/wp-content/themes/theme/` only. Plugins, WordPress core and core language packs are Composer-managed (`web/wp-content/plugins/`, `web/wp/`, `web/wp-content/languages/` root). Plugin and theme translations from WP-CLI (`web/wp-content/languages/plugins/`, `web/wp-content/languages/themes/`) are committed. Custom theme translations live in `web/wp-content/themes/theme/languages/`. Do not edit `vendor/`, `web/wp/` or third-party plugin files.

## PHP Conventions

- **Style:** PSR-12, indentation with two spaces (Prettier `tabWidth` 2)
- **Namespace:** `Theme\` (plugins under `Theme\Plugins\`)
- **Variables:** camelCase; don't shorten names (e.g. `$thumbnail` not `$thumb`)
- **Type hints:** Declare parameter and return types on all methods. Hook callback methods (registered with `add_action` or `add_filter`) are usually left untyped because WordPress and other plugins can break the contract. Type a hook callback only when the hook’s arguments and return value are each a single stable type; when in doubt, leave it untyped.
- **Comments:** Add docblocks on methods, except `init` and `__construct`. Describe behaviour only; do not put types in docblocks, except `@param` and `@return` on hook callback methods for the expected types. Inline `//` before hooks and complex code. Don't add comments about removed code or features.
- **WordPress hooks:** Register in static `init`; callbacks as static methods in same class; call `init` from `functions.php`. For every `add_action` and `add_filter`, always add a single-line comment directly above it that describes what the callback does; use an empty line between each hook registration. Define callback methods in the same order as their hook registrations in `init`
- **Control structures:** Prefer `if { }` syntax over `if : endif;` syntax. Same for `while`, `foreach`, etc.

## Templates and Components

- **Components:** Render files from `components/` with `component('name', $args)` (`functions/component.php`)
- **Separate logic from presentation:** Templates/components should contain minimal logic. Complex logic belongs in Classes (preferred), or in a PHP block before the HTML output. HTML can only contain simple inline variables or function calls like `<?= $post->title(); ?>`, `<?= $title; ?>` or `<?= get_the_title(); ?>`.
- **PHP short tags:** Use `<?=` instead of `<?php echo` in template and component files
- **End expressions with semicolon:** All PHP expressions must end with a semicolon: `<?= $title; ?>`. This is not allowed: `<?= $title ?>`.
- **HTML attributes:** Order attributes alphabetically.
- **data-name attributes:** Parent elements in `web/wp-content/themes/theme/components`: add `data-name` equal to the file name (without `.php`).

## Tailwind CSS

- Utility-based styling with Tailwind v4 (Vite, `@tailwindcss/vite`)
- Entry point: `web/wp-content/themes/theme/styles/theme.css`
- New component styles: `@apply` with Tailwind utilities; BEM-style class names (e.g. `.button`, `.button-small`)

## JavaScript

- Vanilla JS for new code
- Theme scripts: `web/wp-content/themes/theme/scripts/theme.js` (Vite)

## Verification

- After changes that affect the site: test the result (browser when relevant), check `web/wp-content/debug.log` for new errors, fix any related to the change, re-verify until clean
