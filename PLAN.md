# Panda Tattoo — WordPress → Coded Site

**Goal:** 100% visual + behavioural replica of `https://staging4.temp.tattoopanda.com/`
as a plain coded site, deployable to SiteGround shared hosting (PHP, **no Node at runtime**).
Animations, transitions and micro-interactions must match exactly.

**Reference (read-only, never edit):** `../tattoopanda-wp/`
**Live reference:** `https://staging4.temp.tattoopanda.com/`
**Work happens here:** `panda-site/`

---

## 0. Status

**Stack decided: PHP.** See §2.

`staging4.temp.tattoopanda.com` was returning HTTP 500 earlier on 2026-09-09; it is **back up**.
Rendered HTML for all 9 pages plus both theme stylesheets is captured in
`reference/html/` as the diff baseline, so a future outage no longer blocks us.

`pandatattoo.com` is a **separate, older production site** (routes `/gallery/`, `/contact/`).
It is not the design target — **but it is where the analytics live.** See §1.5 and `TRACKING.md`.

Still needed: the **database dump** (`wp_posts`, `wp_postmeta`, `wp_options`, `wp_terms*`).
Everything except the ACF/CPT content in §1.2 can proceed without it.

---

## 1. What we found in the reference code

Site is a WordPress child theme `wp-content/themes/studio-child` on parent `studio`
(Catch Themes). The child theme is where 100% of the real site lives.

### 1.0 Live page inventory (confirmed against staging, 2026-09-09)

| URL | Template | Page ID | HTML size |
|---|---|---|---|
| `/` | `templates/home-dev.php` | 130 | 379 KB |
| `/gallery/` | `templates/work.php` | 14 | 362 KB |
| `/gear/` | `templates/gear.php` | 186 | 244 KB |
| `/contact/` | `templates/contact.php` | 19 | 242 KB |
| `/privacy-policy/` | `templates/privacy-policy.php` via `template_include` filter | 3 | 264 KB |
| `/terms-and-conditions/` | `templates/terms-and-conditions.php` via same filter | 888 | 266 KB |
| `/giveaway/` (`/give/` → 301) | `templates/give.php` | 208 | 55 KB |
| `/tattoo-fight/` | `templates/tattoo-fight.php` | 397 | 45 KB |
| `/booking/` | `templates/booking.php` | 880 | 227 KB |

**Homepage is `home-dev.php`** — confirmed via body class `page-template-home-dev`.
So `home.php`, `home-template.php`, `home-dev-backup.php` are dead.
`sample.php`, `temp-page.php`, `artist-profile.php` and `artist-booking.php` route to
**no live URL** (`/sample/`, `/temp/`, `/artist/`, `/work/` all 404) — confirm before dropping.

Nav menu (`primary`): Home · Gallery · Gear · Contact. Footer: Privacy Policy · Terms & Conditions.
Artist links are anchors into `/gallery/#<slug>`, not separate pages.

### 1.1 Page templates (child theme `templates/`)
| File | Template Name | LOC | Notes |
|---|---|---|---|
| `home-dev.php` | Home Dev | 2588 | **the live homepage** (`hd-*` classes, booking modal) |
| `home.php` | Home | 1387 | dead — not routed |
| `home-template.php` | Main Home Template | 369 | dead — not routed |
| `home-dev-backup.php` | Home Dev | 2762 | backup, ignore |
| `work.php` | Work | 1160 | serves `/gallery/` — artist grid (Contentful) + featured work CPT |
| `contact.php` | Contact | 562 | GHL inline form + booking calendar |
| `gear.php` | Gear | 543 | fully static |
| `give.php` | GiveAway | 362 | serves `/giveaway/` — GHL inline form, otherwise static |
| `tattoo-fight.php` | Tattoo Fight | 491 | contest + OTP voting, heavy ACF |
| `artist-profile.php` | Artist Profile | 341 | **not routed** — per-artist page, Contentful by slug |
| `artist-booking.php` / `booking.php` | Artist Booking Page | 212 / 149 | standalone booking |
| `privacy-policy.php` | Privacy Policy | 962 | static |
| `terms-and-conditions.php` | Terms and Conditions | 1035 | static |
| `temp-page.php` | Temp Landing Page | 1037 | **not routed** |
| `sample.php` | sample | 395 | **not routed** |
| `template-parts/booking-modal.php` | — | 2707 | Alpine.js multi-step booking form |

