import * as CheckboxPrimitive from '@radix-ui/react-checkbox';
import { Check } from 'lucide-react';
import { cn } from '@/utils/cn';

export interface CheckboxProps extends CheckboxPrimitive.CheckboxProps {
  label: string;
  description?: string;
}

export const Checkbox: React.FC<CheckboxProps> = ({ label, description, className, ...props }) => (
  <label className={cn('flex cursor-pointer items-start gap-3 text-sm text-slate-600', className)}>
    <CheckboxPrimitive.Root
      className="mt-1 flex h-5 w-5 items-center justify-center rounded-md border border-brand-200 bg-white shadow-sm transition hover:border-brand-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-200"
      {...props}
    >
      <CheckboxPrimitive.Indicator>
        <Check className="h-4 w-4 text-brand-600" />
      </CheckboxPrimitive.Indicator>
    </CheckboxPrimitive.Root>
    <span>
      <span className="font-semibold text-brand-600">{label}</span>
      {description && <p className="text-xs text-slate-500">{description}</p>}
    </span>
  </label>
);
