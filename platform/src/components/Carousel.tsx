import { useRef } from 'react';
import { Button } from '@/components/ui/button';
import { ChevronLeft, ChevronRight } from 'lucide-react';

interface CarouselProps {
  title: string;
  items: React.ReactNode[];
}

export const Carousel: React.FC<CarouselProps> = ({ title, items }) => {
  const listRef = useRef<HTMLDivElement>(null);

  const scroll = (direction: 'left' | 'right') => {
    const container = listRef.current;
    if (!container) return;
    const offset = direction === 'left' ? -320 : 320;
    container.scrollBy({ left: offset, behavior: 'smooth' });
  };

  return (
    <section className="space-y-4">
      <div className="flex items-center justify-between">
        <h2 className="text-xl font-semibold text-brand-700">{title}</h2>
        <div className="flex gap-2">
          <Button variant="ghost" size="icon" aria-label="Scroll left" onClick={() => scroll('left')}>
            <ChevronLeft className="h-5 w-5" />
          </Button>
          <Button variant="ghost" size="icon" aria-label="Scroll right" onClick={() => scroll('right')}>
            <ChevronRight className="h-5 w-5" />
          </Button>
        </div>
      </div>
      <div
        ref={listRef}
        className="flex gap-4 overflow-x-auto pb-4"
        role="region"
        aria-roledescription="carousel"
        aria-label={title}
      >
        {items.map((item, index) => (
          <div key={index} className="min-w-[280px] flex-1">{item}</div>
        ))}
      </div>
    </section>
  );
};
