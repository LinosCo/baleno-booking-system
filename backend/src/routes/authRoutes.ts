import { Router } from 'express';
import { signIn, signUp, forgotPassword } from '../controllers/authController.js';
import passport from 'passport';

export const authRouter = Router();

authRouter.post('/sign-up', signUp);
authRouter.post('/sign-in', signIn);
authRouter.post('/forgot-password', forgotPassword);

authRouter.get('/google', passport.authenticate('google', { scope: ['profile', 'email'] }));
authRouter.get(
  '/google/callback',
  passport.authenticate('google', { failureRedirect: '/auth/sign-in' }),
  (req, res) => {
    res.redirect(`${process.env.CLIENT_URL ?? 'http://localhost:3000'}/auth/google-callback?token=${req.user?.token ?? ''}`);
  }
);
