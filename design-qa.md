# Employee Interview Design QA

**Source visual truth**

- Adobe XD export: `/Users/n.tsubasa/Desktop/web案件/保守企業/ミ_三基食品/デザイン/社員の1日/三基食品社員の1日/ページイメージ_20260518.png`
- Source pixels: `1920 × 8132` at 1× artboard density.

**Rendered implementation**

- Deployed route: `https://mikishokuhin.wiz-services.com/recruit/kitamura/`
- Full-view comparison: `docs/evidence/employee-interview-design-comparison.png`
- Desktop captures: `docs/evidence/employee-interview-desktop-hero.png`, `docs/evidence/employee-interview-desktop-q2.png`, `docs/evidence/employee-interview-desktop-schedule.png`
- Mobile captures: `docs/evidence/employee-interview-mobile.png`, `docs/evidence/employee-interview-mobile-schedule.png`, `docs/evidence/employee-interview-mobile-cta.png`
- Desktop viewport and screenshot pixels: `1920 × 1080`, 1× density. The deployed page measured `1920 × 7100` CSS px.
- Mobile viewport and screenshot pixels: `390 × 844`, 1× density.
- Full-view normalization: source and deployed page were both rendered at a `1920` px CSS width and shown at `12.5%` scale in one `800 × 1080` comparison capture.
- State: unauthenticated, light theme, employee page `kitamura`, default page state.

**Findings**

- No actionable P0, P1, or P2 differences remain.
- [P3] The deployed document is slightly more compact vertically than the static XD artboard. The WordPress implementation keeps the site's current production header/footer and uses live responsive text flow, while preserving the source section order, hierarchy, alternating interview rhythm, timeline structure, message, and CTA treatment.

**Required fidelity surfaces**

- Fonts and typography: Cormorant Garamond is used for the source's editorial English headings and time labels; the existing Japanese site typeface and weights preserve the source hierarchy and wrapping.
- Spacing and layout rhythm: hero/profile split, alternating interview grid, centered timeline, message, and CTA align with the reference. Desktop has no horizontal overflow; mobile measured `scrollWidth = 390` at a `390` px viewport.
- Colors and tokens: green `#2C5E3F`, ivory `#FAF6EC`, plum `#6E3957`, and gold `#C9A961` map to the source palette, with matching paper texture, borders, and restrained shadows.
- Image quality and asset fidelity: the existing CMS employee and workplace photographs are used directly. No placeholder, emoji, custom SVG, CSS illustration, or generated substitute is used for visible source imagery.
- Copy and content: the five existing interview entries are preserved. The new hero, profile, six-item daily schedule, message, and CTA copy match the supplied design content and remain editable through Secure Custom Fields.
- Accessibility and responsiveness: semantic headings, articles, time elements, alt text, escaped output, mobile stacking, readable line lengths, and practical CTA tap targets were checked.

**Focused evidence**

- Hero/profile fidelity: `docs/evidence/employee-interview-desktop-hero.png`
- Alternating image/text grid and Q marker: `docs/evidence/employee-interview-desktop-q2.png`
- Timeline cards, time rail, accent dots, notes, and spacing: `docs/evidence/employee-interview-desktop-schedule.png`
- Mobile hero, timeline, and CTA/footer: the three mobile captures listed above.

**Comparison history**

1. Initial local comparison found a P2 grid-ratio mismatch in right-text interview rows: Q2/Q5 imagery occupied too much width and compressed the copy.
2. Fixed `.miki-interview-question.is-text_r .miki-interview-question__content` to use image/text tracks of `.78fr / 1.22fr` on desktop and `.75fr / 1.25fr` at the intermediate breakpoint.
3. Re-captured Q2 and the full deployed page. Post-fix evidence is `docs/evidence/employee-interview-desktop-q2.png` and `docs/evidence/employee-interview-design-comparison.png`; no P0/P1/P2 mismatch remains.

**Primary interactions and runtime checks**

- The primary CTA navigated to `https://mikishokuhin.wiz-services.com/recruit/#slick`.
- The secondary CTA resolves to `https://mikishokuhin.wiz-services.com/recruit/`.
- All six employee routes returned HTTP 200, complete HTML, and no visible PHP Fatal/Warning/Deprecated/Notice output.
- Browser console errors: none. One existing third-party Adobe Launch deprecation warning was observed and is unrelated to this implementation.

**Implementation checklist**

- [x] Match desktop source composition and editorial styling.
- [x] Preserve existing interview data and CMS images.
- [x] Add editable SCF fields and repeaters for extensible sections.
- [x] Verify desktop and mobile layouts without horizontal overflow.
- [x] Verify deployed files, employee routes, CTAs, and browser console.

final result: passed
