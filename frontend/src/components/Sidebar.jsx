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
    <div className="w-64 bg-gray-900 text-white">
      <div className="p-6">
        <h1 className="text-2xl font-bold text-blue-400">E-Commerce Admin</h1>
      </div>
      
      <nav className="mt-6">
        <div className="px-4 space-y-2">
          {navigation.map((item) => {
            const isActive = location.pathname === item.href;
            return (
              <Link
                key={item.name}
                to={item.href}
                className={`flex items-center px-4 py-3 text-sm font-medium rounded-md transition-colors ${
                  isActive
                    ? 'bg-blue-600 text-white'
                    : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                }`}
              >
                <item.icon className="mr-3 h-5 w-5" />
                {item.name}
              </Link>
            );
          })}
        </div>
      </nav>
    </div>
  );
};

export default Sidebar;
