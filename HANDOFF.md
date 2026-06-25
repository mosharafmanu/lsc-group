# LSC Group Theme Handoff

_Last updated: 2026-06-25 (session 4)._

## Current State

The theme is being built from the LSC Capital design (homepage + inner pages).

- Visual system: warm cream/stone backgrounds, near-black text, orange accent `#ff8a3b`, Instrument Sans, uppercase compact headings, rounded cards.
- Section work is **code-first**: PHP templates + ACF JSON only. **CSS is handled separately** (see "Who owns the CSS" below) — except the stacked-testimonials styles, written this session with explicit authorisation.
- Local WordPress: `http://localhost/ClientProjects/WordPress/2026/lsc/`.

## 🔄 RESUME HERE (after shutdown) — session 4 end

**Single Case Study page + Downloads CPT — DONE, committed and pushed.** `main`, `faisal`, `imran` are all aligned on GitHub at the session-4 commit (local working tree clean). Nothing in progress.

**What session 4 delivered (all detailed below):**
- **Single Case Study page** via a dedicated `single-case_study.php` template (full-width hero → two-column [cms left + Case Summary sidebar right] → template-rendered Related Case Studies → template-rendered Global CTA).
- New cms sections for the case study left column: **`rich_text`** (body copy), **`stats_section` Cards style** (merged — was a short-lived `stat_cards`), **`quote_block`** (Manual / Testimonial Library). Plus **`media_full`** (reusable full-width media band).
- New global **`apply_now_link`** (Site Settings → Global CTA) + `lsc_get_apply_now_link()` — the sidebar button.
- **Case Study Details** group (`group_case_study.json`) — the sidebar meta.
- Shared partials: `template-parts/cards/case-study-card.php` + `template-parts/cta-band.php` (both `case_studies_grid` / `cta_section` refactored to use them).
- **Downloads:** new **`download` CPT** (library, like Testimonial) + `group_download.json` (subtitle + PDF) + **`downloads_section`** layout + `assets/svgs/download.php`.

**Do these on any environment that pulls this commit (local already done except where noted):**
1. **WP Admin → Custom Fields → Sync** — picks up: *Page Builder* (new layouts: `media_full`, `rich_text`, `quote_block`, `downloads_section`; `stats_section` Style/Columns; `cta_section` unchanged markup), *Site Settings* (Apply Now Link), and two **new groups** *Case Study Details* + *Download Details*.
2. **Settings → Permalinks** flush (case study singles; `download`/`testimonial` are non-public).
3. **Set Apply Now Link** + fill **Global CTA** in Site Settings.
4. **Faisal:** `git pull` before his next `faisal.css` push. **CSS still to do** (BEM hooks emitted, unstyled): `.case-study-layout` two-column grid + nested-container reset + sticky `.case-summary` sidebar; `.rich-text`; `.stats-section--cards`; `.quote-block`; `.media-full`; `.downloads-section`/`.download-item`; `.case-studies-section--related`.
5. Author **Downloads** (title + subtitle + PDF + order) and build the **Downloads page** (`inner_hero` + `downloads_section`); author **Case Study** posts.

### `single-case_study.php` — the case study page skeleton (NEW, the agreed architecture)

The single case study is **not** built from one mega-section. A dedicated `single-case_study.php` template (WP picks it up automatically for the `case_study` CPT) owns the skeleton and **runs the `cms` flexible content itself** (a custom loop, not the plain `lsc_flexible_content()` dispatcher) so it can split the sections:

```
HERO (inner_hero)        → full width (breaks out of the columns)
┌──────────────────────────┬─────────────────┐
│  rest of cms → LEFT col  │  Case Summary    │  ← two-column band
│  (body / stats / quote / │  sidebar (right, │     sidebar = template,
│   media, etc.)           │  sticky)         │     reads CPT meta + Apply Now
└──────────────────────────┴─────────────────┘
Related Case Studies + CTA → handled SEPARATELY (not in this loop — TBD)
```

**The loop's rule (locked with the user):**
- **Breakout list = `inner_hero` only** (`$lsc_fullwidth_layouts` in the template). When it's the first section it renders full-width at the top, like a normal page hero.
- **Every other cms section** flows into the **left column** of the two-column band.
- The **Case Summary sidebar is part of the template, not a flexible section.** It's built once (output-buffered into `$lsc_sidebar_html`) and dropped into the right column. It reads the `client_type` / `sector` / `funding_requirement` / `outcome` post meta and the global `apply_now_link`. Blank meta rows hide themselves.
- **Related Case Studies is rendered by the template** (below the band, full-width) — **not** a cms section. A `WP_Query` pulls the 3 latest *other* `case_study` posts (`post__not_in` the current one, `no_found_rows`), under a fixed "Related Case Studies" header, and renders each via the shared card partial. Hidden when there are no other case studies. **CTA is rendered by the template** (full-width, below Related) straight from the **Global CTA** (Site Settings) via `lsc_get_global_cta()` — no per-case-study fields. Fill the Global CTA once and every case study shows it; it self-hides if the Global CTA is empty.

**Shared case study card partial (NEW):** `template-parts/cards/case-study-card.php` renders one card (image/title/excerpt/"Read Case Study" link) from `$args['post_id']`. Used by **both** `case_studies_grid.php` (the flexible section, refactored to call it) **and** the single template's Related block — one source of truth for the card markup. BEM unchanged: `.case-study-card › __media/__image, __content › __title/__excerpt/__link`. Related section reuses `.case-studies-section` (+ `--related` modifier for any distinct bg) so Faisal's existing CSS applies.

**Shared CTA band partial (NEW):** `template-parts/cta-band.php` renders the CTA band (eyebrow/title/copy/buttons on a `bg-lsc-*` background) from normalised `$args`. `cta_section.php` was refactored to a thin wrapper that resolves global-vs-custom and delegates to it; the single template calls it directly with `lsc_get_global_cta()`. BEM unchanged (`.cta-section …`) so Faisal's existing CSS applies.
- ⚠️ **Constraint:** left-column sections must stay **contiguous** between the hero and anything full-width. A breakout layout dropped mid-body would close the band and re-emit a second sidebar. For the case study design that never happens; the close-on-breakout branch is kept only for robustness.

**No dedicated body section.** The left column just runs whatever cms sections the editor stacks (body copy, stats, quote, media). Author the Challenge/Strategy/Solution/Result with existing content sections — we did **not** build a bespoke `case_study_content` layout (the user explicitly wanted everything managed from the template).

