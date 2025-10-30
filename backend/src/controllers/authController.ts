import { Request, Response } from 'express';
import bcrypt from 'bcryptjs';
import jwt from 'jsonwebtoken';
import { User } from '../models/User.js';
import { env } from '../config/env.js';

const TOKEN_EXPIRATION = '7d';

export const signUp = async (req: Request, res: Response) => {
  const { email, password, acceptUpdates } = req.body as {
    email: string;
    password: string;
    acceptUpdates: boolean;
  };

  if (!email || !password) {
    return res.status(400).json({ message: 'Email and password are required.' });
  }

  const existingUser = await User.findOne({ email });
  if (existingUser) {
    return res.status(409).json({ message: 'Email already registered.' });
  }

  const hashedPassword = await bcrypt.hash(password, 10);
  const user = await User.create({ email, password: hashedPassword, acceptUpdates });
  return res.status(201).json({ id: user.id, email: user.email });
};

export const signIn = async (req: Request, res: Response) => {
  const { email, password } = req.body as { email: string; password: string };

  if (!email || !password) {
    return res.status(400).json({ message: 'Email and password are required.' });
  }

  const user = await User.findOne({ email });
  if (!user || !user.password) {
    return res.status(401).json({ message: 'Invalid credentials.' });
  }

  const match = await bcrypt.compare(password, user.password);
  if (!match) {
    return res.status(401).json({ message: 'Invalid credentials.' });
  }

  const token = jwt.sign({ sub: user.id, email: user.email }, env.jwtSecret, { expiresIn: TOKEN_EXPIRATION });

  res.cookie('token', token, {
    httpOnly: true,
    secure: process.env.NODE_ENV === 'production',
    sameSite: 'lax',
    maxAge: 7 * 24 * 60 * 60 * 1000,
  });

  return res.json({ id: user.id, email: user.email });
};

export const forgotPassword = async (req: Request, res: Response) => {
  const { email } = req.body as { email: string };
  if (!email) {
    return res.status(400).json({ message: 'Email is required.' });
  }

  const user = await User.findOne({ email });
  if (!user) {
    return res.status(200).json({ message: 'If the email exists, a reset link will be sent.' });
  }

  // In a real implementation we would send an email. For now return placeholder response.
  return res.json({ message: 'Reset instructions sent to email (mock).' });
};
