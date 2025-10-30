import Image from 'next/image';
import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';
import { cn } from '@/utils/cn';

interface CardProps {
  image: string;
  title: string;
  subtitle?: string;
  progress?: number;
  tag?: string;
  actionLabel?: string;
  onAction?: () => void;
  footer?: React.ReactNode;
  className?: string;
}

export const Card: React.FC<CardProps> = ({
  image,
  title,
  subtitle,
  progress,
  tag,
  actionLabel = 'Open',
  onAction,
  footer,
  className,
}) => {
  return (
    <article className={cn('group overflow-hidden rounded-3xl border border-brand-100 bg-white shadow-soft transition hover:-translate-y-1 hover:shadow-xl focus-within:ring-2 focus-within:ring-brand-200', className)}>
      <div className="relative h-40 w-full overflow-hidden">
        <Image src={image} alt={title} fill className="object-cover transition duration-500 group-hover:scale-105" />
        {tag && (
          <span className="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-brand-600">
            {tag}
          </span>
        )}
      </div>
      <div className="space-y-4 p-6">
        <div>
          <h3 className="text-lg font-semibold text-brand-700">{title}</h3>
          {subtitle && <p className="mt-1 text-sm text-slate-500">{subtitle}</p>}
        </div>
        {typeof progress === 'number' && (
          <div className="space-y-2">
            <div className="flex items-center justify-between text-xs font-semibold text-slate-500">
              <span>Progress</span>
              <span>{progress}%</span>
            </div>
            <Progress value={progress} />
          </div>
        )}
        {footer}
        <Button className="w-full" onClick={onAction} aria-label={`${actionLabel} ${title}`}>
          {actionLabel}
        </Button>
      </div>
    </article>
  );
};
