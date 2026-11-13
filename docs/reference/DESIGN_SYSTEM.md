# Design system

Hand-built with Tailwind CSS v4. Every brand value lives in **one place**: the `@theme` block at the top of `resources/css/app.css`.

## Tokens
| Token | Default | Use |
|---|---|---|
| `--color-brand` / `brand-dark` / `brand-soft` | #0F3B59 / #0A2A40 (derived) / #EAF0F2 | primary navy, darker navy (footer, topbar, gallery), Light Blue tints |
| `--color-secondary` | #7D929E | decorative lines/icons only (too light for small text) |
| `--color-accent` | #DBA12C | gold: icons/text on navy, hover underlines, CTA fill |
| `--color-accent-text` | #7A5505 (derived) | bronze for small gold text on light backgrounds (AA) |
| `--color-cta` / `cta-dark` | #DBA12C / #C48C1A | conversion buttons: gold with NAVY text (white on this gold fails contrast) |
| `--color-quiz` | #DBA12C | quiz card border and focus ring |
| `--color-ivory` / `soft-white` / `sand` | #F8F7F3 / #FFFEFA / #DBD4CC | page background, header surface, alternate section background |
| `--color-ink` / `muted` / `line` | #0F3B59 / #3E251E / #E5E1DA | text and headings, paragraph (Alt Text) colour, borders |
| `--font-display` | Cormorant Garamond 600/700 (+ italics) | headings (bold by default) |
| `--font-sans` | DM Sans (variable) | body |
| `--radius-card`, `--shadow-card` | 1rem, soft shadow | cards |

Tailwind generates utilities from them: `bg-brand`, `text-muted`, `font-display`, `border-quiz`.

## Components (`resources/views/components/`)
- `<x-button variant="primary|secondary|outline|light" href="...">`
- `<x-section-heading eyebrow title subtitle :center :light tag>`
- `<x-card>` surface with border/shadow/radius
- `<x-form-field name label type="text|email|tel|select|textarea" required :id :active>` (labels, inline errors, ARIA)
- `<x-faq-item question>` native `<details>` accordion
- `<x-icon name>` inline SVG icon set (add paths in the file; quiz icon names come from here)
- `<x-enquiry-form form-name ...>` the shared form; `<x-enquiry-modal>`; `<x-hero-quiz :steps>`; `<x-package-card :package>`

## Re-skinning for a new brand
1. Edit the colour and font tokens in `app.css`. Keep AA contrast: white on `cta`, `accent` on `brand-dark`, `accent-text` on ivory.
2. Fonts: `npm install @fontsource-variable/<font>`, change the two `@import` lines and the `--font-*` values, and update the two preload `<link>` paths in `layouts/app.blade.php`.
3. Admin > Settings: brand name, legal company name, logo, favicon, footer text, contact details. Seeded copy (hero text, tour types, FAQs) lives in `database/seeders/`.
4. Change the destination wording in `QuizSeeder` / admin Quiz, the tagline and hero text, and the default meta description.
5. `npm run build`.

## Layout rules
Mobile first. Container `container-page` (max 7xl, 16px gutters). Sections use `section` (py-16/20). No horizontal scroll at 360, 768 or 1280 px (checked). Respect `prefers-reduced-motion`. Visible focus outline uses the quiz orange.
