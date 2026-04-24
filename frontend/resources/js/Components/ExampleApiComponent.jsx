import { useState, useEffect } from 'react';
import { useApi } from '../hooks/useApi';

/**
 * Example component demonstrating API usage
 */
export default function ExampleApiComponent() {
    const [data, setData] = useState(null);
    const { loading, error, get, post } = useApi();

    // Example: Fetch data on component mount
    useEffect(() => {
        fetchData();
    }, []);

    const fetchData = async () => {
        try {
            const response = await get('/api/example');
            setData(response);
        } catch (err) {
            console.error('Failed to fetch data:', err);
        }
    };

    const handleSubmit = async (formData) => {
        try {
            const response = await post('/api/example', formData);
            console.log('Success:', response);
            // Refresh data after successful post
            fetchData();
        } catch (err) {
            console.error('Failed to submit:', err);
        }
    };

    if (loading) {
        return <div className="text-center py-4">Loading...</div>;
    }

    if (error) {
        return (
            <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                Error: {error}
            </div>
        );
    }

    return (
        <div className="p-4">
            <h2 className="text-xl font-bold mb-4">API Example</h2>
            {data && (
                <pre className="bg-gray-100 p-4 rounded">
                    {JSON.stringify(data, null, 2)}
                </pre>
            )}
        </div>
    );
}
