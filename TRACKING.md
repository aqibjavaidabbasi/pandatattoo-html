# Tracking & Third-Party Script Audit

Measured, not assumed. Method: grep of the full WP install on disk, mining of the two
Lighthouse JSONs in `wp-content/uploads/`, and live inspection of both sites in a real
browser (main-frame `window` globals, inline `<script>`/`<noscript>` contents, and the
runtime resource waterfall).

**Audited 2026-09-09.** Re-run before launch — tag managers change without code changes.

---

## 1. The headline finding

**Staging and production do not have the same tracking.**

| | `staging4.temp.tattoopanda.com` (design target) | `pandatattoo.com` (production) |
|---|---|---|
| Google Tag Manager | ❌ none | ✅ `GTM-M3WW4NT7` |
| GA4 (direct gtag.js) | ❌ none | ✅ `G-YBBGK3PZCZ` |
| Meta Pixel | ❌ none | ✅ `2148964251981412` |
| Meta Open Bridge / CAPI gateway | ❌ none | ✅ (see §3) |

Verified on staging across **all 9 pages** — `window.fbq`, `window.gtag`, `window.dataLayer`
all `undefined`, and zero matches for `GTM-`, `G-`, `UA-`, `AW-`, `fbq(`, `googletagmanager`
or `connect.facebook` in any captured HTML.

**Consequence:** a literal 1:1 replica of staging ships with **no analytics at all**.
The tracking must be lifted from **production**, not from the design target. That is what
this file is for.

**None of it exists in any file on disk.** It is injected from the database — the
`insert-headers-and-footers` (WPCode) plugin is installed, which stores snippets in
`wp_posts` (`post_type = 'wpcode'`) and `wp_options`. Confirm against the DB dump when it
arrives; the snippets below are captured from the live DOM and are authoritative regardless.

---

## 2. Carry these over verbatim

### 2.1 GA4 — `<head>`, first
```html
<script async src="https://www.googletagmanager.com/gtag/js?id=G-YBBGK3PZCZ"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-YBBGK3PZCZ');
</script>
```

### 2.2 Google Tag Manager — `<head>`, immediately after GA4
```html
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-M3WW4NT7');</script>
```

### 2.3 GTM noscript — **first element inside `<body>`**
```html
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M3WW4NT7"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
```

### 2.4 Meta Pixel — near the end of `<body>`
```html
<script>!function(b,e,f,g,a,c,d){b.fbq||(a=b.fbq=function(){a.callMethod?a.callMethod.apply(a,arguments):a.queue.push(arguments)},b._fbq||(b._fbq=a),a.push=a,a.loaded=!0,a.version="2.0",a.queue=[],c=e.createElement(f),c.async=!0,c.src=g,d=e.getElementsByTagName(f)[0],d.parentNode.insertBefore(c,d))}(window,document,"script","https://connect.facebook.net/en_US/fbevents.js");fbq("init","2148964251981412");fbq("track","PageView");</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=2148964251981412&ev=PageView&noscript=1"></noscript>
```

All four are **site-wide** — identical on `/` and `/contact/` on production, with no
page-specific variants. Put them in `inc/header.php` / `inc/footer.php` and they are done.

Placement matters: GA4 and GTM in `<head>` (GA4 first, matching production), the GTM
`<noscript>` as the very first `<body>` child, the pixel late in `<body>`. Keep that order —
moving GTM below other scripts changes what its tags can observe.

---

## 3. Meta Open Bridge — no action, but know it exists

Production fires an extra request to:
```
https://od-18c216b54e2045bdbb2d2f70bc4834b0.ecs.us-east-2.on.aws/events?cee=no
```
and the pixel's own request carries `eid=ob3_plugin-set_…`. That is Meta's **Conversions API
Gateway (Open Bridge)** — a server-side event relay configured **inside Meta Events Manager
against the pixel ID**, not in site code. It is delivered by `fbevents.js` at runtime.

Keep the same pixel ID and it keeps working. Change the pixel ID and it silently stops.
Nothing to copy.

---

## 4. Other third-party scripts on staging (the design target)

These are in the theme code and must be carried over as part of the rebuild:

| Host | What | Where |
|---|---|---|
| `link.smartwebsite360.com` | GHL / LeadConnector `form_embed.js` | every page with an embed |
| `link.smartwebsite360.com` | booking calendar widget `oHN0M6e18FAfLByWox01` | home, gear, contact, gallery, privacy, terms |
| `link.smartwebsite360.com` | inline form `B1LLvOARhRLPJ7570tJD` | contact |
| `link.smartwebsite360.com` | inline form `YesKgIXFuU4MFuCv4lRU` | giveaway |
| `backend.leadconnectorhq.com` | GHL attribution session (fires inside the widget iframe) | automatic |
| `cdn.jsdelivr.net` | Alpine.js 3.14.0 | site-wide |
| `cdnjs.cloudflare.com` | animate.css 4.1.1 | site-wide |
| `fonts.googleapis.com` | Inter, Space Grotesk | site-wide |
| `images.ctfassets.net` | Contentful artist images | home, gallery |
| `www.tiktok.com` | `embed.js` + video embeds | tattoo-fight |
| `www.youtube.com` | video embed | check page |
| `maps.google.com` | address link `254 NW 36th St, Miami, FL 33127` | contact |
| `static.vecteezy.com` | one avatar placeholder image | tattoo-fight |
| `www.instagram.com` | outbound profile links (no script) | home, gallery |

⚠️ **`pandatattoo.com` — 161 references in staging's HTML.** Staging serves logos and
uploads cross-domain from the production box (the earlier Lighthouse run shows the same
pattern against `staging2.tattoopanda.com`). These are hardcoded absolute URLs, including
in `studio-child/header.php`. **Localise every one during the rebuild** or the new site
stays hostage to a box we're trying to retire.

---

## 5. Not present anywhere — do not add

Checked and absent on both sites: Google Ads conversion (`AW-`), Universal Analytics (`UA-`),
Microsoft Clarity, Hotjar, TikTok Pixel (`ttq`), Snap Pixel, Pinterest Tag, LinkedIn Insight,
HubSpot, Segment, Mixpanel, PostHog.

No SEO plugin output either — production has no `og:`/`twitter:` tags and no meta
description. Worth fixing in the rebuild, but flag it as a change rather than a replica.

---

## 6. Verify after launch

- [ ] GTM preview mode connects and the container fires on every page
- [ ] GA4 realtime shows the new site
- [ ] Meta Events Manager shows `PageView`, and Open Bridge still shows server events
- [ ] `/wp-json/` and WordPress-specific tags are gone (they leak the old stack)
- [ ] Re-run this audit against production the week of launch, in case tags were added since
