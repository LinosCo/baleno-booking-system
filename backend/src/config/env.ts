import 'dotenv/config';

export const env = {
  port: Number(process.env.PORT || 4000),
  mongoUri: process.env.MONGO_URI || 'mongodb://localhost:27017/sphere',
  jwtSecret: process.env.JWT_SECRET || 'dev-secret-key',
  sessionSecret: process.env.SESSION_SECRET || 'session-secret',
  googleClientId: process.env.GOOGLE_CLIENT_ID || 'GOOGLE_CLIENT_ID',
  googleClientSecret: process.env.GOOGLE_CLIENT_SECRET || 'GOOGLE_CLIENT_SECRET',
  clientUrl: process.env.CLIENT_URL || 'http://localhost:3000',
};
