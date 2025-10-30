import type { AppProps } from 'next/app';
import { useEffect } from 'react';
import { ThemeProvider } from 'next-themes';
import { ToastProvider } from '@/components/ui/use-toast';
import { ToastViewport } from '@/components/ui/toaster';
import '@/styles/globals.css';

const App = ({ Component, pageProps }: AppProps) => {
  useEffect(() => {
    const doc = document.documentElement;
    doc.lang = 'en';
  }, []);

  return (
    <ThemeProvider attribute="class" defaultTheme="light" enableSystem={false}>
      <ToastProvider>
        <div className="min-h-screen bg-gradient-to-br from-brand-50 via-white to-purple-50">
          <Component {...pageProps} />
        </div>
        <ToastViewport />
      </ToastProvider>
    </ThemeProvider>
  );
};

export default App;
