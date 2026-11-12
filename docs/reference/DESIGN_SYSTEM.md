# Design system

Hand-built with Tailwind CSS v4. Every brand value lives in **one place**: the `@theme` block at the top of `resources/css/app.css`.

## Tokens
| Token | Default | Use |
|---|---|---|
| `--color-brand` / `brand-dark` / `brand-soft` | #1f3d34 / #142a23 / #e7efe9 | header, buttons, dark sections, tints |
| `--color-accent` | #d4b36d | gold for text/icons on dark green (AA) |
| `--color-accent-text` | #7a5f2a | gold text on light backgrounds (AA) |
| `--color-cta` / `cta-dark` | #b85c0a / #94490a | primary conversion buttons (AA with white text) |
| `--color-quiz` | #f08a24 | quiz card border and focus ring |
| `--color-ivory` / `sand` | #faf7f0 / #f1ebdd | page and alternate section backgrounds |
| `--color-ink` / `muted` / `line` | #1c2421 / #55605b / #ddd6c5 | text, secondary text, borders |
| `--font-display` | Cormorant (variable) | headings |
| `--font-sans` | Inter (variable) | body |
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
