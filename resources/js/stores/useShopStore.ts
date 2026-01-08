import { defineStore } from 'pinia'
import { router } from '@inertiajs/vue3'
import { CartItem, Product } from '@/product'


export interface ShopState {
    cart: CartItem[]
}

export const useShopStore = defineStore('shop', {
    state: (): ShopState => ({
        cart: [],
    }),

    getters: {
        cartCount: (state) =>
            state.cart.reduce((sum, item) => sum + item.quantity, 0),

        cartTotal: (state) =>
            state.cart.reduce((sum, item) => sum + item.price * item.quantity, 0),
    },

    actions: {
        /* 
           ADD TO CART
         */
        async addToCart(product: Product) {
            const existing = this.cart.find(i => i.id === product.id)

            if (existing) {
                existing.quantity++
            } else {
                this.cart.push({
                    id: Date.now() * -1,
                    productId: product.id,
                    name: product.name,
                    price: product.price,
                    quantity: 1,
                })
            }

            try {
                await router.post(
                    route('cart.store'),
                    {
                        product_id: product.id,
                        quantity: 1,
                    },
                    {
                        preserveState: true,
                        preserveScroll: true,
                        onError: () => {
                            // Rollback
                            if (existing) {
                                existing.quantity--
                            } else {
                                this.cart = this.cart.filter(i => i.id !== product.id)
                            }
                        },
                    }
                )
            } catch (error) {
                console.error('Add to cart failed', error)
            }
        },

        /* 
           REMOVE FROM CART
         */
        async removeFromCart(productId: number) {
            const backup = [...this.cart]

            this.cart = this.cart.filter(i => i.id !== productId)

            try {
                await router.delete(route('cart.destroy', productId), {
                    preserveState: true,
                    preserveScroll: true,
                    onError: () => {
                        this.cart = backup
                    },
                })
            } catch (error) {
                console.error('Remove from cart failed', error)
                this.cart = backup
            }
        },

        /* 
           UPDATE QUANTITY
         */
        async updateCartItemQuantity(productId: number, quantity: number) {
            const item = this.cart.find(i => i.id === productId)
            if (!item) return

            const oldQuantity = item.quantity

            if (quantity < 1) {
                await this.removeFromCart(productId)
                return
            }

            item.quantity = quantity

            try {
                await router.put(
                    route('cart.updateQty', { itemId: productId }),
                    { quantity },
                    {
                        preserveState: true,
                        preserveScroll: true,
                        onError: () => {
                            item.quantity = oldQuantity
                        },
                    }
                )
            } catch (error) {
                console.error('Update quantity failed', error)
                item.quantity = oldQuantity
            }
        },


        setCartFromServer(cartItems: CartItem[]) {
            this.cart = cartItems
        },
    },
})
