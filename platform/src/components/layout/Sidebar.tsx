import Link from 'next/link';
import { useRouter } from 'next/router';
import { cn } from '@/utils/cn';
import {
  LayoutDashboard,
  Users,
  BookOpen,
  CalendarDays,
  Library,
  BadgePercent,
  Briefcase,
  LifeBuoy,
  Settings,
} from 'lucide-react';
import { ForwardRefExoticComponent, RefAttributes, SVGProps } from 'react';

type IconType = ForwardRefExoticComponent<Omit<SVGProps<SVGSVGElement>, 'ref'> & RefAttributes<SVGSVGElement>>;

const navItems: Array<{ label: string; href: string; icon: IconType }> = [
  { label: 'Home', href: '/home', icon: LayoutDashboard },
  { label: 'Community', href: '/community', icon: Users },
  { label: 'Courses', href: '/courses', icon: BookOpen },
  { label: 'Events', href: '/events', icon: CalendarDays },
  { label: 'Resources', href: '/resources', icon: Library },
  { label: 'Deals', href: '/deals', icon: BadgePercent },
  { label: 'Job Board', href: '/job-board', icon: Briefcase },
  { label: 'Ask for Support', href: '/support', icon: LifeBuoy },
  { label: 'Settings', href: '/settings', icon: Settings },
];

interface SidebarProps {
  variant?: 'desktop' | 'mobile';
}

export const Sidebar: React.FC<SidebarProps> = ({ variant = 'desktop' }) => {
  const router = useRouter();
  const baseClasses =
    'inset-y-0 left-0 z-30 flex w-72 flex-col justify-between border-r border-white/50 bg-white/80 px-6 py-8 shadow-soft backdrop-blur-xl';

  return (
    <aside
      className={cn(baseClasses, variant === 'desktop' ? 'hidden lg:fixed lg:flex' : 'fixed lg:hidden')}
      aria-label="Primary"
    >
      <div className="space-y-6">
        <div>
          <Link href="/home" className="flex items-center gap-3 text-lg font-bold text-brand-600">
            <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-400 via-brand-500 to-accent-500 text-white shadow-soft">
              TG
            </div>
            <div>
              <p className="leading-tight">Talent Garden</p>
              <p className="text-sm font-medium text-slate-500">× Hyper Island</p>
            </div>
          </Link>
        </div>
        <nav className="space-y-1">
          {navItems.map((item) => {
            const isActive = router.pathname === item.href;
            const Icon = item.icon;
            return (
              <Link
                key={item.label}
                href={item.href}
                className={cn(
                  'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-brand-50 hover:text-brand-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-200',
                  isActive && 'bg-brand-100 text-brand-600 shadow-inner'
                )}
                aria-current={isActive ? 'page' : undefined}
              >
                <Icon className="h-5 w-5" aria-hidden />
                <span>{item.label}</span>
              </Link>
            );
          })}
        </nav>
      </div>
      <div className="mt-8 rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-700">Beta 1.5</div>
    </aside>
  );
};
