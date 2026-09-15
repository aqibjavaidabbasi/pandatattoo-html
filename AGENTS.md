# Working rules for this repo

We are rebuilding a WordPress site as plain PHP. Read `PLAN.md` and `TRACKING.md` before
your first edit. This file is the standing contract; the task prompt is what changes.

## Hard rules

1. **`../tattoopanda-wp/` is READ-ONLY.** It is the reference WordPress install. Read it
   freely, never write to it. It is a clean git checkout — any modification will be caught.
2. **`reference/html/` is READ-ONLY.** These are the captured rendered pages from the live
   staging site. They are the source of truth for what the output must look like. Never edit them.
3. **Goal is a byte-faithful replica**, including animations. When the reference markup looks
   odd (stray whitespace, an unclosed `<div id="content">`, WordPress menu classes on `<li>`s),
   **reproduce it anyway** — the CSS depends on it. Do not "clean up" markup.
4. **Never invent content.** Every string, image, URL and number must come from
   `reference/html/` or `../tattoopanda-wp/`. If you cannot find a value, stop and say so in
   your final message. Do not use placeholders, lorem ipsum, or made-up IDs.
5. **Never remove or alter tracking.** GTM, GA4 and the Meta Pixel are carried over
   deliberately (see `TRACKING.md`). The three `inc/tracking-*.php` partials and their
   include order are load-bearing.
6. **No new dependencies, no build step, no Node.** The host is SiteGround shared hosting:
   PHP only. Front-end libs stay on the same CDN URLs the reference uses.
7. **Nothing is out of scope. Nothing gets skipped.** Every page, feature, form, table and
   code path that exists on the WordPress site must exist here — including ones that currently
   render empty, are unlinked from the nav, or look unfinished. We are converting a site, not
   curating it.
   The mirror of that rule: **do not complete, fix or improve anything either.** If a feature
   is half-built on WordPress, it is half-built here in exactly the same way. If a page shows
   placeholder content, the placeholder is carried over. Missing features are the client's call,
   not ours. Report anything that looks wrong — never silently repair it.
8. **No secrets in code.** Anything credential-shaped goes in `.env` / `.env.example`
   and is read via `env()`.

## Conventions already established — follow them

- `inc/config.php` — env loading + the helpers that replace WordPress:
  `e()` (esc_html/esc_attr), `eu()` (esc_url), `asset()` (cache-busted asset URL),
  `current_path()`, and the `$PAGE` array.
- Every page is a top-level `<name>.php` shaped like this:

  ```php
  <?php
  require __DIR__ . '/inc/config.php';
  $PAGE = [
      'title'      => 'Gear – Tatto Panda',
      'body_class' => '...copy verbatim from the reference capture...',
      'css'        => 'gear.css',   // page CSS, or null
  ];
  include __DIR__ . '/inc/head.php';
  include __DIR__ . '/inc/header.php';
  ?>
  ... page markup ...
  <?php include __DIR__ . '/inc/footer.php';
  ```

- Each template's single big inline `<style>` block moves to
  `assets/css/pages/<page>.css` — content unchanged, no reformatting, no minifying.
- Stylesheet order in `inc/head.php` is the site's cascade. Do not reorder it.
- **Page links go through `route()`, not `url()`.** `route('/gallery/')` takes the canonical
  path and emits whatever the host can serve — `/gallery.php` by default, `/gallery/` when
  `PRETTY_URLS=1`. Pretty URLs need a rewrite, and inside another site's document root that
  rewrite may never run. Same rule for JS endpoints: post at `api/*.php` directly, never at
  the `/wp-json/` or `/wp-admin/` aliases, which exist only if our `.htaccess` is honoured.
- Absolute URLs pointing at `pandatattoo.com`, `staging4.temp.tattoopanda.com` or
  `staging2.tattoopanda.com` must become **local paths emitted through `url()` / `asset()`
  / `<?= base_url() ?>`** — never a hardcoded `/foo/`, so the site works at the domain root
  and inside a subfolder alike (`inc/config.php`, `base_path()`). The asset itself is
  copied from `../tattoopanda-wp/wp-content/uploads/` into `assets/img/`. This is the whole
  point of the rebuild — do not leave the new site depending on the old boxes.
  Third-party hosts (Contentful `images.ctfassets.net`, GHL, jsDelivr, cdnjs, Google Fonts,
  Instagram, TikTok) stay exactly as they are.
- **Localise the `href`, never the visible text.** Where an old-domain URL appears as link
  *text* — it does in the legal pages, e.g.
  `<a href="https://pandatattoo.com/contact">https://pandatattoo.com/contact</a>` — the href
  becomes `<?= base_url() ?>/contact/` but the text between the tags stays exactly as the
  capture has it.
  That text is published legal copy; rewriting it changes what the document says.

## Definition of done for a page task

- `php -l <page>.php` passes.
- `php -S 127.0.0.1:8123` serves the page with no PHP notices or warnings.
- Every asset the page requests resolves locally (no 404s, no old-domain URLs).
- The rendered output matches `reference/html/<page>.html` in structure, text and class names.
- Your final message lists: what you built, anything you could not find, and anything you
  deliberately changed from the reference (with the reason).