### 1.2 The WordPress surface is tiny
Total WP API calls across every template, header and footer:

```
38 esc_attr  36 esc_html  27 esc_url  26 get_field  17 get_footer  15 get_header
13 get_template_part  12 home_url  8 get_stylesheet_directory_uri  4 do_shortcode
3 wp_nav_menu  2 wp_head  2 wp_footer  2 get_the_title  2 bloginfo  1 get_template_directory_uri
```

Nearly all page content is **hardcoded HTML inside the templates**, not in the database.
Only these bits are DB-backed:

- CPT `awards` → ACF `award_name`, `year`, `award_link` (home)
- CPT `work` → featured work grid (work page)
- CPT `contests` + CPT `artists` → ACF `ig_handle`, `votes`, `artist_photo`, `battle_clips` (tattoo-fight)
- ACF `contact_info` (contact page)
- ACF `hero_title`, `hero_subtitle`, `cta_button_text`, `cta_button_link`, `shortcode` (tattoo-fight)
- ACF `artist_slug` (artist-profile)
- Nav menus: `primary`, `social`
- Page → template mapping and page slugs

That list is the entire scraping/extraction job.

### 1.3 CSS / JS
- `studio/style.css` — 2867 lines (parent)
- `studio-child/style.css` — 4067 lines (child)
- One large inline `<style>` block per template (page-scoped CSS)
- Fonts: Neue Montreal (light/regular/medium/bold, woff+woff2, local in child theme),
  Google Fonts `Inter` + `Space Grotesk`, Typicons (parent)
- JS libs: jQuery, **Alpine.js 3.14.0**, **animate.css 4.1.1**, **Fancybox 5**, child `vote.js`

### 1.4 Integrations
**Contentful** — space `na4mk1p9pznd`, env `master`. Tokens currently **hardcoded in
`functions.php` as fallbacks** (CDA + CMA). The CMA token is a write token and must never
reach the browser.
- *Read* (CDA): `get_contentful_artists()`, `get_contentful_artist_by_slug()`,
  `get_contentful_artist_by_id()`, content type `artists`, cached via WP transients.
- *Write* (CMA): booking submissions → upload image to `upload.contentful.com` → create asset →
  process → publish → create + publish `appointments` entry.
- REST route: `POST /wp-json/custom/v1/booking-submit`
- Hardcoded slug → Contentful entry ID map for 10 artists (`get_artist_contentful_map()`).

**GHL (GoHighLevel, white-labelled as `link.smartwebsite360.com`)** — 100% client-side iframes,
nothing server-side. Copy verbatim:
- `https://link.smartwebsite360.com/js/form_embed.js`
- booking calendar widget `oHN0M6e18FAfLByWox01` (gear, contact, work, privacy, terms, home)
- inline form `B1LLvOARhRLPJ7570tJD` (contact)
- inline form `YesKgIXFuU4MFuCv4lRU` (giveaway)

**Voting (tattoo-fight)** — custom MySQL table `wp_artist_votes`, email OTP flow
(`send_otp_vote` / `verify_otp_vote`), rate-limited per email + per IP per day, `wp_mail`.
Needs a real DB + mailer in the port.

**Contact Form 7** — `store_artist_to_custom_post()` on `wpcf7_before_send_mail`
(artist signup → CPT). Check whether this is still in use before porting.


### 1.5 Analytics & tracking — audited, see `TRACKING.md`

**Staging has no tracking at all. Production has three tags.** A literal replica of the
design target would ship with zero analytics, so these come from `pandatattoo.com`:

