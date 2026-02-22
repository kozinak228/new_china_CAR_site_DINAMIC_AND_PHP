import React from 'react';
import Header from './components/Header';
import Hero from './components/Hero';
import Advantages from './components/Advantages';

function App() {
  return (
    <div className="min-h-screen bg-background-dark">
      <Header />
      <Hero />
      <Advantages />

      {/* Footer Quote Section from Stitch */}
      <footer className="bg-background-dark/95 py-12 px-6 border-t border-white/5 text-center relative z-10">
        <div className="max-w-md mx-auto">
          <span className="material-symbols-outlined text-primary text-4xl mb-4">format_quote</span>
          <p className="text-white font-serif italic text-xl md:text-2xl leading-relaxed mb-6">
            "Мы не просто привозим машины, мы доставляем новый стандарт жизни и комфорта."
          </p>
          <div className="h-px w-12 bg-primary mx-auto mb-6"></div>
          <p className="text-slate-500 uppercase text-xs tracking-[0.2em]">INVOICE PREMIUM AUTO TEAM · 2026</p>
        </div>
      </footer>
    </div>
  );
}

export default App;
