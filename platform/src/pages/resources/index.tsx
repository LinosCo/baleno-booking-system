import { MainLayout } from '@/components/layout/MainLayout';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Carousel } from '@/components/Carousel';
import { Card } from '@/components/Card';
import { resources } from '@/utils/mockData';

const ResourcesPage = () => {
  return (
    <MainLayout userName="Arianna">
      <Tabs defaultValue="community-contents">
        <TabsList>
          <TabsTrigger value="community-contents">Community Contents</TabsTrigger>
          <TabsTrigger value="toolbox">Academy Toolbox</TabsTrigger>
          <TabsTrigger value="saved">Saved</TabsTrigger>
        </TabsList>
        <TabsContent value="community-contents" className="space-y-10">
          <Carousel
            title="Curated Playlists"
            items={resources.map((resource) => (
              <Card
                key={resource.id}
                image={resource.image}
                title={resource.title}
                subtitle={`${resource.program} • ${resource.category}`}
                tag="Playlist"
                actionLabel="Open playlist"
                footer={<p className="text-sm text-slate-500">{resource.author}</p>}
              />
            ))}
          />
          <div className="rounded-3xl border border-brand-100 bg-white/80 p-8 shadow-soft">
            <h3 className="text-lg font-semibold text-brand-700">Community knowledge hub</h3>
            <p className="mt-2 text-sm text-slate-500">
              Browse field guides, templates and stories curated by the Baleno Academy community.
            </p>
          </div>
        </TabsContent>
        <TabsContent value="toolbox">
          <div className="rounded-3xl border border-brand-100 bg-white/80 p-8 shadow-soft">
            <h3 className="text-lg font-semibold text-brand-700">Baleno Academy Toolbox</h3>
            <p className="mt-2 text-sm text-slate-500">
              Access facilitation canvases, warm-up activities and experience blueprints.
            </p>
          </div>
        </TabsContent>
        <TabsContent value="saved">
          <div className="rounded-3xl border border-brand-100 bg-white/80 p-8 text-center shadow-soft">
            <p className="text-sm text-slate-500">Save your favourite resources to access them quickly.</p>
          </div>
        </TabsContent>
      </Tabs>
    </MainLayout>
  );
};

export default ResourcesPage;
