import { Sidebar } from './Sidebar';
import { Header } from './Header';
import { ReactNode, useState } from 'react';
import { Button } from '@/components/ui/button';
import { motion, AnimatePresence } from 'framer-motion';
import { X } from 'lucide-react';

interface MainLayoutProps {
  userName: string;
  children: ReactNode;
}

export const MainLayout: React.FC<MainLayoutProps> = ({ userName, children }) => {
  const [isMobileMenuOpen, setMobileMenuOpen] = useState(false);

  return (
    <div className="lg:pl-72">
      <Sidebar variant="desktop" />
      <AnimatePresence>
        {isMobileMenuOpen && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden"
            onClick={() => setMobileMenuOpen(false)}
          >
            <motion.nav
              initial={{ x: -300 }}
              animate={{ x: 0 }}
              exit={{ x: -300 }}
              className="relative h-full w-72"
              onClick={(event) => event.stopPropagation()}
            >
              <Sidebar variant="mobile" />
              <Button
                variant="ghost"
                size="icon"
                className="absolute right-4 top-4 border border-brand-200 text-brand-600"
                onClick={() => setMobileMenuOpen(false)}
                aria-label="Close menu"
              >
                <X className="h-5 w-5" />
              </Button>
            </motion.nav>
          </motion.div>
        )}
      </AnimatePresence>
      <div className="flex min-h-screen flex-col">
        <Header userName={userName} onMenuClick={() => setMobileMenuOpen(true)} />
        <main className="flex-1 px-4 pb-16 pt-6 lg:px-8">
          <div className="mx-auto max-w-6xl space-y-10">{children}</div>
        </main>
      </div>
    </div>
  );
};