- Google Tag Manager `GTM-M3WW4NT7` (head + `<noscript>` as first `<body>` child)
- GA4 `G-YBBGK3PZCZ` (direct `gtag.js`, loaded *in addition to* GTM)
- Meta Pixel `2148964251981412` (+ `<noscript>` img, + Meta Open Bridge / CAPI gateway
  configured server-side against the pixel ID — keep the ID and it keeps working)

None of it is in any file on disk; it is injected from the DB by the WPCode /
Insert Headers and Footers plugin. Verbatim snippets and placement rules are in `TRACKING.md`.

Absent on both sites, do **not** invent: Google Ads (`AW-`), UA, Clarity, Hotjar, TikTok
Pixel, Snap, Pinterest, LinkedIn, HubSpot, Segment, Mixpanel, PostHog.

⚠️ Staging's HTML carries **161 hardcoded `pandatattoo.com` URLs** (logos, uploads) — the
rebuild must localise every one, or the new site stays dependent on the old box.


---

## 2. Stack decision — **plain PHP, no framework, no build step**

**Chosen:** static-first PHP. Per-page `.php` files that `include` a shared header/footer,
one CSS file per page, plus a handful of `api/*.php` endpoints. Upload via SFTP/Git to
SiteGround, nothing to compile.

**Why:**
- The existing templates *are already PHP*. Stripping ~15 WordPress functions is a mechanical
  find-and-replace; a rewrite into JSX/components is not. Shortest path to a byte-accurate replica.
- The CMA write token, the OTP flow and Contentful read caching **require a server runtime**.
  PHP is the only runtime the host gives us.
- Zero build step means what we see locally is what ships — the surest route to "100% replica".

**Rejected — Astro:** the site would still need PHP endpoints for booking/OTP/CMA, so we'd
maintain two runtimes for one site. Static artist pages would go stale whenever Contentful
changes unless we rebuild or client-fetch anyway. Adds a Node toolchain the host can't run.
*Revisit if:* the page count grows a lot or the team wants component reuse and CI deploys.

**Rejected — pure HTML/CSS/JS:** no includes means the header, footer and booking modal get
copy-pasted into ~14 pages, and we'd still be adding PHP files for the API. More work, no gain.

### Target structure
```
panda-site/
  index.php              home
  work.php  contact.php  gear.php  give.php  tattoo-fight.php
  privacy-policy.php  terms-and-conditions.php  booking.php
  artist.php             ?slug= (or /artist/<slug>/ via .htaccess)
  inc/
    header.php  footer.php  booking-modal.php  config.php  contentful.php  db.php
    tracking-head.php  tracking-body-open.php  tracking-body-end.php
  api/
    booking-submit.php  send-otp.php  verify-otp.php
  assets/
    css/base.css  css/pages/*.css
    js/vote.js  js/site.js
    fonts/  img/
  data/                  cached Contentful JSON + DB-extracted content
  .htaccess              pretty URLs, deny data/ and inc/
  .env.example           secrets template (never commit real values)
  reference/html/        captured staging HTML + CSS baselines (do not edit)
  PLAN.md  TRACKING.md
```

Secrets move out of code into `.env`-style config (`inc/config.php` reading a
gitignored file). `.env.example` documents every key.

---

## 2.5 Scope rule (settled 2026-09-10)

**Full 1:1 port. Nothing is dropped, nothing is added.** Every page, form, table and code path
on the WordPress site is reproduced — UI, UX, data model and logic — including features that
currently render empty or are unlinked. Equally, nothing half-finished on WordPress gets
finished here; gaps are reported to the client, not repaired by us.

This settles the Tattoo Fight question: **it gets built in full**, voting and all. See §3 Phase 3.

## 2.6 Running it locally

```bash
cd panda-site
php -S 127.0.0.1:8123 router.php     # then open http://127.0.0.1:8123/
```

**`router.php` is required.** PHP's built-in server ignores `.htaccess`, and for any
directory-style URI it falls back to the document root's `index.php` — so without the router
every pretty URL (`/gear/`, `/gallery/`) silently serves the *home page*, and clicking the nav
makes it look as though every page is identical. `router.php` reproduces the `.htaccess` rules
so local behaviour matches Apache. It is a dev-only file; SiteGround runs Apache and reads
`.htaccess`.

