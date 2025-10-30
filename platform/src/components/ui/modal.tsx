import * as Dialog from '@radix-ui/react-dialog';
import { X } from 'lucide-react';
import { cn } from '@/utils/cn';

interface ModalProps {
  title: string;
  description?: string;
  trigger: React.ReactNode;
  children: React.ReactNode;
}

export const Modal: React.FC<ModalProps> = ({ title, description, trigger, children }) => (
  <Dialog.Root>
    <Dialog.Trigger asChild>{trigger}</Dialog.Trigger>
    <Dialog.Portal>
      <Dialog.Overlay className="fixed inset-0 z-50 bg-slate-900/30 backdrop-blur" />
      <Dialog.Content
        className={cn(
          'fixed left-1/2 top-1/2 z-50 w-[95%] max-w-lg -translate-x-1/2 -translate-y-1/2 rounded-3xl border border-brand-100 bg-white p-8 shadow-2xl focus-visible:outline-none'
        )}
      >
        <div className="flex items-start justify-between gap-4">
          <div>
            <Dialog.Title className="text-xl font-semibold text-brand-700">{title}</Dialog.Title>
            {description && (
              <Dialog.Description className="mt-2 text-sm text-slate-500">{description}</Dialog.Description>
            )}
          </div>
          <Dialog.Close
            className="rounded-full border border-brand-200 p-2 text-brand-600 transition hover:bg-brand-50"
            aria-label="Close"
          >
            <X className="h-5 w-5" />
          </Dialog.Close>
        </div>
        <div className="mt-6 space-y-4">{children}</div>
      </Dialog.Content>
    </Dialog.Portal>
  </Dialog.Root>
);
