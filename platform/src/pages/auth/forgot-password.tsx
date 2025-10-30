import { useState } from 'react';
import Link from 'next/link';
import { AuthLayout } from '@/components/layout/AuthLayout';
import { Button } from '@/components/ui/button';
import { requestPasswordReset } from '@/services/apiClient';
import { useToast } from '@/components/ui/use-toast';

const ForgotPasswordPage = () => {
  const { pushToast } = useToast();
  const [email, setEmail] = useState('');
  const [isLoading, setIsLoading] = useState(false);

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setIsLoading(true);
    try {
      await requestPasswordReset({ email });
      pushToast({ title: 'Password reset sent', description: 'Check your inbox for the reset link.', variant: 'success' });
    } catch (error) {
      pushToast({ title: 'Reset failed', description: 'We could not send the reset email.', variant: 'error' });
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <AuthLayout
      title="Reset your password"
      subtitle="Enter your email and we will send you a reset link"
    >
      <form onSubmit={handleSubmit} className="space-y-6" aria-label="Forgot password form">
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
        <Button type="submit" className="w-full" disabled={isLoading}>
          {isLoading ? 'Sending…' : 'Send reset link'}
        </Button>
        <Button asChild variant="ghost" className="w-full border border-brand-200 text-brand-600">
          <Link href="/auth/sign-in">Back to sign in</Link>
        </Button>
      </form>
    </AuthLayout>
  );
};

export default ForgotPasswordPage;
