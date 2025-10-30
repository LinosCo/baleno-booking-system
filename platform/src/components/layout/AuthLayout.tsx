import Link from 'next/link';

interface AuthLayoutProps {
  title: string;
  subtitle?: string;
  children: React.ReactNode;
}

export const AuthLayout: React.FC<AuthLayoutProps> = ({ title, subtitle, children }) => (
  <div className="flex min-h-screen items-center justify-center bg-gradient-to-br from-brand-50 via-white to-purple-50 px-4 py-12">
    <div className="w-full max-w-md space-y-8 rounded-3xl border border-brand-100 bg-white/80 p-10 shadow-2xl backdrop-blur">
      <div className="space-y-2 text-center">
        <Link href="/home" className="text-sm font-semibold uppercase tracking-wide text-brand-500">
          Baleno Corporate Academy
        </Link>
        <h1 className="text-2xl font-bold text-brand-700">{title}</h1>
        {subtitle && <p className="text-sm text-slate-500">{subtitle}</p>}
      </div>
      {children}
    </div>
  </div>
);
