# Sphere Platform Frontend

This Next.js application replicates the core experience of the Talent Garden × Hyper Island "Sphere" platform. It uses TypeScript, Tailwind CSS, shadcn-inspired UI primitives, and Framer Motion for delightful interactions.

## Getting started

```bash
npm install
npm run dev
```

The app expects the backend API to be reachable at `NEXT_PUBLIC_API_URL` (defaults to `http://localhost:4000/api`).

## Features

- Responsive dashboard layout with persistent sidebar and header
- Authentication flows (sign-in, sign-up, forgot password) with password strength indicators
- Community, Courses, Events, Resources, Deals, Job Board, Settings, and Support sections
- Accessible design with keyboard focus states, ARIA labels, and WCAG-friendly color contrast
- Toast notifications and reusable UI primitives (buttons, tabs, cards, modals, carousels)
