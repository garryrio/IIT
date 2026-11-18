# Presentation Outline — Marga Mulya Hub (Final Stage, max 7 slides)

Format: 5 minutes presentation & demo + 10 minutes Q&A, delivered in English.
Use the **official IIT Challenge logo** from the committee (link.accelist.com/PresentationLogo) — do not recreate it.

---

## Slide 1 — Introduction
- Title: **Marga Mulya Hub** — *Village information, local potential and community participation in one platform.*
- Team name & members, category: EcoQuest AI Web Application.
- One-line objective: make Desa Marga Mulya's information easy to find, easy to understand and easy to update.
- Visual: homepage hero screenshot (desktop + phone mock-up).

## Slide 2 — Village Overview
- Identity: Desa Marga Mulya (location to be confirmed with the committee), RW/RT, area.
- Geography & land use (rice fields dominate), community and livelihoods (farmers, private employees, craftsmen).
- Potential: rice farming, home-based food products, bamboo crafts, culinary stalls, village park, sewing training, waste bank.
- Note on stage: figures shown are sample data following the official EcoQuest data structure.

## Slide 3 — Problem Identification
1. Information is scattered (notice boards, chat groups) and hard to find.
2. Village data is published as raw tables that are hard to understand.
3. Local potential lacks attractive, searchable promotion.
4. Updating a website depends on a developer.
5. Community activities are not documented systematically.
6. Residents have no structured channel to contribute updates.
7. Social assistance targeting lacks a simple, transparent data tool.

## Slide 4 — Proposed Website Solution
- Concept: **Website + CMS + Village Data Hub + Potential Catalogue + Community Platform**.
- Target users: residents, village officials, MSME owners, visitors.
- How it solves each problem (map problems 1–7 to features).
- Tech approach (compliant with rules): Laravel (PHP 8.3+), MySQL, Tailwind, Alpine.js, framer-motion, Leaflet/OpenStreetMap, Chart.js, deployed on Vercel.

## Slide 5 — Website Features and Demonstration
- 9 public pages (Home, Profile, Data Hub, Potentials + detail, Information + detail, Gallery, Contact).
- Innovations: Village Data Hub, layered interactive map, smart search (Ctrl+K), event calendar with .ics/Google Calendar, Citizen-to-Village moderation, admin-only income-class analysis for aid prioritisation.
- Accessibility: mobile-first, keyboard friendly, chart text/table alternatives, reduced-motion support.
- **Live demo (60–90 seconds)** — see script below.

## Slide 6 — Impact and Implementation Feasibility
- Visitors: one place for trustworthy information, maps and contacts.
- Residents: active announcements, calendar reminders, a safe channel to contribute.
- MSMEs: free digital storefront with WhatsApp contact and map location.
- Government: no-code content updates, data-driven aid targeting, transparent governance.
- Feasibility: free/open-source stack, low hosting cost, images stored in MySQL, simple CMS taught in the 3-day VIP (overview → admin training → handover).
- SDG alignment: SDG 9, SDG 11, SDG 16, SDG 8.

## Slide 7 — Conclusion
- Summary: one platform that keeps village information current, understandable and participatory.
- Value to the village: better access, stronger local economy, fairer assistance, documented community life.
- Expected outcome: officials can independently manage the site after handover.
- Closing line + QR code to the Vercel URL.

---

## Demo script (≈ 80 seconds)

| Time | Action | What to say |
|---|---|---|
| 0:00–0:10 | Open homepage; scroll past hero, stats, active announcements | "Everything you see here comes from the CMS — nothing is hard-coded." |
| 0:10–0:20 | Press **Ctrl + K**, type "pelatihan" | "Smart search groups results by type: announcements, events, news, potentials." |
| 0:20–0:30 | Home map: toggle Potensi / Fasilitas / Agenda layers, click a marker | "A layered map built on OpenStreetMap." |
| 0:30–0:40 | Open **Data Desa**, switch year, expand "Lihat tabel data" | "Charts with text summaries and tables for accessibility." |
| 0:40–0:50 | **Informasi → Kalender agenda**, open an event, show "Tambah ke Google Calendar" | "Residents can save village events to their phones." |
| 0:50–1:05 | **Kontak → Kontribusi warga**: submit a report | "Residents contribute, but nothing is published without moderation." |
| 1:05–1:20 | Log in to **/admin**: approve the contribution → "Jadikan draf berita" → publish; refresh public page | "Officials moderate and publish in a few clicks — and the sender's private data is never shown." |
| (bonus) | **Analisis penghasilan**: priority list & CSV export | "An admin-only tool to target social assistance fairly; the public only sees aggregates." |

Tips: prepare the admin session in a second tab, use a stable connection, and keep a screen recording as a backup.
