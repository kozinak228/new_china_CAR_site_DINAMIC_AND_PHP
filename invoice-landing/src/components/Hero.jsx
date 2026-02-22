import React from 'react';

const Hero = () => {
    return (
        <main className="relative min-h-screen flex flex-col pt-20 jade-gradient">
            {/* Background Texture Overlay */}
            <div className="absolute inset-0 stone-texture pointer-events-none"></div>

            {/* Architectural Visual Section */}
            <div className="relative w-full aspect-[4/5] md:aspect-[21/9] overflow-hidden">
                {/* Moon Gate Structure & Car */}
                <div className="absolute inset-0 flex items-center justify-center">
                    {/* Concrete Moon Gate with Satin Gold Trim */}
                    <div className="relative w-72 h-72 sm:w-96 sm:h-96 md:w-[600px] md:h-[600px] rounded-full border-[12px] border-slate-700/50 shadow-[0_0_40px_rgba(207,161,23,0.2)]">
                        <div className="absolute inset-0 rounded-full border-2 border-primary/40 -m-1"></div>
                        {/* Content inside gate (The Car) */}
                        <div className="absolute inset-0 overflow-hidden rounded-full flex items-center justify-center">
                            <img
                                alt="Premium dark metallic EV SUV parked"
                                className="w-full h-full object-cover scale-110"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBC8-V6mGqZflmWwV3Nb9MSZt1yGfhXy8wOiXlkgp6i4LYCJSsuOdCLyMx3MbCxESlmWR9mGHxYdKlXMVhiZlaaLaJaaDoogPVxdbgNwtpLYXG_sE0UezLQQ8g0zirLEUETPnyb5HVISj93tpiokLLPyC4_GUNCkfNFgKFBOs3-BjR9KAQi6khghc9hPh5hYTe-egEU2zIm8sqIzw6aM7BM0p3tzLeVDdCLyWQkAOZcOXx5m58Jfnnx637uyw7nbeuuaSLCrEbAgTzq"
                            />
                        </div>
                    </div>
                </div>
                {/* Reflective Surface Effect */}
                <div className="absolute bottom-0 left-0 right-0 h-32 reflection-overlay"></div>
                {/* Floating Decorative Light Specs */}
                <div className="absolute top-1/4 right-10 w-1 h-1 bg-primary rounded-full blur-sm opacity-50"></div>
                <div className="absolute bottom-1/3 left-10 w-2 h-2 bg-primary rounded-full blur-md opacity-30"></div>
            </div>

            {/* Text Content Area */}
            <div className="relative z-10 flex flex-col px-6 pb-12 -mt-12 text-center items-center gap-6">
                <div className="flex flex-col gap-4 max-w-2xl">
                    <h1 className="font-serif text-white text-4xl leading-tight tracking-tight">
                        Авто из Китая: фиксируем цену в рублях и сроки договором
                    </h1>
                    <p className="text-slate-300 text-lg leading-relaxed font-normal">
                        Экономия до 30% от рыночной цены в РФ. Прямые поставки <span className="text-primary italic">Li Auto</span> и <span className="text-primary italic">Zeekr</span> с полной юридической гарантией.
                    </p>
                </div>

                {/* Primary CTA */}
                <div className="w-full max-w-sm mt-4">
                    <button className="w-full py-4 px-6 bg-primary hover:bg-yellow-500 text-background-dark font-bold text-base rounded-lg shadow-[0_10px_20px_rgba(207,161,23,0.3)] hover:scale-105 transition-all duration-300">
                        Рассчитать итоговую стоимость
                    </button>
                    <p className="mt-3 text-xs text-slate-500 uppercase tracking-widest">со всеми пошлинами и доставкой</p>
                </div>

                {/* Trust Badges */}
                <div className="grid grid-cols-3 gap-2 w-full max-w-xl mt-4">
                    <div className="flex flex-col items-center gap-2 p-3 bg-white/5 rounded-lg border border-white/10 hover:border-primary/30 transition-colors">
                        <span className="material-symbols-outlined text-primary text-2xl">verified_user</span>
                        <span className="text-[10px] uppercase font-bold text-slate-400">Гарантия 2 года</span>
                    </div>
                    <div className="flex flex-col items-center gap-2 p-3 bg-white/5 rounded-lg border border-white/10 hover:border-primary/30 transition-colors">
                        <span className="material-symbols-outlined text-primary text-2xl">payments</span>
                        <span className="text-[10px] uppercase font-bold text-slate-400">Фикс в рублях</span>
                    </div>
                    <div className="flex flex-col items-center gap-2 p-3 bg-white/5 rounded-lg border border-white/10 hover:border-primary/30 transition-colors">
                        <span className="material-symbols-outlined text-primary text-2xl">local_shipping</span>
                        <span className="text-[10px] uppercase font-bold text-slate-400">Срок 25 дней</span>
                    </div>
                </div>
            </div>
        </main>
    );
};

export default Hero;
