import { useMemo, useState } from 'react';
import { MainLayout } from '@/components/layout/MainLayout';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Card } from '@/components/Card';
import { Carousel } from '@/components/Carousel';
import { Button } from '@/components/ui/button';
import { FilterPanel, FilterResetButton } from '@/components/FilterPanel';
import { events } from '@/utils/mockData';

const locations = ['All', 'Milan', 'Stockholm', 'Online'];

const EventsPage = () => {
  const [activeTab, setActiveTab] = useState<'explore' | 'saved'>('explore');
  const [location, setLocation] = useState('All');
  const [startDate, setStartDate] = useState('');
  const [endDate, setEndDate] = useState('');

  const filteredEvents = useMemo(() => {
    return events.filter((event) => {
      const matchesLocation = location === 'All' || event.location === location;
      const matchesStart = startDate ? event.startDate >= startDate : true;
      const matchesEnd = endDate ? event.endDate <= endDate : true;
      return matchesLocation && matchesStart && matchesEnd;
    });
  }, [location, startDate, endDate]);

  const resetFilters = () => {
    setLocation('All');
    setStartDate('');
    setEndDate('');
  };

  return (
    <MainLayout userName="Arianna">
      <Tabs value={activeTab} onValueChange={(value) => setActiveTab(value as typeof activeTab)}>
        <TabsList>
          <TabsTrigger value="explore">Explore</TabsTrigger>
          <TabsTrigger value="saved">Saved</TabsTrigger>
        </TabsList>
        <TabsContent value="explore" className="space-y-10">
          <Carousel
            title="Top Events"
            items={events.map((event) => (
              <Card
                key={`top-${event.id}`}
                image={event.image}
                title={event.title}
                subtitle={`${event.participants} participants • ${event.category}`}
                tag={event.category}
                actionLabel="Join event"
              />
            ))}
          />
          <FilterPanel
            title="Filters"
            actions={<FilterResetButton onClick={resetFilters}>Clear filters</FilterResetButton>}
          >
            <label className="text-sm font-semibold text-brand-600">
              Location
              <select
                className="mt-1 w-full rounded-xl border border-brand-200 bg-white px-4 py-3 text-sm shadow-inner focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
                value={location}
                onChange={(event) => setLocation(event.target.value)}
              >
                {locations.map((item) => (
                  <option key={item}>{item}</option>
                ))}
              </select>
            </label>
            <label className="text-sm font-semibold text-brand-600">
              Start date
              <input
                type="date"
                value={startDate}
                onChange={(event) => setStartDate(event.target.value)}
                className="mt-1 w-full rounded-xl border border-brand-200 bg-white px-4 py-3 text-sm shadow-inner focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
              />
            </label>
            <label className="text-sm font-semibold text-brand-600">
              End date
              <input
                type="date"
                value={endDate}
                onChange={(event) => setEndDate(event.target.value)}
                className="mt-1 w-full rounded-xl border border-brand-200 bg-white px-4 py-3 text-sm shadow-inner focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
              />
            </label>
          </FilterPanel>
          <div className="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            {filteredEvents.map((event) => (
              <Card
                key={event.id}
                image={event.image}
                title={event.title}
                subtitle={`${event.location} • ${event.startDate}`}
                tag={event.category}
                actionLabel="Save event"
              />
            ))}
          </div>
        </TabsContent>
        <TabsContent value="saved">
          <div className="rounded-3xl border border-brand-100 bg-white/80 p-8 text-center shadow-soft">
            <h3 className="text-lg font-semibold text-brand-700">Saved events</h3>
            <p className="mt-2 text-sm text-slate-500">Bookmark events to see them here.</p>
            <Button className="mt-4" variant="secondary">
              Explore events
            </Button>
          </div>
        </TabsContent>
      </Tabs>
    </MainLayout>
  );
};

export default EventsPage;
