import { useState } from 'react';
import { useApi } from '../hooks/useApi';
import PrimaryButton from './PrimaryButton';

export default function BuyButton({ productId, price, quantity = 1, onSuccess, onError }) {
    const [isPurchasing, setIsPurchasing] = useState(false);
    const { post } = useApi();

    const handlePurchase = async () => {
        setIsPurchasing(true);

        try {
            const response = await post('/api/orders/purchase', {
                product_id: productId,
                quantity: quantity,
                price: price,
            });

            if (onSuccess) {
                onSuccess(response);
            }
        } catch (error) {
            console.error('Purchase failed:', error);
            if (onError) {
                onError(error);
            }
        } finally {
            setIsPurchasing(false);
        }
    };

    return (
        <PrimaryButton
            onClick={handlePurchase}
            disabled={isPurchasing}
            className="buy-button"
            data-testid="buy-button"
        >
            {isPurchasing ? 'Processing...' : 'Buy Now'}
        </PrimaryButton>
    );
}
