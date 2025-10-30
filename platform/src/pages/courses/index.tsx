import { useState } from 'react';
import { MainLayout } from '@/components/layout/MainLayout';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Card } from '@/components/Card';
import { courses } from '@/utils/mockData';
import { Button } from '@/components/ui/button';

const CoursesPage = () => {
  const [search, setSearch] = useState('');
  const filtered = courses.filter((course) => course.title.toLowerCase().includes(search.toLowerCase()));

  return (
    <MainLayout userName="Arianna">
      <Tabs defaultValue="my-courses">
        <TabsList>
          <TabsTrigger value="my-courses">My courses</TabsTrigger>
          <TabsTrigger value="on-demand">On Demand</TabsTrigger>
        </TabsList>
        <TabsContent value="my-courses">
          <div className="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            {courses.map((course) => (
              <Card
                key={course.id}
                image={course.image}
                title={course.title}
                subtitle={`${course.duration} • ${course.mode}`}
                progress={course.progress}
                tag="Assigned"
                actionLabel="Start course"
              />
            ))}
          </div>
        </TabsContent>
        <TabsContent value="on-demand">
          <div className="flex flex-col gap-6">
            <div className="flex flex-wrap items-center justify-between gap-3 rounded-3xl border border-brand-100 bg-white/80 p-4 shadow-soft">
              <input
                type="search"
                value={search}
                onChange={(event) => setSearch(event.target.value)}
                placeholder="Search for a course"
                aria-label="Search courses"
                className="flex-1 rounded-xl border border-brand-200 bg-white px-4 py-3 text-sm shadow-inner focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
              />
              <Button variant="ghost" className="border border-brand-200 text-brand-600">
                Filters
              </Button>
            </div>
            <div className="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
              {filtered.map((course) => (
                <Card
                  key={`on-demand-${course.id}`}
                  image={course.image}
                  title={course.title}
                  subtitle={`${course.category} • ${course.duration}`}
                  progress={course.progress}
                  tag="On Demand"
                  actionLabel="Start course"
                />
              ))}
            </div>
          </div>
        </TabsContent>
      </Tabs>
    </MainLayout>
  );
};

export default CoursesPage;
