import { Button } from '@/components/ui/button';
import { cn } from '@/utils/cn';

interface FilterPanelProps {
  title: string;
  actions?: React.ReactNode;
  className?: string;
  children: React.ReactNode;
}

export const FilterPanel: React.FC<FilterPanelProps> = ({ title, actions, className, children }) => {
  return (
    <section
      className={cn(
        'rounded-3xl border border-brand-100 bg-white/80 p-6 shadow-soft backdrop-blur-sm focus-within:ring-2 focus-within:ring-brand-200',
        className
      )}
      aria-label={`${title} filters`}
    >
      <div className="flex flex-wrap items-center justify-between gap-4">
        <h2 className="text-lg font-semibold text-brand-700">{title}</h2>
        {actions}
      </div>
      <div className="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">{children}</div>
    </section>
  );
};

export const FilterResetButton: React.FC<React.ComponentProps<typeof Button>> = ({ className, ...props }) => (
  <Button variant="ghost" className={cn('border border-brand-200 text-brand-600', className)} {...props} />
);
