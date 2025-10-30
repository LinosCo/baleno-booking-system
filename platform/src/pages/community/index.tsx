import { useState } from 'react';
import { MainLayout } from '@/components/layout/MainLayout';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Button } from '@/components/ui/button';
import { Progress } from '@/components/ui/progress';

const checklist = [
  'Upload profile photo',
  'Add bio and professional info',
  'Specify interests and skills',
  'Indicate availability & languages',
];

const CommunityPage = () => {
  const [profileComplete, setProfileComplete] = useState(false);

  return (
    <MainLayout userName="Arianna">
      <Tabs defaultValue="explore">
        <div className="flex flex-wrap items-center justify-between gap-4">
          <TabsList>
            <TabsTrigger value="explore">Explore</TabsTrigger>
            <TabsTrigger value="meetings">Meetings</TabsTrigger>
            <TabsTrigger value="favourites">Favourites</TabsTrigger>
          </TabsList>
          <Button variant="secondary">Create a post</Button>
        </div>
        <TabsContent value="explore">
          {profileComplete ? (
            <div className="grid gap-6 md:grid-cols-2">
              <div className="rounded-3xl border border-brand-100 bg-white/80 p-6 shadow-soft">
                <h3 className="text-lg font-semibold text-brand-700">AI Builders Circle</h3>
                <p className="mt-2 text-sm text-slate-500">
                  Join weekly sessions to exchange with peers building learning products with AI.
                </p>
                <Button className="mt-4">Join circle</Button>
              </div>
              <div className="rounded-3xl border border-brand-100 bg-white/80 p-6 shadow-soft">
                <h3 className="text-lg font-semibold text-brand-700">Remote Facilitators Club</h3>
                <p className="mt-2 text-sm text-slate-500">Curated discussions on hybrid collaboration rituals.</p>
                <Button className="mt-4" variant="secondary">
                  Save for later
                </Button>
              </div>
            </div>
          ) : (
            <div className="rounded-3xl border border-dashed border-brand-200 bg-white/80 p-10 text-center shadow-soft">
              <h3 className="text-xl font-semibold text-brand-700">Complete your profile to explore Community</h3>
              <p className="mt-2 text-sm text-slate-500">
                Unlock curated circles, meetings and collaborations by finishing your profile.
              </p>
              <div className="mx-auto mt-6 max-w-md space-y-4 text-left">
                <Progress value={40} />
                <ul className="space-y-2 text-sm text-slate-500">
                  {checklist.map((item) => (
                    <li key={item} className="flex items-center gap-2">
                      <span className="flex h-6 w-6 items-center justify-center rounded-full bg-brand-50 text-xs font-semibold text-brand-600">
                        •
                      </span>
                      {item}
                    </li>
                  ))}
                </ul>
                <Button className="w-full" onClick={() => setProfileComplete(true)}>
                  Go to profile
                </Button>
              </div>
            </div>
          )}
        </TabsContent>
        <TabsContent value="meetings">
          <div className="rounded-3xl border border-brand-100 bg-white/80 p-8 text-center shadow-soft">
            <h3 className="text-lg font-semibold text-brand-700">Community meetings</h3>
            <p className="mt-2 text-sm text-slate-500">Stay tuned for the next gatherings curated for you.</p>
          </div>
        </TabsContent>
        <TabsContent value="favourites">
          <div className="rounded-3xl border border-brand-100 bg-white/80 p-8 text-center shadow-soft">
            <h3 className="text-lg font-semibold text-brand-700">Saved conversations</h3>
            <p className="mt-2 text-sm text-slate-500">Bookmark stories and resources to revisit quickly.</p>
          </div>
        </TabsContent>
      </Tabs>
    </MainLayout>
  );
};

export default CommunityPage;
