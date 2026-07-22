# Design QA Report

## Source visual truth

- `/var/folders/mv/8jg0c4px65d1pnpzczwqqfn40000gp/T/TemporaryItems/NSIRD_screencaptureui_1meYEF/スクリーンショット 2026-07-23 2.31.51.png`
- Source pixels: 794 × 620
- Source state: employee-list card before refinement, showing a heavily cropped portrait and image-based affiliation bubble.

## Implementation

- URL: `https://mikishokuhin.wiz-services.com/recruit/voices/`
- Browser-rendered implementation screenshot: unavailable; the in-app browser's screenshot command timed out.
- Desktop/browser-panel viewport checked: 723 × 755 CSS px
- Mobile viewport checked: 375 × 812 CSS px
- Density normalization: unavailable because implementation pixels could not be captured.

## Full-view comparison evidence

Blocked. The supplied source image was opened, but a rendered implementation screenshot could not be captured and therefore could not be placed beside the source in one visual comparison input.

## Focused region comparison evidence

Blocked for the same reason. DOM geometry, computed styles, intrinsic image dimensions, content fit, and responsive overflow were inspected in the browser instead.

## Findings and fixes

- [P1] Desktop portrait cropped to the face only.
  - Before: 327.5 × 245.625 px visual with `object-fit: cover`; only about 300 of the source image's 514 vertical pixels were visible.
  - Fix: changed the visual to a 327.5 × 327.5 px square and used `object-fit: contain`, preserving the full 400 × 514 employee portrait inside the compact square.
- [P1] Affiliation bubble competed with the portrait.
  - Before: 112 × 81.25 px raster bubble overlaid on the employee's face/shoulder area.
  - Fix: removed the bubble image from the list card and rendered editable CFS department text in the card body.
- [P1] Mobile left thumbnail was too tall.
  - Before: 210 px high.
  - Fix: reduced to 160 px, kept the card at 160 px, and verified all six cards' names, department text, catch copy, and links stay inside the card.

## Required fidelity surfaces

- Fonts and typography: existing site families, weights, colors, and hierarchy retained; mobile text was tightened only as needed to fit the 160 px card.
- Spacing and layout rhythm: desktop visual is square; mobile uses 34% thumbnail and 66% text with 14 px body padding.
- Colors and visual tokens: existing green, plum, gold, white, and text tokens retained. No new decorative color treatment was introduced.
- Image quality and asset fidelity: original employee photos are reused. Desktop shows the complete portrait without enlargement crop; mobile uses a near-native portrait ratio with only slight horizontal cropping.
- Copy and content: affiliation text for all six employees was migrated from the old bubble assets into editable CFS profile rows and is rendered as text.

## Functional checks

- Six employee cards rendered.
- Affiliation bubble count: 0.
- Department text present on all six cards.
- Mobile card and body height: 160 px for all six cards.
- Mobile horizontal overflow: none.
- Mobile link text remains inside every card.
- PHP error text: none.
- Browser console warnings/errors: none.

## Comparison history

1. The supplied screenshot identified the 4:3 crop and bubble overlay as P1 visual issues.
2. The portrait treatment was changed to a square contained image; the bubble was replaced with CFS text; the mobile thumbnail was reduced from 210 px to 160 px.
3. Post-fix DOM and computed-style checks passed, but post-fix visual evidence could not be captured because screenshot capture timed out.

## Remaining blocker

A same-input source-versus-implementation pixel comparison is still blocked by the browser screenshot timeout. Final visual acceptance must therefore be confirmed from the open test page.

final result: blocked
