import { useState } from 'react';
import { MainLayout } from '@/components/layout/MainLayout';
import { Button } from '@/components/ui/button';
import { Modal } from '@/components/ui/modal';
import { useToast } from '@/components/ui/use-toast';

const SupportPage = () => {
  const { pushToast } = useToast();
  const [message, setMessage] = useState('');

  const handleSubmit = (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    pushToast({ title: 'Support message sent', variant: 'success' });
    setMessage('');
  };

  return (
    <MainLayout userName="Arianna">
      <section className="space-y-4">
        <h2 className="text-xl font-semibold text-brand-700">Need help?</h2>
        <p className="text-sm text-slate-500">
          Reach our team at{' '}
          <a href="mailto:support@sphere.example.com" className="font-semibold text-brand-500 hover:underline">
            support@sphere.example.com
          </a>
        </p>
        <Modal
          title="Send a support message"
          description="Share your question with our crew. We will reply within 24 hours."
          trigger={<Button>Open support form</Button>}
        >
          <form className="space-y-4" onSubmit={handleSubmit}>
            <label className="block text-sm font-semibold text-brand-600">
              Message
              <textarea
                value={message}
                onChange={(event) => setMessage(event.target.value)}
                required
                rows={4}
                className="mt-1 w-full rounded-xl border border-brand-200 bg-white px-4 py-3 text-sm shadow-inner focus:border-brand-400 focus:outline-none focus:ring-2 focus:ring-brand-200"
              />
            </label>
            <Button type="submit" disabled={!message}>
              Submit
            </Button>
          </form>
        </Modal>
      </section>
    </MainLayout>
  );
};

export default SupportPage;
