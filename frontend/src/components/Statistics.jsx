import React, { useState, useEffect } from 'react';
import {
  LineChart,
  Line,
  BarChart,
  Bar,
  PieChart,
  Pie,
  Cell,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  Legend,
  ResponsiveContainer
} from 'recharts';
import {
  ChartBarIcon,
  CurrencyDollarIcon,
  ShoppingCartIcon,
  UserGroupIcon
} from '@heroicons/react/24/outline';
import axios from 'axios';

const Statistics = () => {
  const [stats, setStats] = useState({
    total_orders: 0,
    pending_orders: 0,
    processing_orders: 0,
    completed_orders: 0,
    total_revenue: 0,
    recent_orders: []
  });
  const [loading, setLoading] = useState(true);
  const [monthlyData, setMonthlyData] = useState([]);
  const [statusData, setStatusData] = useState([]);

  useEffect(() => {
    const fetchStatistics = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/orders/statistics');
        setStats(response.data);
      } catch (error) {
        console.error('Erreur lors de la récupération des statistiques:', error);
        // Utiliser des données mock si l'API échoue
        setStats({
          total_orders: 4,
          pending_orders: 1,
          processing_orders: 1,
          completed_orders: 1,
          total_revenue: 2349.96,
          recent_orders: []
        });
      } finally {
        setLoading(false);
      }
    };

    fetchStatistics();
  }, []);

  useEffect(() => {
    // Données mensuelles pour le graphique de tendance
    const monthlySales = [
      { month: 'Jan', ventes: 12500, commandes: 45 },
      { month: 'Fév', ventes: 15800, commandes: 52 },
      { month: 'Mar', ventes: 18900, commandes: 61 },
      { month: 'Avr', ventes: 22100, commandes: 73 },
      { month: 'Mai', ventes: 25400, commandes: 89 },
      { month: 'Juin', ventes: 28700, commandes: 94 }
    ];
    setMonthlyData(monthlySales);

    // Données de statut pour le graphique circulaire
    const statusDistribution = [
      { name: 'En attente', value: stats.pending_orders || 1, color: '#F59E0B' },
      { name: 'En traitement', value: stats.processing_orders || 1, color: '#3B82F6' },
      { name: 'Livrées', value: stats.completed_orders || 1, color: '#10B981' },
      { name: 'Annulées', value: 0, color: '#EF4444' }
    ];
    setStatusData(statusDistribution);
  }, [stats.pending_orders, stats.processing_orders, stats.completed_orders]);

  const statCards = [
    {
      name: 'Total Commandes',
      value: stats.total_orders,
      icon: ShoppingCartIcon,
      color: 'bg-blue-500',
      change: '+12%',
      changeType: 'positive'
    },
    {
      name: 'Revenu Total',
      value: `${stats.total_revenue.toFixed(2)} €`,
      icon: CurrencyDollarIcon,
      color: 'bg-green-500',
      change: '+8%',
      changeType: 'positive'
    },
    {
      name: 'Commandes Actives',
      value: stats.pending_orders + stats.processing_orders,
      icon: ChartBarIcon,
      color: 'bg-purple-500',
      change: '+5%',
      changeType: 'positive'
    },
    {
      name: 'Taux de Conversion',
      value: '68%',
      icon: UserGroupIcon,
      color: 'bg-orange-500',
      change: '-2%',
      changeType: 'negative'
    }
  ];

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="text-lg">Chargement des statistiques...</div>
      </div>
    );
  }

  return (
    <div className="p-6">
      <h1 className="text-3xl font-bold text-gray-900 mb-6">Statistiques</h1>
      
      {/* Cartes de statistiques */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {statCards.map((stat) => (
          <div key={stat.name} className="bg-white rounded-lg shadow p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600">{stat.name}</p>
                <p className="text-2xl font-semibold text-gray-900">{stat.value}</p>
                <div className={`flex items-center mt-2 text-sm ${
                  stat.changeType === 'positive' ? 'text-green-600' : 'text-red-600'
                }`}>
                  <span>{stat.change}</span>
                </div>
              </div>
              <div className={`${stat.color} rounded-lg p-3`}>
                <stat.icon className="h-6 w-6 text-white" />
              </div>
            </div>
          </div>
        ))}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {/* Graphique de tendance des ventes */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Tendance des Ventes</h2>
          <ResponsiveContainer width="100%" height={300}>
            <LineChart data={monthlyData}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="month" />
              <YAxis />
              <Tooltip />
              <Legend />
              <Line 
                type="monotone" 
                dataKey="ventes" 
                stroke="#3B82F6" 
                strokeWidth={2}
                name="Ventes (€)"
              />
              <Line 
                type="monotone" 
                dataKey="commandes" 
                stroke="#10B981" 
                strokeWidth={2}
                name="Commandes"
              />
            </LineChart>
          </ResponsiveContainer>
        </div>

        {/* Graphique de distribution des statuts */}
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Distribution des Commandes</h2>
          <ResponsiveContainer width="100%" height={300}>
            <PieChart>
              <Pie
                data={statusData}
                cx="50%"
                cy="50%"
                labelLine={false}
                label={({ name, value }) => `${name}: ${value}`}
                outerRadius={80}
                fill="#8884d8"
                dataKey="value"
              >
                {statusData.map((entry, index) => (
                  <Cell key={`cell-${index}`} fill={entry.color} />
                ))}
              </Pie>
              <Tooltip />
            </PieChart>
          </ResponsiveContainer>
        </div>
      </div>

      {/* Graphique des ventes mensuelles */}
      <div className="bg-white rounded-lg shadow p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Ventes Mensuelles</h2>
        <ResponsiveContainer width="100%" height={400}>
          <BarChart data={monthlyData}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="month" />
            <YAxis />
            <Tooltip />
            <Legend />
            <Bar dataKey="ventes" fill="#3B82F6" name="Ventes (€)" />
            <Bar dataKey="commandes" fill="#10B981" name="Commandes" />
          </BarChart>
        </ResponsiveContainer>
      </div>

      {/* Tableau récapitulatif */}
      <div className="bg-white rounded-lg shadow p-6 mt-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Récapitulatif Mensuel</h2>
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                  Mois
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                  Ventes
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                  Commandes
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                  Panier Moyen
                </th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {monthlyData.map((row, index) => (
                <tr key={index} className="hover:bg-gray-50">
                  <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {row.month}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {row.ventes.toLocaleString()} €
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {row.commandes}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {(row.ventes / row.commandes).toFixed(2)} €
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};

export default Statistics;
