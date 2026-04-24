import React, { useState, useEffect } from 'react';
import { 
  ShoppingBagIcon, 
  CurrencyDollarIcon, 
  UserGroupIcon, 
  ClockIcon,
  ArrowTrendingUpIcon,
  CubeIcon,
  ChartBarIcon
} from '@heroicons/react/24/outline';
import apiClient, { API_ENDPOINTS } from '../config/api';

const Dashboard = () => {
  const [stats, setStats] = useState({
    total_orders: 0,
    pending_orders: 0,
    processing_orders: 0,
    completed_orders: 0,
    total_revenue: 0,
    total_customers: 0,
    total_products: 0,
    recent_orders: [],
    monthly_revenue: [],
    top_products: []
  });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchStatistics = async () => {
      try {
        const response = await apiClient.get(API_ENDPOINTS.orders.statistics);
        setStats(response.data);
      } catch (error) {
        console.error('Erreur lors de la récupération des statistiques:', error);
        // Données par défaut si l'API échoue
        setStats({
          total_orders: 0,
          pending_orders: 0,
          processing_orders: 0,
          completed_orders: 0,
          total_revenue: 0,
          total_customers: 0,
          total_products: 0,
          recent_orders: [],
          monthly_revenue: [],
          top_products: []
        });
      } finally {
        setLoading(false);
      }
    };

    fetchStatistics();
  }, []);

  const statCards = [
    {
      name: 'Total Commandes',
      value: stats.total_orders,
      icon: ShoppingBagIcon,
      color: 'bg-blue-500',
      trend: '+12%',
      trendColor: 'text-green-600'
    },
    {
      name: 'Revenu Total',
      value: `${typeof stats.total_revenue === 'number' ? stats.total_revenue.toFixed(2) : '0.00'} €`,
      icon: CurrencyDollarIcon,
      color: 'bg-green-500',
      trend: '+8%',
      trendColor: 'text-green-600'
    },
    {
      name: 'Total Clients',
      value: stats.total_customers,
      icon: UserGroupIcon,
      color: 'bg-purple-500',
      trend: '+15%',
      trendColor: 'text-green-600'
    },
    {
      name: 'Total Produits',
      value: stats.total_products,
      icon: CubeIcon,
      color: 'bg-orange-500',
      trend: '+5%',
      trendColor: 'text-green-600'
    }
  ];

  const orderStatusCards = [
    {
      name: 'En Attente',
      value: stats.pending_orders,
      icon: ClockIcon,
      color: 'bg-yellow-500'
    },
    {
      name: 'En Traitement',
      value: stats.processing_orders,
      icon: ArrowTrendingUpIcon,
      color: 'bg-blue-500'
    },
    {
      name: 'Livrées',
      value: stats.completed_orders,
      icon: ShoppingBagIcon,
      color: 'bg-green-500'
    }
  ];

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="text-lg">Chargement...</div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <div className="bg-white shadow-sm border-b border-gray-200">
        <div className="px-6 py-4">
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-2xl font-bold text-gray-900">Tableau de Bord</h1>
              <p className="text-sm text-gray-600 mt-1">Vue d'ensemble de votre activité commerciale</p>
            </div>
            <div className="flex items-center space-x-3">
              <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Exporter
              </button>
              <button className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                Actualiser
              </button>
            </div>
          </div>
        </div>
      </div>

      <div className="p-6">
        {/* KPI Overview */}
        <div className="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
          {statCards.map((stat) => (
            <div key={stat.name} className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-200">
              <div className="p-6">
                <div className="flex items-center justify-between mb-4">
                  <div className={`${stat.color} rounded-lg p-3`}>
                    <stat.icon className="h-6 w-6 text-white" />
                  </div>
                  {stat.trend && (
                    <div className={`flex items-center text-sm font-medium ${stat.trendColor} bg-green-50 px-2 py-1 rounded-full`}>
                      <ArrowTrendingUpIcon className="h-3 w-3 mr-1" />
                      {stat.trend}
                    </div>
                  )}
                </div>
                <div>
                  <p className="text-sm font-medium text-gray-600 mb-1">{stat.name}</p>
                  <p className="text-3xl font-bold text-gray-900">{stat.value}</p>
                </div>
              </div>
              <div className="h-1 bg-gradient-to-r from-transparent via-gray-100 to-transparent"></div>
            </div>
          ))}
        </div>

        {/* Main Content Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Order Status Overview */}
          <div className="lg:col-span-2">
            <div className="bg-white rounded-xl shadow-sm border border-gray-200">
              <div className="p-6 border-b border-gray-200">
                <div className="flex items-center justify-between">
                  <h2 className="text-lg font-semibold text-gray-900">Vue d'ensemble des commandes</h2>
                  <span className="text-sm text-gray-500">Derniers 30 jours</span>
                </div>
              </div>
              <div className="p-6">
                <div className="grid grid-cols-3 gap-4">
                  {orderStatusCards.map((stat) => (
                    <div key={stat.name} className="text-center p-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                      <div className={`${stat.color} rounded-lg p-3 mx-auto mb-3 w-fit`}>
                        <stat.icon className="h-5 w-5 text-white" />
                      </div>
                      <p className="text-2xl font-bold text-gray-900">{stat.value}</p>
                      <p className="text-sm text-gray-600 mt-1">{stat.name}</p>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>

          {/* Quick Actions */}
          <div className="lg:col-span-1">
            <div className="bg-white rounded-xl shadow-sm border border-gray-200">
              <div className="p-6 border-b border-gray-200">
                <h2 className="text-lg font-semibold text-gray-900">Actions Rapides</h2>
              </div>
              <div className="p-6 space-y-3">
                <button className="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                  <ShoppingBagIcon className="h-5 w-5 mr-2" />
                  Nouvelle Commande
                </button>
                <button className="w-full px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center justify-center">
                  <UserGroupIcon className="h-5 w-5 mr-2" />
                  Ajouter un Client
                </button>
                <button className="w-full px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center justify-center">
                  <CubeIcon className="h-5 w-5 mr-2" />
                  Nouveau Produit
                </button>
              </div>
            </div>
          </div>
        </div>

        {/* Recent Orders Table */}
        <div className="mt-6">
          <div className="bg-white rounded-xl shadow-sm border border-gray-200">
            <div className="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
              <div>
                <h2 className="text-lg font-semibold text-gray-900">Commandes Récentes</h2>
                <p className="text-sm text-gray-500 mt-1">Les dernières commandes passées</p>
              </div>
              <button className="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center">
                Voir toutes
                <ChartBarIcon className="h-4 w-4 ml-1" />
              </button>
            </div>
            <div className="overflow-x-auto">
              <table className="min-w-full">
                <thead className="bg-gray-50 border-b border-gray-200">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Commande
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Client
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Articles
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Montant
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Statut
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Date
                    </th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-100">
                  {stats.recent_orders.length > 0 ? (
                    stats.recent_orders.map((order) => (
                      <tr key={order.id} className="hover:bg-gray-50 transition-colors">
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm font-medium text-gray-900">{order.order_number}</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm text-gray-900">
                            {order.customer?.full_name || `${order.customer?.first_name} ${order.customer?.last_name}`}
                          </div>
                          <div className="text-xs text-gray-500">{order.customer?.email}</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm text-gray-900">3 articles</div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm font-medium text-gray-900">
                            {typeof order.total_amount === 'number' ? order.total_amount.toFixed(2) : '0.00'} €
                          </div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <span className={`px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${
                            order.status === 'delivered' ? 'bg-green-100 text-green-800' :
                            order.status === 'processing' ? 'bg-blue-100 text-blue-800' :
                            order.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                            'bg-red-100 text-red-800'
                          }`}>
                            {order.status_label}
                          </span>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {new Date(order.created_at).toLocaleDateString('fr-FR', { 
                            day: 'numeric', 
                            month: 'short', 
                            year: 'numeric' 
                          })}
                        </td>
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td colSpan="6" className="px-6 py-12 text-center">
                        <div className="flex flex-col items-center">
                          <ShoppingBagIcon className="h-12 w-12 text-gray-300 mb-3" />
                          <p className="text-sm text-gray-500">Aucune commande récente</p>
                        </div>
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;
