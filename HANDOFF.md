# LSC Group Theme Handoff

_Last updated: 2026-06-24 (session 2)._

## Current State

The theme is being built from the LSC Capital design (homepage + inner pages).

- Visual system: warm cream/stone backgrounds, near-black text, orange accent `#ff8a3b`, Instrument Sans, uppercase compact headings, rounded cards.
- Section work is **code-first**: PHP templates + ACF JSON only. **CSS is handled separately** (see "Who owns the CSS" below).
- Local WordPress: `http://localhost/ClientProjects/WordPress/2026/lsc/`.

## ✅ Committed & pushed — resume point

Two 2026-06-24 batches are committed and pushed; **`main`, `faisal`, and `imran` are all aligned** on GitHub (nothing lost). Faisal's CSS commits (`77aa3e2`, `93c428e`, `f5b7a71` — `faisal.css`, `lsc-group-design-style.css`, `video-behaviors.css`) are merged into `main` too.

- **Batch 1 (`24d17d1`):** inner_hero, broker_callout, content_card_5050, timeline_section, cta_section, the `media_content_5050` → `media_card_5050` rename, header two-CTA, finance-grid orphan centering.
- **Batch 2 (this session):** the inner-page hero system below. Files:
  - `inner_hero.php` — single **Page Hero** (Hero Style selector; Content/Media tabs; Text+Media video; product Key Facts bar via `show_facts_bar`).
  - `media_content_5050.php` (NEW) — 50/50 content + checklist beside image/video; **Enable Background** toggle; baked `mt-50 mt-lg-90` top gap.
  - `acf-json/group_flexible_content.json` — Page Hero fields, `media_content_5050` layout, video-group fields at 50% width.
  - `acf-json/group_finance_product.json` (NEW) — product **Key Facts** meta (`product_facts` repeater) on the `finance_product` CPT.
  - `inc/helper-functions/acf-field-visibility.php` (NEW) — `lsc_get_admin_post_type()` + filter that hides the facts-bar toggle off Finance Products.
  - `acf-json/group_site_settings.json` + `inc/helper-functions/site-settings.php` — Opening Hours (staged for the deferred contact panel).
- `stats_section.php` / `contact_section.php` are **untouched** — general-purpose, not the overlap sections.

**After pulling: WP Admin → Custom Fields → Sync** (Page Builder changed + new *Finance Product Details* group).

After any ACF JSON change: **WP Admin → Custom Fields → Sync**.

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
| 4 | `stats_section` | `stats_section.php` | General stats grid (untouched) |
| 5 | `media_card_5050` | `media_card_5050.php` | **RENAMED** from `media_content_5050` |
| 5b | `media_content_5050` | `media_content_5050.php` | **NEW** — 50/50 content + checklist beside image/video; optional bg + Top Spacing |
| 6 | `testimonials_section` | `testimonials_section.php` | Slick carousel |
| 7 | `contact_section` | `contact_section.php` | General contact section (untouched) |
| 8 | `content_card_5050` | `content_card_5050.php` | **NEW** — content + checklist beside a card + image |
| 9 | `timeline_section` | `timeline_section.php` | **NEW** — auto-numbered journey steps |
| 10 | `cta_section` | `cta_section.php` | **NEW** — pulls from Global CTA by default |

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
