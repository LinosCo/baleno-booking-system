# Sphere Platform Backend

Express + TypeScript API that provides authentication endpoints and Google OAuth wiring for the Sphere learning community prototype.

## Getting started

```bash
npm install
npm run dev
```

Set the following environment variables (or rely on defaults) in a `.env` file:

- `PORT` (default `4000`)
- `MONGO_URI`
- `JWT_SECRET`
- `SESSION_SECRET`
- `GOOGLE_CLIENT_ID`
- `GOOGLE_CLIENT_SECRET`
- `CLIENT_URL` (default `http://localhost:3000`)

The API exposes:

- `POST /api/auth/sign-up`
- `POST /api/auth/sign-in`
- `POST /api/auth/forgot-password`
- `GET /api/auth/google`
- `GET /api/auth/google/callback`

A mock password reset implementation is provided for prototyping.
