# Design QA: 社員の声一覧・社員の声詳細

- Source visual truth: `/var/folders/mv/8jg0c4px65d1pnpzczwqqfn40000gp/T/TemporaryItems/NSIRD_screencaptureui_ET0UNv/スクリーンショット 2026-07-23 1.07.30.png`
- Implementation URL: `https://mikishokuhin.wiz-services.com/recruit/voices/?review=qa-final-20260723`
- Implementation screenshot: `design-qa-assets/implementation-voices-card-view.png`
- Source pixels: 662 × 600 px (user-supplied cropped mobile issue capture; device density metadata unavailable)
- Implementation pixels / CSS viewport: 723 × 755 px at 1× capture density
- Density normalization: the source card is approximately 656 physical pixels wide and is treated as a roughly 328 CSS-pixel card at 2×; the implementation card is 328 CSS pixels wide at 1×. Comparison therefore uses the card width as the normalization anchor rather than the surrounding browser frame.
- State: published employee list, first employee card and first row; logged-in test environment. Responsive behavior was additionally measured at 375 × 812 CSS pixels.

## Full-view comparison evidence

The source issue capture shows the affiliation bubble inside the card body, creating a large empty band before the name. In the post-fix implementation, the affiliation graphic is overlaid on the lower-right of the existing portrait, the full portrait keeps its original 400 × 514 ratio, and the body becomes a compact text block. The page-title section border is removed.

At 375 px CSS width, the list resolves to one 335 px column with no horizontal overflow. The portrait is 335 × 430 px, the affiliation graphic is 127 × 92 px, and the catch-copy line height is 19.53 px.

## Focused region comparison evidence

Focused comparison was required because the source is a cropped card-body screenshot rather than a complete page. The first-card measurements after the fix at the normalized 328 px card width are:

- portrait: 328 × 421 px, intrinsic ratio preserved, top visible;
- affiliation graphic: 132 × 96 px, overlaid on the portrait and no longer participating in body layout;
- body: 328 × 248 px;
- page-title underline: 0 px.

The implementation uses the existing site serif/sans typography, green/plum/gold tokens, original employee and affiliation assets, and the source copy without replacement assets.

## Required fidelity surfaces

- Fonts and typography: existing site font families and weights retained; catch-copy line height reduced to 1.55 to remove the loose rhythm in the issue capture.
- Spacing and layout rhythm: bubble removed from document flow, fixed body minimum height removed, and link placement handled by flex layout. No horizontal overflow at 375 px.
- Colors and visual tokens: existing green, plum, gold, ivory, and line tokens retained; page-title underline removed as requested.
- Image quality and asset fidelity: original 400 × 514 employee images and original 255 × 185 affiliation graphics are used. No CSS or generated replacement assets were introduced.
- Copy and content: all six published employee-detail links and their existing names/catch copy are preserved.

## Comparison history

1. Initial findings:
   - P2: affiliation graphic occupied a 70 px body row and created excessive whitespace.
   - P2: fixed-height thumbnail treatment reduced useful portrait visibility.
   - P2: catch-copy vertical rhythm was too loose in the cropped mobile card.
2. Fixes:
   - moved the affiliation graphic onto the portrait and enlarged it for legibility;
   - removed fixed thumbnail height and absolute crop, preserving natural image ratio with top-aligned content;
   - removed the card-body minimum height and tightened catch-copy line height;
   - removed the page-title section underline.
3. Post-fix evidence:
   - affiliation graphic is 132 × 96 px at the normalized card width and does not add body whitespace;
   - portrait is full-ratio and the face is visible;
   - body is 248 px high at tablet card width and 233 px at 375 px mobile width;
   - no broken images, PHP error text, browser console errors, or mobile horizontal overflow were found.

## Functional QA

- Six published recruit child pages render; the `voices` page itself is excluded.
- Detail links match `kitamura`, `adachi`, `matsushita_m`, `matsushita_k`, `maeda_a`, and `ofiji`.
- Pagination is configured at twelve items per page and remains hidden until a thirteenth published child page is added.
- The Kitamura detail FV loads the same `kitamura.png` used by the list.
- The detail editor contains 17 CFS loop headers; zero headers and zero bodies are open on initial load.
- Browser console errors: none.

## Findings

No actionable P0, P1, or P2 findings remain.

## Follow-up polish

None required for this scope.

final result: passed
