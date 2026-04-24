import React from 'react';
import { Link, useLocation } from 'react-router-dom';
import { 
  ShoppingBagIcon, 
  ChartBarIcon, 
  UsersIcon, 
  CubeIcon,
  HomeIcon 
} from '@heroicons/react/24/outline';

const Sidebar = () => {
  const location = useLocation();

  const navigation = [
    { name: 'Dashboard', href: '/', icon: HomeIcon },
    { name: 'Commandes', href: '/orders', icon: ShoppingBagIcon },
    { name: 'Produits', href: '/products', icon: CubeIcon },
    { name: 'Clients', href: '/customers', icon: UsersIcon },
    { name: 'Statistiques', href: '/statistics', icon: ChartBarIcon },
  ];

  return (
    <div className="w-full bg-gray-900 text-white shadow-lg">
      <div className="flex items-center justify-between px-6 py-4">
        <h1 className="text-xl font-bold text-blue-400">E-Commerce Admin</h1>
        
        <nav className="flex space-x-1">
          {navigation.map((item) => {
            const isActive = location.pathname === item.href;
            return (
              <Link
                key={item.name}
                to={item.href}
                className={`flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 ${
                  isActive
                    ? 'bg-blue-600 text-white shadow-md transform scale-105'
                    : 'text-gray-300 hover:bg-gray-800 hover:text-white hover:shadow-md'
                }`}
              >
                <item.icon className="mr-2 h-5 w-5" />
                <span className="hidden md:inline">{item.name}</span>
              </Link>
            );
          })}
        </nav>
      </div>
    </div>
  );
};

export default Sidebar;
