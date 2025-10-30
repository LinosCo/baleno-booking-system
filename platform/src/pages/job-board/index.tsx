import { useState } from 'react';
import { MainLayout } from '@/components/layout/MainLayout';
import { jobs } from '@/utils/mockData';
import { Button } from '@/components/ui/button';

const JobBoardPage = () => {
  const [keyword, setKeyword] = useState('');
  const [location, setLocation] = useState('');

  const filtered = jobs.filter((job) => {
    const matchesKeyword = keyword
      ? job.title.toLowerCase().includes(keyword.toLowerCase()) ||
        job.company.toLowerCase().includes(keyword.toLowerCase())
      : true;
    const matchesLocation = location
      ? job.location.toLowerCase().includes(location.toLowerCase())
      : true;
    return matchesKeyword && matchesLocation;
  });

  return (
    <MainLayout userName="Arianna">
      <section className="space-y-6">
        <header className="space-y-3">
          <h2 className="text-xl font-semibold text-brand-700">Job Board</h2>
          <p className="text-sm text-slate-500">
            Discover open roles and internships curated for the Baleno Academy community.
          </p>
        </header>
        <div className="grid gap-4 rounded-3xl border border-brand-100 bg-white/80 p-6 shadow-soft md:grid-cols-3">
          <label className="text-sm font-semibold text-brand-600 md:col-span-1">
            Keyword
            <input
              type="search"
              value={keyword}
              onChange={(event) => setKeyword(event.target.value)}
              className="mt-1 w-full rounded-xl border border-brand-200 bg-white px-4 py-3 text-sm shadow-inner focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
              placeholder="Role or company"
            />
          </label>
          <label className="text-sm font-semibold text-brand-600 md:col-span-1">
            Location
            <input
              type="search"
              value={location}
              onChange={(event) => setLocation(event.target.value)}
              className="mt-1 w-full rounded-xl border border-brand-200 bg-white px-4 py-3 text-sm shadow-inner focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
              placeholder="City or remote"
            />
          </label>
          <div className="flex items-end md:col-span-1">
            <Button className="w-full" variant="secondary" onClick={() => { setKeyword(''); setLocation(''); }}>
              Clear
            </Button>
          </div>
        </div>
        <ul className="space-y-4">
          {filtered.map((job) => (
            <li
              key={job.id}
              className="rounded-3xl border border-brand-100 bg-white/80 p-6 shadow-soft"
            >
              <div className="flex flex-wrap items-center justify-between gap-4">
                <div>
                  <h3 className="text-lg font-semibold text-brand-700">{job.title}</h3>
                  <p className="text-sm text-slate-500">{job.company}</p>
                </div>
                <span className="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-600">{job.location}</span>
              </div>
              <Button className="mt-4" variant="secondary">
                View details
              </Button>
            </li>
          ))}
        </ul>
      </section>
    </MainLayout>
  );
};

export default JobBoardPage;
