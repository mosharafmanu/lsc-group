# LSC Group Theme Handoff

## Current State

The theme is being built from the LSC Capital homepage design direction:

- Visual system: warm cream/stone backgrounds, near-black text, orange accent `#ff8a3b`, Instrument Sans typography, uppercase compact headings, rounded product/card styling.
- Section implementation is code-first: PHP templates and ACF JSON only. CSS is handled separately when requested.
- Local WordPress is currently reachable at `http://localhost/ClientProjects/WordPress/2026/lsc/` and returns `200`.

## CSS State

Section styling lives in `faisal.css` (authored on the `faisal` branch by the front-end designer). Styled so far:

- Header
- Hero
- Finance products grid
- Stats
- Media Content 50/50
- Footer

Not yet styled: `testimonials-section` (markup and `quote-watermark` are in place; CSS pending).

## Git / Branch Workflow

- Remote: `https://github.com/mosharafmanu/lsc-group.git`
- Branches: `main` (integration), `faisal` (designer/front-end), `imran`.
- `main` is the integration branch; `faisal` and `imran` are kept in sync with it after each merge.
- Faisal pushes design/CSS work to `origin/faisal`. Before merging, always `git fetch` first — he pushes frequently and merging without fetching can reject or miss his latest.
- All branches are currently aligned on GitHub (same commit, identical files).

## Implemented Content Architecture

### Custom Post Types

- `finance_product`
  - Registered in `inc/post-types.php`
  - Loaded from `functions.php`
  - Archive disabled: `has_archive => false`
  - Supports: `title`, `editor`, `thumbnail`, `excerpt`, `page-attributes`
  - Intended card data:
    - Title = card title
    - Featured Image = card image
    - Excerpt = card description
    - Permalink = Learn More link

### Flexible Content Sections

All section templates live in `template-parts/sections/` and are loaded by `lsc_flexible_content('cms')`.

- `hero_section.php`
  - Image/video hero.
  - Uses nested `title_lines -> line_parts` for highlighted text without editor HTML.
  - Supports buttons repeater and feature chips with uploaded SVG/image icons rendered via `lsc_render_icon()`.

- `finance_products_grid.php`
  - Queries `finance_product` posts.
  - Product source: all or selected products.
  - Supports grid column selection via existing `card-grid columns-*` classes.
  - Uses native title, featured image, excerpt, permalink.

- `stats_section.php`
  - Simple metrics band.
  - Repeater fields: value and label.
  - Grid is fixed to `columns-3`.

- `media_content_5050.php`
  - Reusable 50/50 content and media section.
  - Media position: left or right.
  - Media type: image or video.
  - Uses nested `title_lines -> line_parts`.
  - Buttons repeater rendered with `lsc_render_button()`.
  - Image media uses `<figure>` and `<figcaption>`.
  - Media label/caption fields only apply when media type is image.
  - The `__inner` wrapper also outputs a short-form `media-left` / `media-right` class (from `media_position`) for CSS layout ordering, alongside the BEM `media-content-5050--media-*` modifier on the section.

- `testimonials_section.php`
  - Header (eyebrow, `title_lines -> line_parts`, description) plus a 3-column card grid.
  - Each `testimonial-card` renders: star rating (`assets/svgs/star`), inline quote icon (`assets/svgs/quote`), quote text, and author block (initial, name, role).
  - A decorative background quote mark is rendered as the card's first child via `testimonial-card__quote-watermark` (`assets/svgs/quote-watermark`, `aria-hidden`). Positioning is left to CSS.

## ACF Notes

- Main field group: `acf-json/group_flexible_content.json`
- Site settings group: `acf-json/group_site_settings.json`
- Removed the starter `example_section` layout and deleted `template-parts/sections/example_section.php`.
- Removed the redundant `Finance Product Fields` ACF group. If it appears again in WP Admin, delete it from the database; local JSON has been removed.
- ACF sync timestamps have been adjusted several times. If “Sync available” remains after syncing, compare local JSON `modified` timestamps against DB `post_modified` values.

## Reusable Title Highlight Pattern

For any heading where text may be highlighted, use:

```text
title_lines
└── line_parts
    ├── text
    └── highlight
```

This supports both full-line highlights and inline highlighted words without requiring editors to enter HTML.

Example:

```text
Line 1:
- WE ARE READY,

Line 2:
- WILLING AND [highlight on]

Line 3:
- ABLE TO LEND.
```

Documented in:

- `.ai/ACF-PATTERNS.md`
- `.ai/THEME-ARCHITECTURE.md`

## Header / Footer State

- Header markup includes logo, main nav, phone, CTA, and mobile toggle.
- The submenu indicator is rendered from `assets/svgs/submenu-indicator.php`; the SVG uses `currentColor`.
- Header CSS previously added during prototyping was removed at the user’s request.
- Footer markup has been updated to match the design structure:
  - brand/logo/tagline/contact action icons
  - footer menu column
  - contact column
  - company registration row
  - copyright and website credit

## Verification Completed

- PHP lint passed for newly edited templates and includes.
- ACF JSON parses correctly after each layout change.
- WP-CLI confirmed `finance_product`:
  - `supports_editor=yes`
  - `supports_excerpt=yes`
  - `has_archive=false`
- Local homepage request returns `200`.

## Recommended Next Steps

1. Sync ACF field groups in WP Admin.
2. Add Finance Product posts with title, featured image, excerpt, and page order.
3. Build CSS for the implemented sections when ready:
   - `hero-section`
   - `finance-products-section`
   - `stats-section`
   - `media-content-5050`
   - `testimonials-section`
4. Add remaining homepage sections from the design as dedicated flexible content templates.
5. After all CPT/layout changes are stable, visit Settings > Permalinks and save once.
