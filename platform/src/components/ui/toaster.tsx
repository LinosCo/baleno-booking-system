import { useToast } from './use-toast';
import { cn } from '@/utils/cn';

export const ToastViewport = () => {
  const { toasts, removeToast } = useToast();

  if (!toasts.length) return null;

  return (
    <div className="fixed top-4 right-4 z-50 space-y-3">
      {toasts.map((toast) => (
        <div
          key={toast.id}
          role="status"
          className={cn(
            'w-80 rounded-2xl border border-brand-200 bg-white/95 p-4 shadow-soft backdrop-blur-sm transition focus-within:ring-2 focus-within:ring-brand-200 focus-within:ring-offset-2 dark:bg-slate-900/90',
            toast.variant === 'error' && 'border-red-300',
            toast.variant === 'success' && 'border-emerald-300'
          )}
        >
          <div className="flex items-start justify-between gap-3">
            <div>
              <p className="text-sm font-semibold text-brand-700 dark:text-brand-200">{toast.title}</p>
              {toast.description && (
                <p className="mt-1 text-sm text-slate-600 dark:text-slate-300">{toast.description}</p>
              )}
            </div>
            <button
              aria-label="Dismiss notification"
              onClick={() => removeToast(toast.id)}
              className="text-sm font-medium text-brand-500 hover:text-brand-600"
            >
              Close
            </button>
          </div>
        </div>
      ))}
    </div>
  );
};
