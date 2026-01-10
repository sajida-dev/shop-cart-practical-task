import { defineStore } from 'pinia'
import { router } from '@inertiajs/vue3'
import { CartItem, Product } from '@/product'
import { toast } from 'vue3-toastify'


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
            let existing = this.cart.find(i => i.productId === product.id)

            if (existing) {
                existing.quantity++
            } else {
                existing = {
                    id: Date.now() * -1,
                    productId: product.id,
                    name: product.name,
                    price: product.price,
                    quantity: 1,
                }
                this.cart.push(existing)
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
                        onSuccess: (page) => {
                            const shopCart = (page.props as any).shop.cart as any[]
                            const backendItem = shopCart.find(i => i.productId === product.id)
                            if (backendItem?.id) {
                                existing.id = backendItem.id
                                existing.quantity = backendItem.quantity
                            }
                            // toast.success('Product added to cart')
                        },
                        onError: () => {
                            // Rollback
                            if (existing.quantity > 1) {
                                existing.quantity--
                            } else {
                                this.cart = this.cart.filter(i => i.id !== existing.id)
                            }
                            toast.error('Product could not be added to cart')
                        },
                    }
                )
            } catch (error) {
                console.error('Add to cart failed', error)
                if (existing.quantity > 1) {
                    existing.quantity--
                } else {
                    this.cart = this.cart.filter(i => i.id !== existing.id)
                }
                toast.error('Product could not be added to cart')
            }
        },

        /* 
           REMOVE FROM CART
         */
        async removeFromCart(productId: number) {
            console.log('removeFromCart', productId)
            const backup = [...this.cart]

            this.cart = this.cart.filter(i => i.id !== productId)

            try {
                await router.delete(route('cart.destroy', productId), {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: () => {
                        toast.success('Product removed from cart')
                    },
                    onError: () => {
                        this.cart = backup
                        toast.error('Product could not be removed from cart')
                    },
                })
            } catch (error) {
                console.error('Remove from cart failed', error)
                this.cart = backup
                toast.error('Product could not be removed from cart')
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
                        onSuccess: () => {
                            toast.success('Update quantity successful')
                        },
                        onError: () => {
                            item.quantity = oldQuantity
                            toast.error('Update quantity failed')
                        },
                    }
                )
            } catch (error) {
                console.error('Update quantity failed', error)
                item.quantity = oldQuantity
                toast.error('Update quantity failed')
            }
        },


        setCartFromServer(cartItems: CartItem[]) {
            this.cart = cartItems
        },
    },
})
