import '@testing-library/jest-dom';
import { expect, afterEach } from 'vitest';
import { cleanup } from '@testing-library/react';

// Cleanup after each test
afterEach(() => {
    cleanup();
});

// Mock window.axios
global.axios = {
    defaults: {
        headers: {
            common: {},
        },
        withCredentials: true,
        withXSRFToken: true,
        baseURL: 'http://localhost:8000',
    },
    interceptors: {
        request: { use: () => {} },
        response: { use: () => {} },
    },
    get: () => Promise.resolve({ data: {} }),
    post: () => Promise.resolve({ data: {} }),
    put: () => Promise.resolve({ data: {} }),
    delete: () => Promise.resolve({ data: {} }),
};
