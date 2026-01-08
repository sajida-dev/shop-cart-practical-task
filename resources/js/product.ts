export interface Product {
    id: number;
    name: string;
    price: number;
    stock_quantity: number;
    inStock: number;
}

export interface CartItem {
    id: number
    name: string
    price: number
    quantity: number
}

export function mapProducts(raw: any[]): Product[] {
    return raw.map(p => ({
        id: p.id,
        name: p.name ?? '',
        price: p.price ?? 0,
        stock_quantity: p.stock_quantity ?? 0,
        inStock: p.inStock ?? 0,
    }))
}

export function mapCartItems(raw: any[]): CartItem[] {
    return raw.map(c => ({
        id: c.id,
        productId: c.product.id ?? 0,
        name: c.product.name ?? '',
        price: c.product.price ?? 0,
        quantity: c.quantity ?? 1,
    }))
}
