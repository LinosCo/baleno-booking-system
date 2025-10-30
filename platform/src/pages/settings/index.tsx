import { useState } from 'react';
import { MainLayout } from '@/components/layout/MainLayout';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Toggle } from '@/components/ui/toggle';
import { Modal } from '@/components/ui/modal';
import { useToast } from '@/components/ui/use-toast';

const SettingsPage = () => {
  const { pushToast } = useToast();
  const [notifications, setNotifications] = useState({
    email: true,
    sms: false,
    product: true,
  });
  const [privacy, setPrivacy] = useState({
    showProfile: true,
    shareActivity: false,
  });
  const [cookiesAccepted, setCookiesAccepted] = useState(false);

  const handleSave = () => {
    pushToast({ title: 'Settings saved', variant: 'success' });
  };

  return (
    <MainLayout userName="Arianna">
      <div className="grid gap-8 lg:grid-cols-2">
        <section className="space-y-4 rounded-3xl border border-brand-100 bg-white/80 p-6 shadow-soft">
          <header>
            <h2 className="text-lg font-semibold text-brand-700">Edit profile</h2>
            <p className="text-sm text-slate-500">Update your details for the community.</p>
          </header>
          <div className="space-y-4">
            <label className="text-sm font-semibold text-brand-600">
              Full name
              <input
                type="text"
                defaultValue="Arianna Rossi"
                className="mt-1 w-full rounded-xl border border-brand-200 bg-white px-4 py-3 text-sm shadow-inner focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
              />
            </label>
            <label className="text-sm font-semibold text-brand-600">
              Bio
              <textarea
                defaultValue="Learning designer crafting hybrid journeys."
                rows={4}
                className="mt-1 w-full rounded-xl border border-brand-200 bg-white px-4 py-3 text-sm shadow-inner focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
              />
            </label>
            <Button onClick={handleSave}>Save profile</Button>
          </div>
        </section>
        <section className="space-y-4 rounded-3xl border border-brand-100 bg-white/80 p-6 shadow-soft">
          <header>
            <h2 className="text-lg font-semibold text-brand-700">Notifications</h2>
            <p className="text-sm text-slate-500">Choose how we keep in touch.</p>
          </header>
          <div className="space-y-3">
            <Checkbox
              checked={notifications.email}
              onCheckedChange={(value) => setNotifications((prev) => ({ ...prev, email: Boolean(value) }))}
              label="Email updates"
              description="Receive program news and reminders"
            />
            <Checkbox
              checked={notifications.sms}
              onCheckedChange={(value) => setNotifications((prev) => ({ ...prev, sms: Boolean(value) }))}
              label="SMS alerts"
              description="Get text notifications for live sessions"
            />
            <Checkbox
              checked={notifications.product}
              onCheckedChange={(value) => setNotifications((prev) => ({ ...prev, product: Boolean(value) }))}
              label="Product announcements"
            />
          </div>
        </section>
        <section className="space-y-4 rounded-3xl border border-brand-100 bg-white/80 p-6 shadow-soft">
          <header>
            <h2 className="text-lg font-semibold text-brand-700">Privacy preferences</h2>
            <p className="text-sm text-slate-500">Control what your peers can see.</p>
          </header>
          <div className="space-y-4">
            <div className="flex items-center justify-between gap-4 rounded-2xl bg-brand-50 px-4 py-3">
              <div>
                <p className="text-sm font-semibold text-brand-600">Show profile to community</p>
                <p className="text-xs text-slate-500">Only members can see your profile details.</p>
              </div>
              <Toggle
                pressed={privacy.showProfile}
                onClick={() => setPrivacy((prev) => ({ ...prev, showProfile: !prev.showProfile }))}
              />
            </div>
            <div className="flex items-center justify-between gap-4 rounded-2xl bg-brand-50 px-4 py-3">
              <div>
                <p className="text-sm font-semibold text-brand-600">Share learning activity</p>
                <p className="text-xs text-slate-500">Display your progress in community feeds.</p>
              </div>
              <Toggle
                pressed={privacy.shareActivity}
                onClick={() => setPrivacy((prev) => ({ ...prev, shareActivity: !prev.shareActivity }))}
              />
            </div>
          </div>
        </section>
        <section className="space-y-4 rounded-3xl border border-brand-100 bg-white/80 p-6 shadow-soft">
          <header>
            <h2 className="text-lg font-semibold text-brand-700">Cookie consent</h2>
            <p className="text-sm text-slate-500">Adjust how we collect insights.</p>
          </header>
          <Modal
            title="Cookie preferences"
            description="Choose the cookies that help us personalize your experience."
            trigger={<Button>{cookiesAccepted ? 'Update cookie settings' : 'Review cookie settings'}</Button>}
          >
            <div className="space-y-3">
              <Checkbox checked readOnly label="Essential cookies" description="Required for secure login" />
              <Checkbox
                checked={cookiesAccepted}
                onCheckedChange={(value) => setCookiesAccepted(Boolean(value))}
                label="Analytics cookies"
                description="Help us improve the platform"
              />
              <Button onClick={() => pushToast({ title: 'Cookie preferences saved', variant: 'success' })}>Save</Button>
            </div>
          </Modal>
        </section>
      </div>
    </MainLayout>
  );
};

export default SettingsPage;
