import { useEffect } from 'react';
import { useRouter } from 'next/router';
import { Spinner } from '@/components/ui/spinner';

const GoogleCallbackPage = () => {
  const router = useRouter();

  useEffect(() => {
    const token = router.query.token as string | undefined;
    if (token) {
      // Placeholder: set cookie/token and redirect
      void router.replace('/home');
    }
  }, [router]);

  return (
    <div className="flex min-h-screen items-center justify-center bg-gradient-to-br from-brand-50 via-white to-purple-50">
      <div className="space-y-4 text-center">
        <Spinner />
        <p className="text-sm font-semibold text-brand-600">Signing you in with Google…</p>
      </div>
    </div>
  );
};

export default GoogleCallbackPage;
