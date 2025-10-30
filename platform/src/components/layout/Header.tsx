import Image from 'next/image';
import { Menu, Moon, Sun } from 'lucide-react';
import { useTheme } from 'next-themes';
import { Button } from '@/components/ui/button';

interface HeaderProps {
  userName: string;
  onMenuClick?: () => void;
}

export const Header: React.FC<HeaderProps> = ({ userName, onMenuClick }) => {
  const { theme, setTheme } = useTheme();

  const toggleTheme = () => {
    setTheme(theme === 'dark' ? 'light' : 'dark');
  };

  return (
    <header className="sticky top-0 z-20 flex items-center justify-between bg-white/70 px-4 py-4 backdrop-blur-xl shadow-soft dark:bg-slate-950/50 lg:px-8">
      <div className="flex items-center gap-4">
        <button
          className="inline-flex items-center justify-center rounded-xl border border-brand-200 bg-white p-2 text-brand-600 shadow-soft focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-200 lg:hidden"
          onClick={onMenuClick}
          aria-label="Toggle navigation"
        >
          <Menu className="h-5 w-5" />
        </button>
        <div>
          <p className="text-sm font-medium text-slate-500">Hello,</p>
          <h1 className="text-2xl font-bold text-brand-700 dark:text-brand-200">{userName}!</h1>
        </div>
      </div>
      <div className="flex items-center gap-3">
        <Button
          variant="ghost"
          size="icon"
          aria-label="Toggle dark mode"
          onClick={toggleTheme}
          className="border border-brand-200 bg-white text-brand-600 hover:bg-brand-50"
        >
          {theme === 'dark' ? <Sun className="h-5 w-5" /> : <Moon className="h-5 w-5" />}
        </Button>
        <div className="flex items-center gap-3 rounded-2xl bg-brand-50 px-3 py-2">
          <div>
            <p className="text-sm font-semibold text-brand-600">Member</p>
            <p className="text-xs text-slate-500">Beta cohort</p>
          </div>
          <div className="h-12 w-12 overflow-hidden rounded-full border-2 border-white shadow-soft">
            <Image
              src="https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=crop&w=80&q=80"
              alt="User avatar"
              width={48}
              height={48}
            />
          </div>
        </div>
      </div>
    </header>
  );
};
