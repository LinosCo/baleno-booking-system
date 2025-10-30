import { useState } from 'react';
import Link from 'next/link';
import { AuthLayout } from '@/components/layout/AuthLayout';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { PasswordStrength } from '@/components/PasswordStrength';
import { signUp } from '@/services/apiClient';
import { useToast } from '@/components/ui/use-toast';

const SignUpPage = () => {
  const { pushToast } = useToast();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [acceptTerms, setAcceptTerms] = useState(false);
  const [acceptUpdates, setAcceptUpdates] = useState(false);
  const [isLoading, setIsLoading] = useState(false);

  const isFormValid = email.length > 0 && password.length >= 8 && acceptTerms;

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    if (!isFormValid) return;
    setIsLoading(true);
    try {
      await signUp({ email, password, acceptUpdates });
      pushToast({ title: 'Account created', description: 'Check your inbox to verify your email.', variant: 'success' });
    } catch (error) {
      pushToast({ title: 'Sign up failed', description: 'Something went wrong. Try again later.', variant: 'error' });
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <AuthLayout
      title="Join Baleno Academy"
      subtitle="Empower your teams with a unified learning community"
    >
      <form onSubmit={handleSubmit} className="space-y-6" aria-label="Sign up form">
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
            <div className="relative mt-1">
              <input
                type={showPassword ? 'text' : 'password'}
                value={password}
                onChange={(event) => setPassword(event.target.value)}
                required
                aria-describedby="password-guidelines"
                className="w-full rounded-xl border border-brand-200 bg-white px-4 py-3 text-sm shadow-inner focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
              />
              <button
                type="button"
                onClick={() => setShowPassword((value) => !value)}
                className="absolute inset-y-0 right-3 flex items-center text-sm font-semibold text-brand-500"
                aria-pressed={showPassword}
              >
                {showPassword ? 'Hide' : 'Show'}
              </button>
            </div>
          </label>
          <div id="password-guidelines" className="space-y-3">
            <PasswordStrength password={password} />
          </div>
        </div>
        <div className="space-y-3">
          <Checkbox
            checked={acceptTerms}
            onCheckedChange={(value) => setAcceptTerms(Boolean(value))}
            label="I accept the Terms of Service"
          />
          <Checkbox
            checked={acceptUpdates}
            onCheckedChange={(value) => setAcceptUpdates(Boolean(value))}
            label="I agree to receive updates via email"
          />
        </div>
        <Button type="submit" className="w-full" disabled={!isFormValid || isLoading}>
          {isLoading ? 'Creating account…' : 'Create account'}
        </Button>
      </form>
      <p className="text-center text-sm text-slate-500">
        Already have an account?{' '}
        <Link href="/auth/sign-in" className="font-semibold text-brand-500 hover:underline">
          Sign in
        </Link>
      </p>
    </AuthLayout>
  );
};

export default SignUpPage;