No build step, no Node, no database needed to look at the site. Nine pages: `/`, `/gallery/`,
`/gear/`, `/contact/`, `/giveaway/`, `/privacy-policy/`, `/terms-and-conditions/`, `/booking/`,
`/tattoo-fight/`. Plus `/give/` → 301 → `/giveaway/`, and a 404 page for anything else.

**Verify every page against the live captures:**

```bash
./verify.sh
```

Starts its own server, renders all nine pages, diffs each against `reference/html/`, and
prints a table: word-level text diff, class-signature diff, PHP warnings, and any old-domain
URL left in an attribute. Exit code is non-zero if anything drifts. Run it after every change.

**Unit and integration checks:**

```bash
php tests/contentful-test.php    # slug generation, artist map, asset URLs
php tests/sanitize-test.php      # the WP sanitiser ports, incl. tampered artistSlug
php tests/env-test.php           # env var precedence
DB_HOST=... DB_NAME=... DB_USER=... DB_PASS=... php tests/voting-flow-test.php
```

**Optional credentials** — all read from the environment or `.env`, environment wins:

- `CONTENTFUL_SPACE_ID` + `CONTENTFUL_CDA_TOKEN` — real artist grids on home and gallery.
  Without them the grids render from the seeded cache (`php tests/seed-artist-cache.php`).
- `CONTENTFUL_CMA_TOKEN` — needed for a real booking submission.
- `DB_*` — Tattoo Fight contest data. Without a database the page renders its
  "No active giveaways right now" empty state, which is what the live site shows anyway.

## 3. Phases & tracker

Mark `[x]` as each item lands. Keep this file the single source of truth for progress.

### Phase 0 — Setup (unblocked, do now)
- [x] Inventory reference theme, integrations, dependencies
- [x] Pick stack, record rationale
- [x] Create folder skeleton + `.htaccess` + `.env.example`
- [x] Copy static assets: fonts, both theme stylesheets, typicons, blocks.css, CF7 css, jQuery, favicons
- [x] Local PHP dev server verified (`php -S 127.0.0.1:8123`)

