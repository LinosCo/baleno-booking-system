import { useState } from 'react';
import { MainLayout } from '@/components/layout/MainLayout';
import { Carousel } from '@/components/Carousel';
import { Card } from '@/components/Card';
import { Button } from '@/components/ui/button';
import { deals } from '@/utils/mockData';

const filters = ['All', 'Education', 'Most Popular'];

const DealsPage = () => {
  const [activeFilter, setActiveFilter] = useState('All');

  return (
    <MainLayout userName="Arianna">
      <section className="space-y-6">
        <header className="space-y-3">
          <h2 className="text-xl font-semibold text-brand-700">Explore our course offers</h2>
          <div className="flex flex-wrap gap-3">
            {filters.map((filter) => (
              <Button
                key={filter}
                variant={filter === activeFilter ? 'primary' : 'secondary'}
                onClick={() => setActiveFilter(filter)}
                className={filter === activeFilter ? '' : 'border border-brand-200 text-brand-600'}
              >
                {filter}
              </Button>
            ))}
          </div>
        </header>
        <Carousel
          title="Featured offers"
          items={deals.map((deal) => (
            <Card
              key={deal.id}
              image="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=800&q=80"
              title={deal.title}
              subtitle={deal.summary}
              actionLabel="Discover more"
              tag={activeFilter}
            />
          ))}
        />
      </section>
    </MainLayout>
  );
};

export default DealsPage;
