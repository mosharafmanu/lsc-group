# LSC Group Theme Handoff

_Last updated: 2026-06-24._

## Current State

The theme is being built from the LSC Capital design (homepage + inner pages).

- Visual system: warm cream/stone backgrounds, near-black text, orange accent `#ff8a3b`, Instrument Sans, uppercase compact headings, rounded cards.
- Section work is **code-first**: PHP templates + ACF JSON only. **CSS is handled separately** (see "Who owns the CSS" below).
- Local WordPress: `http://localhost/ClientProjects/WordPress/2026/lsc/`.

## ⚠️ Uncommitted work — read first

**Everything from the 2026-06-24 session is uncommitted on `main` (still at `c395cb3`).** Nothing has been pushed. Before resuming, decide whether to commit this batch (likely on a working branch, not straight to `main`). Working-tree changes:

- New section templates: `broker_callout.php`, `inner_hero.php`, `content_card_5050.php`, `timeline_section.php`, `cta_section.php`
- Renamed: `media_content_5050.php` → `media_card_5050.php`
- Modified: `header.php`, `inc/helper-functions/site-settings.php`, `template-parts/sections/finance_products_grid.php`, `acf-json/group_flexible_content.json`, `acf-json/group_site_settings.json`, `assets/css/lsc-group-design-style.css`, `faisal.css`
- `style.css` also shows modified (not changed by this session — verify before committing).

After any ACF JSON change: **WP Admin → Custom Fields → Sync**.

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
| 1 | `inner_hero` | `inner_hero.php` | **NEW** — inner-page hero, image only (no video), no overlay div |
| 2 | `finance_products_grid` | `finance_products_grid.php` | Centers last-row orphans (see below) |
| 3 | `broker_callout` | `broker_callout.php` | **NEW** — orange callout card |
| 4 | `stats_section` | `stats_section.php` | |
| 5 | `media_card_5050` | `media_card_5050.php` | **RENAMED** from `media_content_5050` |
| 6 | `testimonials_section` | `testimonials_section.php` | Slick carousel |
| 7 | `contact_section` | `contact_section.php` | |
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

### `media_content_5050` → `media_card_5050` (full rename)
- The new #8/#9 alternating-50/50-with-list design will become `media_content_5050` (not yet built). The captioned-media-card section was renamed to `media_card_5050` to free the name.
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

1. **Commit the session's work** (probably on a working branch; `main` is integration).
2. **Sync ACF** in WP Admin (flexible content + site settings groups changed).
3. **Fill Global CTA** in Site Settings (heading "START YOUR FINANCE APPLICATION" + "Apply Now", Dark bg).
4. **Set the two header buttons** in Site Settings (Primary = "Quick Quote", Secondary/Outline = "Become A Broker").
5. **Build the real `media_content_5050`** (#8/#9): alternating 50/50, image one side + heading/text/list the other, with a media-position toggle.
6. Faisal: style the new sections' BEM classes, and rename `.media-card-5050` selectors are already done — confirm on his branch.
7. Add Finance Product posts (title, featured image, excerpt, page order) so the finance grid populates.
