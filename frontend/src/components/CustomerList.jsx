import React, { useState, useEffect } from 'react';
import { 
  PlusIcon, 
  PencilIcon, 
  TrashIcon, 
  MagnifyingGlassIcon,
  UserIcon,
  EnvelopeIcon,
  PhoneIcon,
  MapPinIcon
} from '@heroicons/react/24/outline';
import axios from 'axios';

const CustomerList = () => {
  const [customers, setCustomers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  const [currentPage] = useState(1);

  useEffect(() => {
    const fetchCustomers = async () => {
      try {
        const response = await axios.get(`http://localhost:8000/api/customers?page=${currentPage}`);
        setCustomers(response.data.data || response.data);
      } catch (error) {
        console.error('Erreur lors de la récupération des clients:', error);
        // Données mock si l'API échoue
        setCustomers([
          {
            id: 1,
            first_name: 'Jean',
            last_name: 'Dupont',
            email: 'jean.dupont@email.com',
            phone: '06 12 34 56 78',
            address: '123 Rue de la République, 75001 Paris',
            city: 'Paris',
            postal_code: '75001',
            country: 'France',
            created_at: '2024-01-15T10:30:00Z',
            total_orders: 5,
            total_spent: 2349.96
          },
          {
            id: 2,
            first_name: 'Marie',
            last_name: 'Martin',
            email: 'marie.martin@email.com',
            phone: '06 23 45 67 89',
            address: '456 Avenue des Champs-Élysées, 75008 Paris',
            city: 'Paris',
            postal_code: '75008',
            country: 'France',
            created_at: '2024-02-20T14:15:00Z',
            total_orders: 3,
            total_spent: 1864.98
          },
          {
            id: 3,
            first_name: 'Pierre',
            last_name: 'Bernard',
            email: 'pierre.bernard@email.com',
            phone: '06 34 56 78 90',
            address: '789 Boulevard Saint-Germain, 69001 Lyon',
            city: 'Lyon',
            postal_code: '69001',
            country: 'France',
            created_at: '2024-03-10T09:45:00Z',
            total_orders: 2,
            total_spent: 784.99
          },
          {
            id: 4,
            first_name: 'Sophie',
            last_name: 'Petit',
            email: 'sophie.petit@email.com',
            phone: '06 45 67 89 01',
            address: '321 Rue de la Paix, 44000 Nantes',
            city: 'Nantes',
            postal_code: '44000',
            country: 'France',
            created_at: '2024-04-05T16:20:00Z',
            total_orders: 1,
            total_spent: 244.99
          },
          {
            id: 5,
            first_name: 'Thomas',
            last_name: 'Robert',
            email: 'thomas.robert@email.com',
            phone: '06 56 78 90 12',
            address: '654 Place de la Concorde, 13001 Marseille',
            city: 'Marseille',
            postal_code: '13001',
            country: 'France',
            created_at: '2024-04-18T11:30:00Z',
            total_orders: 4,
            total_spent: 1549.96
          }
        ]);
      } finally {
        setLoading(false);
      }
    };

    fetchCustomers();
  }, [currentPage]);

  const filteredCustomers = (customers || []).filter(customer =>
    `${customer.first_name} ${customer.last_name}`.toLowerCase().includes(searchTerm.toLowerCase()) ||
    customer.email.toLowerCase().includes(searchTerm.toLowerCase()) ||
    customer.city.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const getCustomerType = (totalOrders) => {
    if (totalOrders >= 5) return { label: 'VIP', color: 'bg-purple-100 text-purple-800' };
    if (totalOrders >= 3) return { label: 'Fidèle', color: 'bg-blue-100 text-blue-800' };
    if (totalOrders >= 1) return { label: 'Nouveau', color: 'bg-green-100 text-green-800' };
    return { label: 'Inactif', color: 'bg-gray-100 text-gray-800' };
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="text-lg">Chargement des clients...</div>
      </div>
    );
  }

  return (
    <div className="p-6">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-3xl font-bold text-gray-900">Gestion des Clients</h1>
        <button className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
          <PlusIcon className="h-5 w-5 mr-2" />
          Ajouter un client
        </button>
      </div>

      {/* Statistiques des clients */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center">
            <div className="bg-blue-500 rounded-lg p-3 mr-4">
              <UserIcon className="h-6 w-6 text-white" />
            </div>
            <div>
              <p className="text-sm font-medium text-gray-600">Total Clients</p>
              <p className="text-2xl font-semibold text-gray-900">{customers.length}</p>
            </div>
          </div>
        </div>
        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center">
            <div className="bg-purple-500 rounded-lg p-3 mr-4">
              <UserIcon className="h-6 w-6 text-white" />
            </div>
            <div>
              <p className="text-sm font-medium text-gray-600">Clients VIP</p>
              <p className="text-2xl font-semibold text-gray-900">
                {customers.filter(c => c.total_orders >= 5).length}
              </p>
            </div>
          </div>
        </div>
        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center">
            <div className="bg-green-500 rounded-lg p-3 mr-4">
              <UserIcon className="h-6 w-6 text-white" />
            </div>
            <div>
              <p className="text-sm font-medium text-gray-600">Nouveaux</p>
              <p className="text-2xl font-semibold text-gray-900">
                {customers.filter(c => c.total_orders === 1).length}
              </p>
            </div>
          </div>
        </div>
        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center">
            <div className="bg-orange-500 rounded-lg p-3 mr-4">
              <UserIcon className="h-6 w-6 text-white" />
            </div>
            <div>
              <p className="text-sm font-medium text-gray-600">Panier Moyen</p>
              <p className="text-2xl font-semibold text-gray-900">
                {customers.length > 0 
                  ? `${(customers.reduce((sum, c) => sum + (c.total_spent / c.total_orders || 0), 0) / customers.length).toFixed(0)} €`
                  : '0 €'
                }
              </p>
            </div>
          </div>
        </div>
      </div>

      {/* Barre de recherche */}
      <div className="bg-white rounded-lg shadow p-4 mb-6">
        <div className="relative">
          <MagnifyingGlassIcon className="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
          <input
            type="text"
            placeholder="Rechercher un client..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>
      </div>

      {/* Tableau des clients */}
      <div className="bg-white rounded-lg shadow">
        <div className="px-6 py-4 border-b border-gray-200">
          <h2 className="text-lg font-semibold text-gray-900">Liste des Clients</h2>
        </div>
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Client
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Contact
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Adresse
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Commandes
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Total Dépensé
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Type
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {filteredCustomers.map((customer) => {
                const customerType = getCustomerType(customer.total_orders);
                return (
                  <tr key={customer.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="flex items-center">
                        <div className="h-10 w-10 flex-shrink-0">
                          <div className="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <UserIcon className="h-6 w-6 text-gray-500" />
                          </div>
                        </div>
                        <div className="ml-4">
                          <div className="text-sm font-medium text-gray-900">
                            {customer.first_name} {customer.last_name}
                          </div>
                          <div className="text-sm text-gray-500">
                            Client depuis {new Date(customer.created_at).toLocaleDateString('fr-FR')}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="text-sm text-gray-900 flex items-center">
                        <EnvelopeIcon className="h-4 w-4 mr-1 text-gray-400" />
                        {customer.email}
                      </div>
                      <div className="text-sm text-gray-500 flex items-center">
                        <PhoneIcon className="h-4 w-4 mr-1 text-gray-400" />
                        {customer.phone}
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="text-sm text-gray-900 flex items-center">
                        <MapPinIcon className="h-4 w-4 mr-1 text-gray-400" />
                        <div>
                          <div>{customer.address}</div>
                          <div>{customer.postal_code} {customer.city}</div>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {customer.total_orders}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {typeof customer.total_spent === 'number' ? customer.total_spent.toFixed(2) : '0.00'} €
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${customerType.color}`}>
                        {customerType.label}
                      </span>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <div className="flex space-x-2">
                        <button className="text-blue-600 hover:text-blue-900">
                          <PencilIcon className="h-5 w-5" />
                        </button>
                        <button className="text-red-600 hover:text-red-900">
                          <TrashIcon className="h-5 w-5" />
                        </button>
                      </div>
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};

export default CustomerList;
