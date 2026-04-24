import { describe, it, expect, vi, beforeEach } from 'vitest';
import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import BuyButton from '../resources/js/Components/BuyButton';
import * as useApiModule from '../resources/js/hooks/useApi';

describe('BuyButton Component', () => {
    let mockPost;

    beforeEach(() => {
        mockPost = vi.fn();
        vi.spyOn(useApiModule, 'useApi').mockReturnValue({
            post: mockPost,
            loading: false,
            error: null,
        });
    });

    it('renders buy button with correct text', () => {
        render(<BuyButton productId={1} price={29.99} />);
        
        const button = screen.getByTestId('buy-button');
        expect(button).toBeInTheDocument();
        expect(button).toHaveTextContent('Buy Now');
    });

    it('calls API when button is clicked', async () => {
        mockPost.mockResolvedValue({
            success: true,
            order: { id: 1234 },
        });

        render(<BuyButton productId={1} price={29.99} quantity={2} />);
        
        const button = screen.getByTestId('buy-button');
        fireEvent.click(button);

        await waitFor(() => {
            expect(mockPost).toHaveBeenCalledWith('/api/orders/purchase', {
                product_id: 1,
                quantity: 2,
                price: 29.99,
            });
        });
    });

    it('shows processing state while purchasing', async () => {
        mockPost.mockImplementation(() => new Promise(resolve => setTimeout(resolve, 100)));

        render(<BuyButton productId={1} price={29.99} />);
        
        const button = screen.getByTestId('buy-button');
        fireEvent.click(button);

        expect(button).toHaveTextContent('Processing...');
        expect(button).toBeDisabled();
    });

    it('calls onSuccess callback when purchase succeeds', async () => {
        const onSuccess = vi.fn();
        const mockResponse = { success: true, order: { id: 1234 } };
        mockPost.mockResolvedValue(mockResponse);

        render(<BuyButton productId={1} price={29.99} onSuccess={onSuccess} />);
        
        const button = screen.getByTestId('buy-button');
        fireEvent.click(button);

        await waitFor(() => {
            expect(onSuccess).toHaveBeenCalledWith(mockResponse);
        });
    });

    it('calls onError callback when purchase fails', async () => {
        const onError = vi.fn();
        const mockError = new Error('Purchase failed');
        mockPost.mockRejectedValue(mockError);

        render(<BuyButton productId={1} price={29.99} onError={onError} />);
        
        const button = screen.getByTestId('buy-button');
        fireEvent.click(button);

        await waitFor(() => {
            expect(onError).toHaveBeenCalledWith(mockError);
        });
    });

    it('re-enables button after purchase completes', async () => {
        mockPost.mockResolvedValue({ success: true });

        render(<BuyButton productId={1} price={29.99} />);
        
        const button = screen.getByTestId('buy-button');
        fireEvent.click(button);

        await waitFor(() => {
            expect(button).not.toBeDisabled();
            expect(button).toHaveTextContent('Buy Now');
        });
    });

    it('handles multiple rapid clicks correctly', async () => {
        mockPost.mockResolvedValue({ success: true });

        render(<BuyButton productId={1} price={29.99} />);
        
        const button = screen.getByTestId('buy-button');
        
        fireEvent.click(button);
        fireEvent.click(button);
        fireEvent.click(button);

        await waitFor(() => {
            expect(mockPost).toHaveBeenCalledTimes(1);
        });
    });
});
