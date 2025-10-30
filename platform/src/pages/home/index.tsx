import { MainLayout } from '@/components/layout/MainLayout';
import { Card } from '@/components/Card';
import { Carousel } from '@/components/Carousel';
import { courses, meetings } from '@/utils/mockData';

const HomePage = () => {
  return (
    <MainLayout userName="Arianna">
      <section className="space-y-6">
        <header>
          <h2 className="text-xl font-semibold text-brand-700">Learning Products</h2>
          <p className="text-sm text-slate-500">
            Continue where you left off and discover new Hyper Island experiences.
          </p>
        </header>
        <div className="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
          {courses.map((course) => (
            <Card
              key={course.id}
              image={course.image}
              title={course.title}
              subtitle={`${course.category} • ${course.duration} • ${course.mode} • ${course.language}`}
              tag={course.category}
              progress={course.progress}
              actionLabel="Start course"
              footer={
                <ul className="grid grid-cols-2 gap-2 text-xs font-semibold text-slate-500">
                  <li>{course.duration}</li>
                  <li>{course.lessons} lessons</li>
                  <li>{course.mode}</li>
                  <li>{course.language}</li>
                </ul>
              }
            />
          ))}
        </div>
      </section>
      <section className="space-y-4">
        <header className="flex items-center justify-between">
          <div>
            <h2 className="text-xl font-semibold text-brand-700">Meetings</h2>
            <p className="text-sm text-slate-500">Your upcoming sessions and coaching circles.</p>
          </div>
        </header>
        {meetings.length ? (
          <ul className="grid gap-4 md:grid-cols-2">
            {meetings.map((meeting) => (
              <li key={meeting.id} className="rounded-3xl border border-brand-100 bg-white/80 p-6 shadow-soft">
                <h3 className="text-lg font-semibold text-brand-700">{meeting.title}</h3>
                <p className="mt-2 text-sm text-slate-500">
                  {meeting.date} · {meeting.time}
                </p>
                <button className="mt-4 text-sm font-semibold text-brand-500 hover:underline">View details</button>
              </li>
            ))}
          </ul>
        ) : (
          <div className="rounded-3xl border border-dashed border-brand-200 bg-white/70 p-8 text-center text-sm font-semibold text-slate-500">
            No upcoming meetings – Go and explore!
          </div>
        )}
      </section>
      <Carousel
        title="Featured journeys"
        items={courses.map((course) => (
          <Card
            key={`carousel-${course.id}`}
            image={course.image}
            title={course.title}
            subtitle={course.category}
            progress={course.progress}
            tag="Journey"
            actionLabel="Explore"
          />
        ))}
      />
    </MainLayout>
  );
};

export default HomePage;
