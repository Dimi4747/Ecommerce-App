import axios from '../bootstrap';

/**
 * API Service for making HTTP requests to Laravel backend
 */
class ApiService {
    /**
     * GET request
     * @param {string} endpoint - API endpoint
     * @param {object} params - Query parameters
     * @returns {Promise}
     */
    async get(endpoint, params = {}) {
        try {
            const response = await axios.get(endpoint, { params });
            return response.data;
        } catch (error) {
            this.handleError(error);
            throw error;
        }
    }

    /**
     * POST request
     * @param {string} endpoint - API endpoint
     * @param {object} data - Request body
     * @returns {Promise}
     */
    async post(endpoint, data = {}) {
        try {
            const response = await axios.post(endpoint, data);
            return response.data;
        } catch (error) {
            this.handleError(error);
            throw error;
        }
    }

    /**
     * PUT request
     * @param {string} endpoint - API endpoint
     * @param {object} data - Request body
     * @returns {Promise}
     */
    async put(endpoint, data = {}) {
        try {
            const response = await axios.put(endpoint, data);
            return response.data;
        } catch (error) {
            this.handleError(error);
            throw error;
        }
    }

    /**
     * PATCH request
     * @param {string} endpoint - API endpoint
     * @param {object} data - Request body
     * @returns {Promise}
     */
    async patch(endpoint, data = {}) {
        try {
            const response = await axios.patch(endpoint, data);
            return response.data;
        } catch (error) {
            this.handleError(error);
            throw error;
        }
    }

    /**
     * DELETE request
     * @param {string} endpoint - API endpoint
     * @returns {Promise}
     */
    async delete(endpoint) {
        try {
            const response = await axios.delete(endpoint);
            return response.data;
        } catch (error) {
            this.handleError(error);
            throw error;
        }
    }

    /**
     * Handle API errors
     * @param {object} error - Error object
     */
    handleError(error) {
        if (error.response) {
            console.error('API Error:', {
                status: error.response.status,
                data: error.response.data,
                headers: error.response.headers,
            });
        } else if (error.request) {
            console.error('Network Error:', error.request);
        } else {
            console.error('Error:', error.message);
        }
    }
}

export default new ApiService();