**BEM the template emits (CSS is Faisal's):**
- Band: `.case-study-layout` › `.lsc-container.layout-padding` › `.case-study-layout__grid` › `.case-study-layout__main` (left) + `.case-study-layout__sidebar`‹aside› (right).
- Sidebar: `.case-summary` › `__title` + `__list`‹dl› › `__row` › `__label`‹dt› / `__value`‹dd› + `__apply` › `__apply-btn`.
- ⚠️ **Faisal must reset the nested container inside the left column** — the cms sections bring their own `.lsc-container.layout-padding`, which double-wraps inside `.case-study-layout__main`. Reset it to `max-width:none; padding-inline:0` there. The two-column grid + sticky sidebar are his too.

**`media_full`** stays as a reusable generic full-width media section — it's no longer the case study lead (the lead is just a cms section in the left column). Remove it if you don't want the spare layout.

---

## 🔄 RESUME HERE (after shutdown) — session 3 end

**Everything is committed and pushed. HEAD = `093d8f3`. `main`, `faisal`, `imran` are all aligned on GitHub at `093d8f3`** (local working tree clean). Nothing in progress, nothing uncommitted.

**Do these once when you come back / on any environment that pulls `093d8f3`:**
1. **WP Admin → Custom Fields → Sync** — picks up the *Page Builder* changes (new layouts: `case_studies_grid`, `feature_columns`, `feature_cards`, `process_steps`, `faqs`; testimonials Source/Layout fields; contact Background Style) **and** the new *Testimonial Details* group.
2. **Settings → Permalinks** (just open it, or flush) so the new `/case-study/…` single URLs resolve. *(Already flushed on local.)*
3. **Faisal:** `git pull` before his next `faisal.css` push — this commit touched `faisal.css` (stacked-testimonials styles), so pulling first avoids a conflict.

**What this session (3) delivered (all detailed in sections below):**
- `contact_section` light/dark Background Style toggle.
- New sections: `feature_columns`, `feature_cards`, `process_steps`, `faqs` (jQuery accordion), `case_studies_grid`.
- **Testimonial CPT** (reusable library) + testimonials_section **Carousel/Stacked** layout toggle + **Manual/Library** source toggle. Stacked CSS written in `faisal.css`. `bin/` migrate + cleanup + seed scripts (already run on local → 17 real testimonials live; Home + Testimonials page on Library/All).
- **Case Study CPT** (public, single pages) + `case_studies_grid` section.
- Cache-bust version now `LSC_GROUP_VERSION = 1.0.46` (bump it on any future CSS/JS edit or the browser serves stale assets — see ACF/asset notes).

**Likely next tasks (not started):**
- Faisal: CSS for the session-3 sections (`feature_columns`, `feature_cards`, `process_steps`, `faqs`, `case_studies_grid`, contact `--bg-dark`) — only stacked-testimonials CSS is done.
- Author **Case Study** posts (title + featured image + excerpt + page order) so the grid populates; spot-check the 6 blank testimonial role lines + Keith M quote (see Testimonial CPT note).
- The **DEFERRED contact-overlap section** (`contact_panel`) is still pending — full spec further below.

---

## New this session (4) — Single Case Study page (in progress)

Started building the **single Case Study** page (design: dark hero → two-column body+sidebar → Related Case Studies → dark CTA). Composed in the `cms` flexible builder on each `case_study` post. Two pieces landed; the two-column content section is next.

### Case Study Details — the Case Summary sidebar meta (`group_case_study.json`, NEW)
- New ACF field group **"Case Study Details"** attached to the `case_study` CPT (post meta, mirroring the `finance_product` → Key Facts precedent — chosen because the summary is intrinsic post data, filled once, queryable, and survives section reordering).
- Four **fixed** text fields under a **Case Summary** tab (we chose fixed named fields over a label/value repeater because the design's schema is standardized): `client_type`, `sector`, `funding_requirement`, `outcome`. All optional — a blank field hides its row in the sidebar (guard in the template when built). Each has `wrapper.width: 50` so the admin lays them out 2-per-row.
- **Apply Now** intentionally has **no field** — it comes from a global link (Site Settings) so it's not re-typed per post.
- The sidebar markup itself is **not built yet** — it renders inside the two-column `case_study_content` section (section #6 in the resume table). This commit only adds the data model.
- ⚠️ Brand-new group → Custom Fields → Sync offers it regardless of timestamp.

### `media_full` — Full-Width Media section (NEW)
The lead media on the case study page (rounded full-width image or video). Reusable anywhere a standalone media band is needed.
- ACF layout `media_full` ("Full-Width Media") appended to `group_flexible_content.json` `cms`. Minimal fields: `media_type` button-group (image/video, default image), `image` (conditional on image), `video` **group cloned 1:1 from `media_content_5050`** (full `lsc_render_video()` option set + conditional logic, key prefix `field_media_full_*`).
- Template `template-parts/sections/media_full.php` — auto-dispatched. One full-width media block inside `lsc-container layout-padding`; bails if neither image nor video set. Same **`controls`-only-on-autoplay** fix as the other video sections.
- **BEM (CSS is Faisal's):** `.media-full` (+ `--image`/`--video`) › `__media` › `__figure`/`__image` (image) or `__video-wrap`/`__video` (video). Rounded corners / aspect ratio are his.
- ⚠️ `"modified"` bumped — Custom Fields → Sync.

---

## ✅ Earlier batches (sessions 1–2) — context

Two 2026-06-24 batches were committed and pushed; **`main`, `faisal`, and `imran` are all aligned** on GitHub (nothing lost). Faisal's CSS commits (`77aa3e2`, `93c428e`, `f5b7a71` — `faisal.css`, `lsc-group-design-style.css`, `video-behaviors.css`) are merged into `main` too.

- **Batch 1 (`24d17d1`):** inner_hero, broker_callout, content_card_5050, timeline_section, cta_section, the `media_content_5050` → `media_card_5050` rename, header two-CTA, finance-grid orphan centering.
- **Batch 2 (this session):** the inner-page hero system below. Files:
  - `inner_hero.php` — single **Page Hero** (Hero Style selector; Content/Media tabs; Text+Media video; product Key Facts bar via `show_facts_bar`).
  - `media_content_5050.php` (NEW) — 50/50 content + checklist beside image/video; **Enable Background** toggle; baked `mt-50 mt-lg-90` top gap.
  - `acf-json/group_flexible_content.json` — Page Hero fields, `media_content_5050` layout, video-group fields at 50% width.
  - `acf-json/group_finance_product.json` (NEW) — product **Key Facts** meta (`product_facts` repeater) on the `finance_product` CPT.
  - `inc/helper-functions/acf-field-visibility.php` (NEW) — `lsc_get_admin_post_type()` + filter that hides the facts-bar toggle off Finance Products.
  - `acf-json/group_site_settings.json` + `inc/helper-functions/site-settings.php` — Opening Hours (staged for the deferred contact panel).
- `stats_section.php` is **untouched** — general-purpose, not an overlap section.

### `contact_section` — Background Style (light/dark) — session 3
The general `contact_section` (the "WE ARE ALWAYS LOOKING FOR BROKERS" enquiry block) now works on **both** the light homepage background and the dark inner-page background via one toggle — same section, no duplicate layout.
- ACF: new **Background Style** button-group (`background_style`, choices `light`/`dark`, default `light`) under the Section Header tab in `group_flexible_content.json`. Existing rows default to Light → homepage unaffected.
- Template (`contact_section.php`): emits a modifier class `.contact-section--bg-light` / `.contact-section--bg-dark` on the `<section>`.
- **CSS is Faisal's:** `.contact-section--bg-dark` is the hook for the dark look (dark page bg, orange "GET IN TOUCH" heading, light body/label text). Markup/class are in place; dark visual rules to be added in `faisal.css`.
- ⚠️ Sync needed: `group_flexible_content.json` `"modified"` was bumped — Custom Fields → Sync to pick up the new field.

**After pulling: WP Admin → Custom Fields → Sync** (Page Builder changed + new *Finance Product Details* group).

After any ACF JSON change: **WP Admin → Custom Fields → Sync**.

> ⚠️ **Hand-edited JSON won't show "Sync available" unless you bump `"modified"`.** ACF compares the JSON file's top-level `"modified"` timestamp to the group's DB timestamp; a manual file edit leaves it unchanged so no sync is offered. After editing any `acf-json/*.json` by hand, set `"modified"` to a current Unix timestamp (greater than the last admin-Save value), then Sync. Full note in `.ai/ACF-PATTERNS.md`.

## Inner-page hero system (session 2)

**One client-facing layout — "Page Hero"** (machine name stays `inner_hero` so existing placements don't break) — covers **all five** inner-page hero designs via a single **Hero Style** selector. There is intentionally **no second hero layout**; an earlier `page_intro` idea was folded into this to avoid confusing editors.

| Design | Page | Hero Style + add-on |
|---|---|---|
| Dark image hero | About | **Image Background** |
| Dark hero + key-facts bar | Product detail | **Image Background** + **Show Product Key Facts Bar** = Yes (facts come from the product's meta) |
| Dark hero + form/contact cards | Contact | **Image Background**, then a dedicated contact-panel section (TBD) with *Overlap The Hero Above* = Yes |
| Light text-only intro | Brokers | **Text Only** |
| Light split (text + media) | Testimonials | **Text + Media** (image **or** video; Media Position left/right) |

- **Hero Style** field (button group): `image` (default) / `text` / `split`. Default `image` means every existing `inner_hero` placement renders exactly as before — **no DB migration**.
- **Tabbed editor:** the Hero Style selector sits at the top, then fields are split across a **Content** tab (eyebrow, title, description, buttons) and a **Media** tab (media type, image, media position, video, overlap) to keep the form short. Conditional logic hides irrelevant fields per style.
- **Text + Media supports video.** The split style has a `media_type` (Image/Video) toggle and the full `video` group cloned 1:1 from `broker_callout` — same `lsc_render_video()` option set and the same `controls`-only-on-autoplay fix. Image is shown for Image Background, or for Text + Media when Media Type = Image.
- **BEM:** base block stays `.inner-hero` (Faisal's existing dark-hero CSS keeps working) with style modifiers `.inner-hero--image|--text|--split`, plus `.inner-hero--image-left|right` (split) and `.inner-hero--has-facts` (image hero showing the product key-facts bar). Split video renders into `.inner-hero__media` (`.inner-hero__video-wrap` / `.inner-hero__video`). Title is `<h1>`; hero images are LCP (`lazy=false`, `fetchpriority=high`).
- **Product Key Facts bar = product meta + hero toggle (product detail design).** Facts are stored on the **Finance Product** post itself (`group_finance_product.json` → `product_facts` repeater: label + value + highlight), filled once on the product. The Page Hero has a **"Show Product Key Facts Bar"** toggle (Image Background style only) that renders the current product's facts as a bar overlapping the hero bottom — so the overlap is *inside one section*, no cross-section coordination. The hero reads `get_field('product_facts')` and no-ops if empty.
  - The toggle is **hidden unless you're editing a Finance Product** — done via `acf/prepare_field/key=field_inner_hero_show_facts_bar` in `inc/helper-functions/acf-field-visibility.php` (ACF conditional logic can't key off post type). `lsc_get_admin_post_type()` lives there too.
  - Facts bar markup: `.inner-hero__facts-wrap` › `.inner-hero__facts` (`card-grid columns-N`, N auto-matches fact count) › `.inner-hero__fact` › `.inner-hero__fact-label` + `.inner-hero__fact-value` (`color-lsc-accent` when highlighted). The overlap-into-the-section-below CSS is Faisal's.
  - The earlier standalone `key_facts` section was **removed** in favour of this product-meta approach.
- **Contact overlap** — **DEFERRED, build at the contact page.** Full spec in "DEFERRED: Contact overlap section" below. The existing `stats_section` / `contact_section` stay general-purpose and untouched.

**Flexible content order:** `inner_hero` (labelled "Page Hero") stays in its existing slot, right after `hero_section`.

## DEFERRED: Contact overlap section (build when we reach the contact page)

> Not built yet — by the user's request. This is the design in screenshot #5/#11: the contact Page Hero (Image Background) with two white cards overlapping its bottom: an **enquiry form** on the left and a **contact-information** card on the right, with a **map** below the info card. Build it as its own NEW section — do **not** modify the existing general `contact_section`.

**Hard rules (decided with the user — don't relitigate):**
- It is a **brand-new, separate** flexible-content section. The existing `contact_section.php` is general-purpose and must stay untouched.
- Overlap is a **single self-contained toggle on this section**: `overlap_hero` true/false labelled **"Overlap The Hero Above"** (reuse the exact wording already used elsewhere: *"Enable when this section sits directly below a hero — it will move up and overlap the bottom of that hero. Leave off to render as a normal full-width section."*). ON → add a `--overlap` modifier class; OFF → normal full-width section. **No paired toggle on the hero** — the Page Hero stays unaware. The negative-margin/overlap CSS is **Faisal's**.
- CSS is **Faisal's**: emit BEM hooks only, no styling.

**Proposed name:** `contact_panel` (machine + `contact_panel.php`), label **"Contact Panel"** — distinct from the general `contact_section`. Confirm the name with the user before building.

**Fields (ACF layout in `group_flexible_content.json`):**
- Form card: `form_title` (text, e.g. "Send An Enquiry") + `form_code` (textarea — a form-plugin shortcode, rendered via `do_shortcode()`, mirroring `contact_section`'s `form_card`).
- Info card: `info_title` (text, e.g. "Contact Information"). The email / phone / office address / **opening hours** come from **Site Settings** via `lsc_get_footer_contact_details()` — which already returns `address`, `phone`, `email`, `linkedin`, `hours`. **Opening Hours is already wired** (`footer_opening_hours` in `group_site_settings.json` + the helper) specifically for this section — don't re-add it.
- Map: `map_embed` (textarea — Google Maps iframe or a map shortcode, rendered via `do_shortcode()` like `form_code`).
- `overlap_hero` (true/false) — the overlap toggle described above.
- **Decision still open:** contact details from **Site Settings** (recommended — single source of truth, and Opening Hours is already there) **vs** the section having its **own** email/phone/address/hours fields. Confirm with the user; default to Site Settings.

**Layout (from the design):** form card left (wider), info card right (narrower); the **map sits below the info card** in the right column. When `overlap_hero` is on, the whole section tucks up over the hero above (Faisal's CSS).

**Proposed BEM:** `.contact-panel` (+ `.contact-panel--overlap`) › `.contact-panel__grid` › `.contact-panel__form` (`.contact-form-card`…) and `.contact-panel__info` (`.contact-info-card`…) + `.contact-panel__map`. You can lift the info-card / form-card markup from `contact_section.php` (address/phone/email items via `lsc_get_icon_svg`, opening hours block, social/LinkedIn) — copy it into the new template rather than sharing, since the two sections are intentionally separate.

**Staged now, pending this build:** `group_site_settings.json` + `inc/helper-functions/site-settings.php` already carry the **Opening Hours** field/return value. If the contact panel is ever abandoned, that field can be removed.

## Who owns the CSS (important workflow rule)

- **Claude/AI does PHP + ACF JSON + markup only.** Faisal owns the design CSS in `faisal.css`.
- **Do NOT add CSS for new sections** — only output sensible BEM classes; Faisal styles them.
- `faisal.css` may be edited **only when explicitly authorised** (e.g. the `media-card-5050` rename, the grid-gap variable).
- The framework grid file `assets/css/lsc-group-design-style.css` may be touched for **grid/plumbing behaviour** when genuinely required (e.g. orphan-centering) — it is theme plumbing, not Faisal's section styling.
- Video options must map **1:1 to existing `lsc_render_video()` args** — never invent new behaviour the renderer doesn't already support.
- Prefer **global/reusable** section names (describe what the section *is*, not the page it's used on).

## Flexible Content layout order (`cms`)

| # | Layout | Template | Notes |
|---|---|---|---|
| 0 | `hero_section` | `hero_section.php` | Image/video hero (homepage) |
| 1 | `inner_hero` | `inner_hero.php` | Labelled **"Page Hero"** — Hero Style image/text/split; image style can show the product Key Facts bar |
| 2 | `finance_products_grid` | `finance_products_grid.php` | Centers last-row orphans (see below) |
| 3 | `broker_callout` | `broker_callout.php` | **NEW** — orange callout card |
| 4 | `stats_section` | `stats_section.php` | General stats grid — **Style** toggle Band/Cards (default Band) + Columns 2/3/4 (session 4) |
| 5 | `media_card_5050` | `media_card_5050.php` | **RENAMED** from `media_content_5050` |
| 5b | `media_content_5050` | `media_content_5050.php` | **NEW** — 50/50 content + checklist beside image/video; optional bg + Top Spacing |
| 6 | `testimonials_section` | `testimonials_section.php` | Slick carousel **or** stacked full-width blocks (Layout toggle) |
| 7 | `contact_section` | `contact_section.php` | General contact section (untouched) |
| 8 | `content_card_5050` | `content_card_5050.php` | **NEW** — content + checklist beside a card + image |
| 9 | `timeline_section` | `timeline_section.php` | **NEW** — auto-numbered journey steps |
| 10 | `cta_section` | `cta_section.php` | **NEW** — pulls from Global CTA by default |
| 11 | `feature_columns` | `feature_columns.php` | **NEW** — 3-column: intro content + info-card stack + dark action card over image |
| 12 | `feature_cards` | `feature_cards.php` | **NEW** — centered header + icon/title/copy card grid (2/3/4 cols) |
| 13 | `process_steps` | `process_steps.php` | **NEW** — centered header + auto-numbered step row (2/3/4 cols) |
| 14 | `faqs` | `faqs.php` | **NEW** — centered header + jQuery slide-toggle accordion (plus/minus) |
| 15 | `case_studies_grid` | `case_studies_grid.php` | **NEW** — header + grid of Case Study CPT cards (image/title/excerpt/link) |
| 16 | `media_full` | `media_full.php` | **NEW (session 4)** — full-width rounded media block, image **or** video (renderer-backed). Reusable generic band |
| 17 | `rich_text` | `rich_text.php` | **NEW (session 4)** — single free-form WYSIWYG block; reuses global `.entry-content` typography. Used for case study body copy (Challenge/Strategy/…) and reusable anywhere |
| 18 | `quote_block` | `quote_block.php` | **NEW (session 4)** — single dark pull-quote (mark + quote + author/avatar). **Source toggle** Manual / Testimonial Library (pick one). Used for the case study client quote |
| 19 | `downloads_section` | `downloads_section.php` | **NEW (session 4)** — optional header + stacked list of PDF download rows (icon + title + subtitle + Download button). Pulls from the **Download CPT** (Source all/selected). Drives the Downloads page |

> **Download CPT + `downloads_section`** (row 19) — the Downloads page, modelled like the Testimonial CPT (reusable library). **CPT `download`** (`inc/post-types.php`, `lsc_register_download_post_type`): not public, admin-only, `supports` title + page-attributes (title = document name, page order = list order), menu icon `dashicons-media-document`, position 23. **ACF group `group_download.json`** on it: `subtitle` (text) + `file` (File, **PDF**, required). **`downloads_section`** has the same Source pattern as the finance/case-study grids — `download_source` all/selected, `selected_downloads` relationship, `posts_per_page`, `orderby`, `order` — plus optional eyebrow/title/description. Renders a stacked `<ul>` list (full-width rows, not a grid). New SVG `assets/svgs/download.php` (currentColor download arrow) used for the row icon and inside the Download button. Download button reuses `site-btn btn-primary` + emits `download target="_blank"` to the PDF. **BEM (Faisal's CSS):** `.downloads-section › __inner/__header (__eyebrow/__title/__description) + __list‹ul› › .download-item › __icon + __content (__title‹h3›/__subtitle) + __button (site-btn btn-primary) › __button-text/__button-icon`. The Downloads **page** = a normal Page: `inner_hero` (USEFUL DOCUMENTS / DOWNLOADS) + `downloads_section`. ⚠️ Sync picks up the new *Download Details* group **and** the *Page Builder* (new Downloads layout); then author Downloads under the new admin menu.
>
> **`quote_block`** (row 18) — single dark pull-quote for the case study client quote. **Source toggle** (mirrors `testimonials_section`): **Manual** (default — `quote`, `author_name`, `author_role`, `avatar`) **or** **Testimonial Library** (a relationship limited to **one** testimonial; maps title→name, CPT `quote`/`author_role`/`author_initial`). Both sources normalise to one shape; bails if no quote. **Avatar fallback:** no image → the author's **initial** in a circle (first letter of the name, or the CPT's `author_initial`). Quote mark = `assets/svgs/quote` (orange). **BEM (Faisal's CSS):** `.quote-block` › `.lsc-container.layout-padding` › `__card`‹figure› › `__mark` + `__quote`‹blockquote› + `__author`‹figcaption› › `__avatar` (`__avatar-img` / `__avatar-initial`) + `__meta` › `__name` / `__role`. Dark rounded card is his.
>
> `stats_section` (row 4) now carries a **Style** toggle (Band / Cards) — the case study stat cards are the **Cards** style, not a separate section. A short-lived standalone `stat_cards` layout was built then **merged into `stats_section`** to avoid two near-identical sections confusing editors.
>
> **Same data model + markup, two looks via a modifier.** Template emits `.stats-section--band` (keeps `bg-lsc-subtle`, the existing look — default, so existing placements are unchanged) **or** `.stats-section--cards` (no subtle bg; grid gains `card-grid--center-last-row`). Inner markup is identical in both: `.stats-section__item › __value + __label`. **Faisal (CSS):** style `.stats-section--cards .stats-section__item` as the white rounded bordered card with the big orange `__value` + uppercase `__label`. The Band look is already done. Columns come from the `columns-N` class on `.stats-section__grid`.

> The single Case Study page is **not** a flexible layout — it's the dedicated `single-case_study.php` template that runs `cms` in its left column beside a template-rendered Case Summary sidebar (see the session-4 resume section).

## New sections built this session

### `broker_callout`
- Content tab: `title_lines → line_parts`, `description` (WYSIWYG), `chips`.
- Media tab: `media_position` (left/right), `media_type` (image/video), `image`, `video` group.
- **Chips** = label + optional link. Renders `<span>` when no link, `<a>` when linked (FCA-status pills like "Unregulated" / "Directly Authorised" / "Appointed Representative").
- Video group exposes the full `lsc_render_video()` option set as fields (behavior, autoplay, autoplay_on_scroll, controls, muted, loop, popup_autoplay, popup_controls) with instructions + 2-up wrapper widths. Defaults mirror the renderer's own defaults.

### `inner_hero`
- Image background (LCP: `lazy=false`, `fetchpriority=high`), eyebrow badge, `title_lines` (`<h1>`), `description`, `buttons`. **No video, no overlay div** (scrim handled in CSS).

### `content_card_5050`
- Content tab: `eyebrow`, `title_lines`, `description`, `features` (checklist — labels only; check icon via CSS `::before`).
- Side Card tab: `card_position` (left/right), `card_title`, `card_description`, `card_buttons`, `image` (image sits below the card).

### `timeline_section`
- `eyebrow`, `title_lines`, `description`, `items` repeater (`year`, `title`, `description`). Step numbers (1,2,3…) auto-increment from the loop. Semantic `<ol>`. Connector line + number circles via CSS.

### Testimonial CPT — reusable testimonial library — session 3
So testimonials are authored **once** and reused across pages instead of re-typed per section (mirrors the `finance_product` CPT + finance-grid Source pattern).
- **New CPT `testimonial`** (`inc/post-types.php`, `lsc_register_testimonial_post_type`) — content library, **not public** (`public/publicly_queryable/query_var` false, `rewrite` false, `exclude_from_search` true, no archive/single). Admin only. `supports` = `title` + `page-attributes`; **post title = author name**, page order drives "Page Order" sorting. Menu icon `dashicons-format-quote`, position 21 (under Finance Products).
- **New ACF group `group_testimonial.json`** on the CPT: `quote` (textarea, required), `author_role` (text), `author_initial` (text, defaults to first letter of title), `rating` (select 1–5). No `theme`/`layout` here — those are per-placement (live on the section).
- **`testimonials_section` gained a Source toggle** (mirrors finance grid): `source` (`manual`/`library`, default `manual`), and when `library`: `library_selection` (`all`/`selected`), `selected_testimonials` (relationship → testimonial CPT), `posts_per_page`, `orderby` (Page Order/Date/Author Name), `order`. The original `testimonials` repeater is now conditional on `source = manual` (min dropped 1→0).
- **Template** normalises **both** sources into one `$items` shape `[rating, quote, author_name, author_role, author_initial, theme]`, then the Carousel/Stacked loops iterate `$items`. Library items map post title → `author_name`, ACF fields → the rest, and `theme = auto` (so Stacked still position-cycles dark→orange→light). `WP_Query` uses `no_found_rows`.
- Default `source = manual` → **existing sections unchanged**; nothing to migrate.
- ⚠️ Sync needed: Custom Fields → Sync picks up **two** changes — *Page Builder* (Source fields) **and** the new *Testimonial Details* group. Then author testimonials under the new **Testimonials** admin menu.
- **Migration scripts (`bin/`, both CLI-only, idempotent, support `--dry-run`):**
  1. `migrate-testimonials.php` — walks every `cms` `testimonials_section` Manual repeater and creates one Testimonial post per entry (title = author name; maps quote/role/initial/rating; stamps `_lsc_migrated_hash`). **Run on local** → created #650 Brian R, #651 John W, #652 Samantha Williamson (1 dup skipped).
  2. `cleanup-testimonial-repeaters.php` — for each still-Manual section whose entries **all** map back to CPT posts (by the same hash), switches it to **Source = Library / Selected** with those testimonials (in order, deduped) and empties the Manual repeater. Sections with any unmapped entry are skipped (so run #1 first; no data loss). **Run on local** → Home (#81) testimonials section now `source=library, selection=selected` → #650/#651/#652, manual repeater emptied.
  - **Order matters on other environments:** run #1 then #2 after pulling on staging/live. Both one-off; safe to delete once all environments are done.
  3. `seed-testimonials.php` — **deletes all `testimonial` posts and reseeds the real 17** transcribed from the live Testimonials page (page order, rating 5). Re-points any Library/Selected section by author name (none currently — both testimonials sections are Library/**All**, so they pick up all 17 automatically). **Run on local** → testimonials #655–#671. ⚠️ **Spot-check needed:** 6 role/subtitle lines were illegible in the source and left **blank** — fill in admin: Sebastiano Carrelli, William H, Michael D, Dave Cookson, Property Saints, Marc Green. Also Keith M's quote: read "would **not** have been able" (the source word was unclear) — verify. Quotes were transcribed from a screenshot; a proofread against the live site is recommended.

### `testimonials_section` — Carousel / Stacked Layout toggle — session 3
Same section now drives **both** testimonial designs (screenshots #27 homepage / #28 testimonial page) off one testimonials repeater — no duplicate content model.
- ACF: new **Layout** button-group (`layout`, `carousel`/`stacked`, default `carousel`) at the top of the Content tab. Per-testimonial **Background** select (`theme`: `auto`/`dark`/`orange`/`light`, default `auto`, conditional on Stacked). `rating` is now conditional on **Carousel** only.
- **Carousel** = unchanged existing Slick markup (stars, quote icon, featured 2nd card).
- **Stacked** = full-width blocks, no stars/quote-icon, big quote watermark + italic quote + author row. Background **auto-cycles dark → orange → light by position** (`$stacked_palette` in the template, `$index % 3`); a per-item `theme` other than `auto` pins that block's colour.
- Template branches on `$layout`; the Slick init only ever sees `.js-testimonials-carousel` (absent in stacked), so no JS change needed.
- **BEM:** section modifier `.testimonials-section--carousel|--stacked`; stacked cards `.testimonial-card--stacked` + `.testimonial-card--theme-dark|orange|light`. Reuses the existing `.testimonial-card__*` author/quote/watermark hooks.
- **Stacked quote mark = the original quote icon (`assets/svgs/quote.php`), NOT the `quote-watermark` glyph.** Stacked branch renders `quote` in `.testimonial-card__quote-icon` (carousel still uses both its own `.testimonial-card__quote-icon` in the header and the faint `.testimonial-card__quote-watermark`). In stacked it sits in **normal flow above the copy** (`margin: 0 0 .75rem`, `line-height: 0`), sized `svg { width: 3rem }` (2.5rem mobile). Coloured via **stroke** per theme: dark/light = accent orange; orange = primary dark + `svg g { opacity: .45 }`.
- **Stacked CSS — written (explicitly authorised exception to the CSS boundary, session 3).** Lives in `faisal.css` just after the carousel testimonial block: `.testimonials-section--stacked .testimonials-section__stack` (flex column, gap), `.testimonial-card--stacked` (full-width, larger padding, un-clamped italic quote, top-left quote-icon watermark), and the three theme blocks (dark = `--lsc-color-dark` + light text; orange = `--lsc-color-accent` + dark quote/white author; light = `#ECEAE3` + dark text), each re-tinting the avatar (white circle) and the watermark stroke. Mobile padding tweak < 768px. ⚠️ **Faisal owns `faisal.css`** — he should `git pull` before his next CSS push so this doesn't conflict; consider folding it into his own structure later. Version bumped to **1.0.43** for cache-bust.
- ⚠️ Sync needed: `"modified"` bumped — Custom Fields → Sync.

### Case Study CPT + `case_studies_grid` — session 3
"Our Case Studies" grid (screenshot #35), mirroring the `finance_product` CPT + finance-grid pattern.
- **New CPT `case_study`** (`inc/post-types.php`, `lsc_register_case_study_post_type`) — **public, with single pages** (the cards link to "Read Case Study"). `rewrite` slug `case-study`, `has_archive` false (listing is the section), `supports` = title/editor/thumbnail/excerpt/page-attributes, icon `dashicons-portfolio`, position 22. Permalinks **flushed** on local; ⚠️ on other environments visit Settings → Permalinks (or flush) once so `/case-study/...` resolves.
- **`case_study` added to the flexible-content `cms` location** (now page/post/product/finance_product/case_study) so single case-study pages build with the page builder, like finance products.
- **New ACF layout `case_studies_grid`** (cloned from `finance_products_grid`): Content tab (eyebrow, title, description) + Grid tab (`case_study_source` all/selected, `selected_case_studies` relationship, `posts_per_page`, `columns` 2/3/4 default 3, `orderby`, `order`).
- **Template `case_studies_grid.php`** — auto-dispatched. Card = featured image (linked) + `<h6>` title (linked) + excerpt + "Read Case Study →" link. Grid reuses `card-grid card-grid--center-last-row columns-N`. Orange divider above heading = `.case-studies-section__divider` hook.
- **BEM (CSS is Faisal's):** `.case-studies-section` › `__inner` / `__header` (`__divider`, `__eyebrow`, `__title`, `__description`) + `.case-studies-grid` (`card-grid…`) › `.case-study-card` › `__media`/`__image`, `__content` (`__title`, `__excerpt`, `__link`). No section bg utility (sits on page cream bg).
- ⚠️ Sync needed: Custom Fields → Sync (Page Builder changed). Then add Case Studies under the new admin menu (title + featured image + excerpt + page order).

### `feature_columns` — Feature Columns (3-Column) — session 3
A 3-column section (screenshot #23): **left** intro content, **middle** a stack of small white info-cards, **right** a highlighted dark action card over an image. Built by extending the `content_card_5050` pattern with a middle info-cards repeater.
- ACF layout `feature_columns` ("Feature Columns (3-Column)") in `group_flexible_content.json`, three tabs:
  - **Content:** `eyebrow`, `title_lines → line_parts` (text + highlight), `description` (WYSIWYG), `features` checklist.
  - **Info Cards:** `info_cards` repeater (`title` + `description`) — the middle stack.
  - **Highlight Card:** `card_title`, `card_description`, `card_buttons` (link + style: primary/outline/secondary — design shows solid "Email us" + outline phone), `image` (sits below the card).
- Template `feature_columns.php` — auto-dispatched by `flexible-content.php` (no dispatcher edit). Title is `<h2>`. Uses `lsc_render_button()` (`show_icon => false`) and `lsc_render_responsive_picture()`.
- **BEM (CSS is Faisal's):** `.feature-columns` › `__inner` › `__content` (`__eyebrow`, `__title`/`__title-line`/`__title-part` + `color-lsc-accent`, `__description`, `__features`/`__feature`) + `__cards` (`__info-card` › `__info-card-title` + `__info-card-description`) + `__aside` (`__card` › `__card-title`/`__card-description`/`__card-buttons` + `__figure`/`__image`). The dark highlight card = `.feature-columns__card`; the three-column grid + dark card styling are Faisal's.
- ⚠️ Sync needed: `"modified"` bumped — Custom Fields → Sync to pick up the new layout.

### `feature_cards` — Feature Cards (Icon Grid) — session 3
Centered header + a responsive grid of icon/title/copy cards (screenshot #24).
- ACF layout `feature_cards` ("Feature Cards (Icon Grid)") in `group_flexible_content.json`, two tabs:
  - **Header:** `title_lines → line_parts` (text + highlight), `description` (WYSIWYG), `columns` button-group (`columns-2/3/4`, default `columns-4`).
  - **Cards:** `cards` repeater — `icon` (optional image, SVG/PNG), `title`, `description`.
- Template `feature_cards.php` — auto-dispatched. Grid reuses `card-grid card-grid--center-last-row columns-N` (same orphan-centering + responsive collapse as the finance grid). Cards titles are `<h3>`.
- **Icon:** each card defaults to a built-in **`check-circle`** icon (added to `lsc_get_icon_svg()` in `inc/helper-functions/site-settings.php`, `currentColor`-based so the accent comes from CSS). An uploaded per-card SVG/PNG overrides it via `lsc_render_icon()`.
- The small orange divider above the heading is a markup hook `.feature-cards__divider` (decorative, styled in CSS).
- **BEM (CSS is Faisal's):** `.feature-cards` › `__header` (`__divider`, `__title`/`__title-line`/`__title-part` + `color-lsc-accent`, `__description`) + `__grid` (`card-grid…`) › `__card` › `__icon` (`color-lsc-accent`, `__icon-svg`) + `__card-title` + `__card-description`. White cards on the page (cream) background; no section bg utility applied.
- ⚠️ Sync needed: `"modified"` bumped — Custom Fields → Sync.

### `process_steps` — Process Steps — session 3
Centered header + a row of auto-numbered steps (screenshot #25).
- ACF layout `process_steps` ("Process Steps") in `group_flexible_content.json`, two tabs:
  - **Header:** `title_lines → line_parts` (text + highlight), `description` (WYSIWYG, optional), `columns` button-group (`columns-2/3/4`, default `columns-4`).
  - **Steps:** `steps` repeater — `title` + `description` only. **No number field** — the badge number comes from the loop index (`$index + 1`), like `timeline_section`.
- Template `process_steps.php` — auto-dispatched. Semantic `<ol>`/`<li>`; grid reuses `card-grid card-grid--center-last-row columns-N`. Step titles are `<h3>`. The orange divider above the heading is a `.process-steps__divider` hook.
- **BEM (CSS is Faisal's):** `.process-steps` › `__header` (`__divider`, `__title`/`__title-line`/`__title-part` + `color-lsc-accent`, `__description`) + `__grid` (`card-grid…`, an `<ol>`) › `__step` (`<li>`) › `__number` (the rounded badge) + `__step-title` + `__step-description`. Number badge styling (rounded white tile, orange numeral) is Faisal's.
- ⚠️ Sync needed: `"modified"` bumped — Custom Fields → Sync.

### `faqs` — FAQs (Accordion) — session 3
Centered header + a jQuery slide-toggle accordion (screenshot #26).
- ACF layout `faqs` ("FAQs (Accordion)") in `group_flexible_content.json`, two tabs:
  - **Header:** `title_lines → line_parts`, `description` (WYSIWYG, optional).
  - **Questions:** `faqs` repeater — `question` (text) + `answer` (WYSIWYG).
- Template `faqs.php` — auto-dispatched. Accessible disclosure pattern: each question is a `<button>` (`aria-expanded`, `aria-controls`) inside an `<h3>`; the answer panel is a `role="region"` with `hidden` until opened. A `static $faq_section_index` keeps aria ids unique when multiple FAQ sections share a page.
- **Icons:** new `assets/svgs/minus.php` (created — the plus with only the horizontal stroke). Each item renders **both** `assets/svgs/plus` (`.faqs__icon-plus`) and `assets/svgs/minus` (`.faqs__icon-minus`); the open/closed swap is driven by `.faqs__item.is-open` / the button's `aria-expanded` — **Faisal's CSS must show minus / hide plus when open** (until then both icons show). The plus SVG uses a hardcoded `#1A1614` @ 0.4 opacity stroke (from the existing `plus.php`).
- **JS:** accordion logic added to `assets/js/scripts.js` (inside the existing jQuery `document.ready`), delegated on `[data-faq-accordion]` → `.faqs__question`. `slideDown`/`slideUp` (300ms), each item toggles **independently** (siblings stay open). Toggles `aria-expanded`, `.is-open`, and the panel's `hidden` prop. No new JS file/enqueue — rides the already-enqueued `lsc-group-scripts`.
- **BEM (CSS is Faisal's):** `.faqs` › `__header` (`__title`/`__title-line`/`__title-part`, `__description`) + `__list[data-faq-accordion]` › `__item`(`.is-open`) › `__question-heading` ‹h3› › `__question` ‹button› (`__question-text` + `__icon` › `__icon-plus`/`__icon-minus`) + `__answer`‹region, hidden› › `__answer-inner`.
- ⚠️ Sync needed: `"modified"` bumped — Custom Fields → Sync.

### `cta_section` + Global CTA
- **Global CTA lives in Site Settings → "Global CTA" tab** (`global_cta_eyebrow`, `global_cta_title_lines`, `global_cta_description`, `global_cta_buttons`, `global_cta_background`).
- Helper `lsc_get_global_cta()` reads those options.
- The CTA section has a **Content Source** toggle: `global` (default — pulls from Site Settings) or `custom` (page-specific). Custom fields are conditional and hidden unless "Custom" is selected.
- Client instructions are embedded in the admin UI (Site Settings intro message + Content Source instructions + custom-fields notice).
- Reuses `bg-lsc-*` background utility (default `dark`).

## Other changes this session

### `media_content_5050` (built, session 2)
- The alternating 50/50 design (#18/#19): heading + copy + checklist on one side, image **or video** on the other. **Built.**
- **Media tab** mirrors `broker_callout`: `media_position` (left/right), `media_type` (image/video), `image`, and the full renderer-backed `video` group (cloned 1:1, 50%-width fields, same `controls`-only-on-autoplay fix).
- **Background + padding rule (per the user):** `background_color` select (None/Light/Subtle/Primary/Dark). When a colour is set → adds `bg-lsc-*` **plus** `pt-50 pb-50 pt-lg-90 pb-lg-90`; when None → flush, no vertical padding. Horizontal gutters always via `lsc-container layout-padding`.
- **Top gap = `mt-*` utilities, not padding:** a `top_spacing` text field takes margin-top utility class(es) (e.g. `mt-60 mt-lg-90`); the template whitelists tokens matching `^mt-(sm-|md-|lg-|xl-|xxl-)?\d+$` before applying.
- **BEM:** `.media-content-5050` (+ `--media-left|right`, + `bg-lsc-*`/`pt-`/`pb-`/`mt-` when set) › `.media-content-5050__grid` › `__media` (`__figure`/`__image` or `__video-wrap`/`__video`) + `__content` (`__eyebrow`, `__title`, `__description`, `__features`/`__feature`, `__buttons`). Title is `<h2>`. CSS is Faisal's.

### `media_content_5050` → `media_card_5050` (earlier rename — historical)
- The captioned-media-card section was renamed to `media_card_5050` to free the `media_content_5050` name (now used by the section above).
- Renamed: template file, ACF layout key/name/label/field keys, BEM classes in template, **and `.media-card-5050__*` selectors in `faisal.css`** (39 selectors). Verified zero `media-content-5050` references remain.
- ⚠️ If the old layout was already added to a page in WP Admin, re-add it as "Media Card 50/50" after syncing.

### `media_card_5050` video options
- Same renderer-backed video option fields + the `controls`-only-for-autoplay fix were wired in here too.

### Video controls fix
- Bug: `Controls` defaulted to Yes and its value leaked to hover/popup behaviors, producing native `<video controls>`. Fix (template-level, both `broker_callout` and `media_card_5050`): `'controls' => 'autoplay' === $video_behavior && ! empty($video['video_controls'])`. Renderer untouched.

### Header — two CTA buttons
- `header.php` now renders a `.header-cta-group` with **secondary (outline) first, primary (solid) second** — matches Figma ("Become A Broker" outline + "Quick Quote" solid).
- `lsc_render_header_button()` / `lsc_get_header_button()` take a `field` arg (default `header_button`).
- New Site Settings field `header_button_secondary` (Link). Existing `header_button` relabelled "Primary (Solid)".
- No header CSS added — `.header-cta-group` / `.header-cta-btn--*` are markup hooks for Faisal.

### Finance grid — center last-row orphans + responsive
- Opt-in `.card-grid--center-last-row` modifier in `lsc-group-design-style.css`: flex-wrap + `justify-content: center`, widths derived from `--lsc-card-grid-gap` so they always match the actual gap.
- `faisal.css` `.finance-products-grid` now sets `--lsc-card-grid-gap: 1.5rem` (was `gap: 1.5rem`) so gap and width math stay in sync. (Root cause of the earlier "looks like 2 columns" bug was a 1.25 vs 1.5rem gap mismatch.)
- Base grid now collapses **columns-3/4/5 → 2 columns at ≤991px**, then 1 at ≤767px.

## Reusable patterns (unchanged)

- **Highlightable title:** `title_lines → line_parts (text + highlight)`; highlighted parts get `color-lsc-accent`.
- **Buttons:** `lsc_render_button()` with `show_icon => false` for section buttons; styles `btn-primary` / `btn-outline` / `btn-secondary`.
- **Grid:** `.card-grid.columns-N`; never redefine columns in section CSS.
- Documented in `.ai/ACF-PATTERNS.md` and `.ai/THEME-ARCHITECTURE.md`.

## Git / Branch Workflow

- Remote: `https://github.com/mosharafmanu/lsc-group.git`
- Branches: `main` (integration), `faisal` (designer/CSS), `imran`. All aligned at `c395cb3` on GitHub.
- Always `git fetch` before merging Faisal's work (he pushes CSS frequently).

## Recommended next steps (resume here)

1. **Commit session 2's work** (the inner-page hero system) — probably on a working branch; `main` is integration.
2. **Sync ACF** in WP Admin: *Page Builder* changed (Page Hero: Hero Style, tabs, video, Show Product Key Facts Bar) **and** a new *Finance Product Details* group to sync.
3. **Faisal: CSS for the new hero styles.** The dark `.inner-hero--image` already has his CSS. Add `.inner-hero--text` (light, text-only), `.inner-hero--split` + `.inner-hero--image-left|right` (light 50/50), and the product key-facts bar (`.inner-hero--has-facts` → `.inner-hero__facts*` tucking over the section below).
4. **Build the example pages** with the one "Page Hero" layout: Brokers (Text Only), Testimonials (Text + Image), Product detail (Image Background + Show Product Key Facts Bar, facts filled on the Finance Product).
5. **DEFERRED — contact page:** build the **contact overlap section** (`contact_panel`) per the "DEFERRED: Contact overlap section" spec above, when we reach the contact page.
6. **Fill Global CTA** in Site Settings (heading "START YOUR FINANCE APPLICATION" + "Apply Now", Dark bg).
7. **Set the two header buttons** in Site Settings (Primary = "Quick Quote", Secondary/Outline = "Become A Broker").
8. Add Finance Product posts (title, featured image, excerpt, Key Facts, page order) so the finance grid + product heroes populate.
