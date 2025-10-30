import * as TabsPrimitive from '@radix-ui/react-tabs';
import { cn } from '@/utils/cn';

export const Tabs = TabsPrimitive.Root;
export const TabsList = ({ className, ...props }: TabsPrimitive.TabsListProps) => (
  <TabsPrimitive.List
    className={cn(
      'inline-flex h-12 items-center justify-center rounded-2xl bg-brand-50 p-1 text-sm font-semibold text-brand-600 shadow-soft',
      className
    )}
    {...props}
  />
);

export const TabsTrigger = ({ className, ...props }: TabsPrimitive.TabsTriggerProps) => (
  <TabsPrimitive.Trigger
    className={cn(
      'inline-flex min-w-[120px] items-center justify-center rounded-xl px-4 py-2 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-200 data-[state=active]:bg-white data-[state=active]:text-brand-700 data-[state=active]:shadow-soft',
      className
    )}
    {...props}
  />
);

export const TabsContent = ({ className, ...props }: TabsPrimitive.TabsContentProps) => (
  <TabsPrimitive.Content className={cn('mt-6 focus-visible:outline-none', className)} {...props} />
);
