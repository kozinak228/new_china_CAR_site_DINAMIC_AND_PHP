import React from 'react';

const Advantages = () => {
    return (
        <section className="bg-background-dark py-20 px-6 border-t border-primary/20 relative z-10">
            <div className="max-w-7xl mx-auto">
                <h2 className="text-white text-3xl font-serif mb-12 text-center">Почему выбирают нас</h2>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div className="flex gap-4 p-6 rounded-xl bg-primary/5 hover:bg-primary/10 border border-primary/10 hover:border-primary/30 transition-all duration-300">
                        <div className="shrink-0 w-14 h-14 flex items-center justify-center rounded-full bg-primary/20 text-primary">
                            <span className="material-symbols-outlined text-2xl">contract</span>
                        </div>
                        <div>
                            <h3 className="text-white font-bold text-lg mb-2">Официальный договор</h3>
                            <p className="text-slate-400 text-sm leading-relaxed">Прописываем финальную стоимость в рублях и точные сроки поставки. Никаких скрытых комиссий и доплат по прибытию.</p>
                        </div>
                    </div>
                    <div className="flex gap-4 p-6 rounded-xl bg-primary/5 hover:bg-primary/10 border border-primary/10 hover:border-primary/30 transition-all duration-300">
                        <div className="shrink-0 w-14 h-14 flex items-center justify-center rounded-full bg-primary/20 text-primary">
                            <span className="material-symbols-outlined text-2xl">diamond</span>
                        </div>
                        <div>
                            <h3 className="text-white font-bold text-lg mb-2">Премиум сервис</h3>
                            <p className="text-slate-400 text-sm leading-relaxed">Персональный менеджер 24/7, детальные фото и видеоотчеты на каждом этапе логистики вашего автомобиля из Китая.</p>
                        </div>
                    </div>
                    <div className="flex gap-4 p-6 rounded-xl bg-primary/5 hover:bg-primary/10 border border-primary/10 hover:border-primary/30 transition-all duration-300">
                        <div className="shrink-0 w-14 h-14 flex items-center justify-center rounded-full bg-primary/20 text-primary">
                            <span className="material-symbols-outlined text-2xl">account_balance</span>
                        </div>
                        <div>
                            <h3 className="text-white font-bold text-lg mb-2">Прозрачная таможня</h3>
                            <p className="text-slate-400 text-sm leading-relaxed">Таможенное оформление через страны ЕАЭС по самым выгодным ставкам с получением активного электронного ПТС.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default Advantages;
