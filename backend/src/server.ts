import express from 'express';
import cors from 'cors';
import cookieParser from 'cookie-parser';
import session from 'express-session';
import passport from './middleware/passport.js';
import { connectDatabase } from './config/database.js';
import { env } from './config/env.js';
import { authRouter } from './routes/authRoutes.js';

const app = express();

app.use(
  cors({
    origin: env.clientUrl,
    credentials: true,
  })
);
app.use(express.json());
app.use(cookieParser());
app.use(
  session({
    secret: env.sessionSecret,
    resave: false,
    saveUninitialized: false,
    cookie: { secure: false },
  })
);
app.use(passport.initialize());
app.use(passport.session());

app.get('/api/health', (_req, res) => {
  res.json({ status: 'ok' });
});

app.use('/api/auth', authRouter);

connectDatabase().then(() => {
  app.listen(env.port, () => {
    console.log(`API ready on port ${env.port}`);
  });
});
