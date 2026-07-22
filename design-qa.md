# Design QA Report

## Source of truth

- Detail spacing reference: `/var/folders/mv/8jg0c4px65d1pnpzczwqqfn40000gp/T/TemporaryItems/NSIRD_screencaptureui_bfQ3lX/スクリーンショット 2026-07-23 2.10.27.png` (2656 × 1142)
- Hero spacing reference: `/var/folders/mv/8jg0c4px65d1pnpzczwqqfn40000gp/T/TemporaryItems/NSIRD_screencaptureui_3Yd0In/スクリーンショット 2026-07-23 2.10.40.png` (1678 × 1322)
- Implementation:
  - `https://mikishokuhin.wiz-services.com/recruit/kitamura/`
  - `https://mikishokuhin.wiz-services.com/recruit/voices/`

## Test conditions

- Desktop viewport: 1678 × 1000
- Mobile viewport: 375 × 812
- States checked: employee detail, employee list, desktop, mobile
- Rendered implementation screenshot: unavailable because the in-app browser screenshot API timed out repeatedly on both the implementation pages and a blank page.

## Comparison status

- Full-page visual comparison: blocked (rendered implementation screenshot unavailable)
- Focused comparison of the hero, Message section, and mobile employee card: blocked (rendered implementation screenshot unavailable)
- Browser DOM and computed-style inspection: completed

## Quantitative checks

### Employee detail, desktop

- Hero title: 174 px high, line-height 58 px
- Hero lead: 86 px high, line-height 28.8 px
- Message title: 65 px high, line-height 32.48 px
- Message body: 86 px high, line-height 28.8 px
- Message section: 407 px high
- Repeated `<br>` elements in the hero and Message CFS text were removed.

### Employee list, desktop

- First card: 351 × 535 px
- Visual area: 351 × 263 px (4:3)
- Portrait: 351 × 263 px, `object-position: center top`
- Affiliation bubble: 112 × 81 px

### Employee list, mobile

- First card: 335 × 210 px
- Layout: 127 px left thumbnail / 208 px right content
- Portrait: 127 × 210 px, aligned to the top
- Affiliation bubble: 84 × 61 px
- Horizontal overflow: none

## Findings and fixes

- P1: CFS rich text contained repeated line breaks that doubled the visual spacing. Fixed by normalizing `<br>` elements and repeated newlines before output.
- P1: Detail hero and Message typography used excessive line-height and section padding. Fixed with tighter line-height, margins, and vertical padding.
- P1: Employee card visual area was too tall. Fixed with a desktop 4:3 visual ratio.
- P1: Mobile employee cards did not prioritize the portrait. Fixed with a left-thumbnail/right-content layout and a constrained affiliation bubble.
- No PHP error text, broken card images, console errors, or horizontal overflow were found in browser inspection.

## Remaining blocker

The browser remained interactive for DOM inspection, but screenshot capture failed consistently. A final pixel-level comparison against the supplied references could therefore not be completed in this run.

final result: blocked
