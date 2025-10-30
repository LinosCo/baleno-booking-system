import { cn } from '@/utils/cn';

interface ToggleProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  pressed?: boolean;
}

export const Toggle: React.FC<ToggleProps> = ({ pressed = false, className, children, ...props }) => (
  <button
    type="button"
    aria-pressed={pressed}
    className={cn(
      'flex h-10 w-20 items-center rounded-full border border-brand-200 bg-white p-1 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-200',
      pressed ? 'justify-end bg-brand-500 text-white' : 'justify-start text-brand-500',
      className
    )}
    {...props}
  >
    <span className="h-8 w-8 rounded-full bg-white shadow-soft" />
    <span className="sr-only">Toggle</span>
    {children}
  </button>
);
