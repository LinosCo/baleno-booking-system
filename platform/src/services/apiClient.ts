import axios from 'axios';

export const apiClient = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL || 'http://localhost:4000/api',
  withCredentials: true,
});

export const signIn = (payload: { email: string; password: string }) =>
  apiClient.post('/auth/sign-in', payload);

export const signUp = (payload: { email: string; password: string; acceptUpdates: boolean }) =>
  apiClient.post('/auth/sign-up', payload);

export const requestPasswordReset = (payload: { email: string }) =>
  apiClient.post('/auth/forgot-password', payload);
