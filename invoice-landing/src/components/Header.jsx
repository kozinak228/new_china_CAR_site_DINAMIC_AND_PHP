import React from 'react';

const Header = () => {
    return (
        <nav className="fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-6 py-4 bg-background-dark/80 backdrop-blur-md border-b border-primary/10">
            <div className="flex items-center gap-2">
                <div className="text-primary">
                    <span className="material-symbols-outlined text-3xl">electric_car</span>
                </div>
                <span className="text-white text-lg font-bold tracking-tight uppercase">INVOICE <span className="text-primary">Auto</span></span>
            </div>
            <div className="flex items-center gap-4">
                <button className="text-white p-2">
                    <span className="material-symbols-outlined">search</span>
                </button>
                <button className="text-primary p-2">
                    <span className="material-symbols-outlined text-3xl">menu</span>
                </button>
            </div>
        </nav>
    );
};

export default Header;