### Phase 1 — Capture the source of truth
- [x] Staging restored; rendered HTML of all 9 pages captured → `reference/html/`
- [x] Both theme stylesheets captured (`studio` 51 KB, `studio-child` 91 KB, as served)
- [x] Confirm the template behind every live URL (§1.0) — homepage is `home-dev.php`
- [x] Extract the `primary` nav menu (Home · Gallery · Gear · Contact) + footer legal links
- [x] Audit analytics / tracking / third-party scripts → `TRACKING.md`
- [ ] DB dump received
- [ ] Extract ACF/CPT content listed in §1.2 → `data/*.json` *(needs DB)*
- [ ] Extract the `social` menu *(not rendered on staging — needs DB, or confirm it's unused)*
- [ ] Mirror the 161 cross-domain `pandatattoo.com` assets locally
- [ ] Screenshot every page at 1440 / 1024 / 768 / 390 as visual baselines

### Phase 2 — Shell & static pages
- [x] `inc/head.php` + `inc/header.php` + `inc/footer.php` (nav, off-canvas, Book Now CTA, copyright)
- [x] CSS kept as separate files in the original load order (parent → animate → child → fonts →
      typicons → blocks → page) rather than merged — merging would change the cascade the site depends on
- [x] Port static pages: gear, giveaway, privacy-policy, terms-and-conditions
- [x] Port home (lift the inline `<style>` block into `css/pages/home.css`)
- [x] Port gallery (was work.php), contact
- [x] Wire GHL embeds verbatim (script + 3 widget IDs)
- [x] Tracking wired from `TRACKING.md` and verified on every built page: GA4 + GTM in
      `<head>`, GTM `<noscript>` first in `<body>`, Meta Pixel late in `<body>`
- [x] Third parties carried: Google Maps link, Instagram outbound links, GHL embeds
      (TikTok `embed.js` + YouTube live on tattoo-fight — verify with that page)
- [x] Animation parity verified on all 8 built pages: animate__*, x-data/x-show/x-init/x-for/x-if,
      x-transition, @click, IntersectionObserver, addEventListener, requestAnimationFrame and
      Fancybox counts all match the captures exactly (CSS `transition:` accounted for in the
      extracted page stylesheets)

### Phase 3 — Dynamic
- [x] `inc/contentful.php` — CDA read + file cache; pure logic covered by tests/contentful-test.php
- [x] Artist grid on gallery + home from Contentful
- [ ] `artist.php` — artist profile by slug (WP template exists but is routed to no live URL —
      see open question)
- [x] `inc/booking-modal.php` — port the 2707-line Alpine modal as-is
- [x] `api/booking-submit.php` — full CMA flow: upload → asset → process → poll → publish → entry → publish
- [x] Server-side slug → entry-ID map kept (`cf_artist_map()`); browser IDs never trusted
- [x] MySQL schema: `artist_votes` (mirrors `hkj_artist_votes`), `contests`, `voting_leaderboard` → data/schema.sql
- [x] tattoo-fight page: contest window query, artist cards, vote counts, countdown, past battles —
      both branches exercised against a real DB (empty state matches the capture; an active
      contest renders artist cards)
- [x] `api/send-otp.php` / `api/verify-otp.php` — OTP issue + verify, 5-min expiry,
      one verified vote per email per artist per day, same per-IP check, same error strings
- [x] OTP mail via PHP `mail()` — matches WP exactly (no SMTP plugin is installed, so
      `wp_mail()` already falls through to `mail()`); no SMTP credentials needed
- [x] Nomination form (CF7 `Nomination Form`) → `api/nominate.php` creates a leaderboard row
      with `artist_name`, `ig_handle`, `why_compete`, `portfolio_link`, `artist_photo`,
      `votes=0`, `rank=0`, `feature_in_round=0` — port of `store_artist_to_custom_post()`
- [x] CF7 client stack carried over (wp-hooks, wp-i18n, swv, contact-form-7 js + the `wpcf7`
      global) so the form keeps its inline-validation UX instead of a full-page POST

### Phase 4 — Parity & ship
- [ ] Page-by-page visual diff against Phase 1 screenshots at all 4 widths
- [ ] Animation parity pass (side-by-side recordings, not stills)
- [ ] Submit a real test booking → confirm the entry appears in Contentful
- [ ] Full OTP vote round-trip
- [ ] Tracking verification pass (checklist at the end of `TRACKING.md`)
- [ ] Re-audit production for tags added since 2026-09-09
- [ ] Preserve URLs + add redirects for any that change; `robots.txt`, sitemap, meta/OG tags
- [ ] Lighthouse vs the WP baseline (`wp-content/uploads/pagespeed_results_lh_*.json` has the old numbers)
- [ ] Deploy to SiteGround, smoke test

---

## 4. Open questions for the user

Settled: staging is up; homepage is `home-dev.php`; **full 1:1 port, nothing skipped** (§2.5);
Tattoo Fight gets built in full; OTP mail uses PHP `mail()` (no SMTP needed); credentials are
the user's to place on prod.

1. **`hkj_artist_votes` schema** — there is no `CREATE TABLE` anywhere in the codebase, so the
   table was made by hand. Columns are derivable from the queries (`id`, `email`, `phone`,
   `ip_address`, `artist_id`, `otp`, `otp_expires_at`, `vote_date`, `created_at`, `verified`)
   but the exact types are not. One command on prod settles it:
   `SHOW CREATE TABLE hkj_artist_votes;` — otherwise we ship a faithful best-guess schema.
2. **Contentful token rotation** — the CDA and CMA tokens are hardcoded in the old
   `functions.php` and in git history; the CMA one is a write token. User handles on prod.
3. **Target domain** — replace `pandatattoo.com`, or ship somewhere new? Also decides whether
   the old cross-domain asset URLs simply resolve or need re-pointing.
