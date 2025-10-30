import { useState } from 'react';
import Link from 'next/link';
import { AuthLayout } from '@/components/layout/AuthLayout';
import { Button } from '@/components/ui/button';
import { signIn } from '@/services/apiClient';
import { useToast } from '@/components/ui/use-toast';

const SignInPage = () => {
  const { pushToast } = useToast();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [isLoading, setIsLoading] = useState(false);

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsLoading(true);
    try {
      await signIn({ email, password });
      pushToast({ title: 'Signed in successfully', variant: 'success' });
    } catch (error) {
      pushToast({
        title: 'Unable to sign in',
        description: 'Check your credentials and try again.',
        variant: 'error',
      });
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <AuthLayout
      title="Welcome back to Baleno Academy"
      subtitle="Access your corporate learning environment"
    >
      <form onSubmit={handleSubmit} className="space-y-6" aria-label="Sign in form">
        <div className="space-y-4">
          <label className="block text-left text-sm font-semibold text-brand-600">
            Email
            <input
              type="email"
              value={email}
              onChange={(event) => setEmail(event.target.value)}
              required
              className="mt-1 w-full rounded-xl border border-brand-200 bg-white px-4 py-3 text-sm shadow-inner focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
            />
          </label>
          <label className="block text-left text-sm font-semibold text-brand-600">
            Password
            <input
              type="password"
              value={password}
              onChange={(event) => setPassword(event.target.value)}
              required
              className="mt-1 w-full rounded-xl border border-brand-200 bg-white px-4 py-3 text-sm shadow-inner focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
            />
          </label>
        </div>
        <div className="flex items-center justify-between text-sm">
          <Link href="/auth/forgot-password" className="font-semibold text-brand-500 hover:underline">
            Forgot password?
          </Link>
        </div>
        <Button type="submit" className="w-full" disabled={isLoading}>
          {isLoading ? 'Signing in…' : 'Sign in'}
        </Button>
        <Button
          type="button"
          variant="secondary"
          className="w-full"
          onClick={() =>
            pushToast({ title: 'Google OAuth', description: 'Redirecting to Google OAuth flow…', variant: 'success' })
          }
        >
          Sign in with Google
        </Button>
      </form>
      <p className="text-center text-sm text-slate-500">
        New to Baleno Academy?{' '}
        <Link href="/auth/sign-up" className="font-semibold text-brand-500 hover:underline">
          Create an account
        </Link>
      </p>
    </AuthLayout>
  );
};

export default SignInPage;
