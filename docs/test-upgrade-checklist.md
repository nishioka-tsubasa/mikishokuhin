# Test Upgrade Checklist

Use this branch for the test-environment upgrade work. Do not deploy to production until the test result is approved.

## Preflight

- Confirm full file backup.
- Confirm database backup.
- Confirm restore procedure.
- Confirm target PHP version.
- Confirm admin login access.
- Confirm current public QA baseline in `docs/evidence/`.

## Stage 1: PHP

- Upgrade test PHP from 7.2.24 to 8.0+.
- Re-check top, company, business, recruit, contact, notice archive/detail.
- Check PHP error log and visible error markers.

## Stage 2: Highest Risk Plugins

- MW WP Form: 4.2.0 -> 5.1.4.
- Custom Field Suite: replace/remove plan required; do not leave as accepted residual risk.
- All-in-One WP Migration: 7.6 -> 7.106.
- WPForms Lite: 1.9.0.4 -> 1.10.2.1.

## Stage 3: Compatibility Updates

- Advanced Custom Fields: 5.8.7 -> 6.8.5.
- Duplicate Post: 3.2.4 -> 4.7.
- UserFeedback Lite: 1.1.1 -> 1.11.2.
- OptinMonster: 2.16.5 -> 2.16.24.
- Classic Editor: 1.5 -> 1.7.0.
- TinyMCE Advanced: 5.3.0 -> 5.9.2.
- WPFront Scroll Top: 2.0.2 -> 3.0.1.

## Stage 4: Core

- WordPress: 6.9.4 -> 7.0.1 after plugin compatibility checks pass.

## QA

- Top page slider.
- Company page.
- Business pages.
- Product page.
- Recruit list and interview detail pages.
- Notice archive and latest notice detail.
- Contact form input and confirmation, without sending unless explicitly approved.
- Header menu, mobile menu, footer.
- PC and mobile viewport.
- REST API root and pages/posts endpoints.
- PHP error log.

## Rollback

- Restore database backup.
- Restore file backup or changed plugin/theme/core directories.
- Verify top/contact/notice/recruit/admin login.
