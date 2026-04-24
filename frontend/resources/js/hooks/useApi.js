import { useState, useCallback } from 'react';
import api from '../services/api';

/**
 * Custom hook for API calls with loading and error states
 * @returns {object} - API methods and states
 */
export const useApi = () => {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    const request = useCallback(async (method, endpoint, data = null) => {
        setLoading(true);
        setError(null);

        try {
            let response;
            switch (method.toLowerCase()) {
                case 'get':
                    response = await api.get(endpoint, data);
                    break;
                case 'post':
                    response = await api.post(endpoint, data);
                    break;
                case 'put':
                    response = await api.put(endpoint, data);
                    break;
                case 'patch':
                    response = await api.patch(endpoint, data);
                    break;
                case 'delete':
                    response = await api.delete(endpoint);
                    break;
                default:
                    throw new Error(`Unsupported method: ${method}`);
            }
            return response;
        } catch (err) {
            setError(err.response?.data?.message || err.message);
            throw err;
        } finally {
            setLoading(false);
        }
    }, []);

    const get = useCallback((endpoint, params) => request('get', endpoint, params), [request]);
    const post = useCallback((endpoint, data) => request('post', endpoint, data), [request]);
    const put = useCallback((endpoint, data) => request('put', endpoint, data), [request]);
    const patch = useCallback((endpoint, data) => request('patch', endpoint, data), [request]);
    const del = useCallback((endpoint) => request('delete', endpoint), [request]);

    return {
        loading,
        error,
        get,
        post,
        put,
        patch,
        delete: del,
    };
};
