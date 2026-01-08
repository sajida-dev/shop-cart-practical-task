<template>
    <div class="relative flex items-center gap-3">

        <!-- CART ICON -->
        <button ref="cartButton" @click="toggleCart" aria-label="Toggle Cart"
            class="relative px-2 py-1 rounded-full hover:bg-white/20 transition">
            <ShoppingCartIcon class="w-6 h-6" />
            <span v-if="shop.cartCount" class="absolute -top-2 -right-1 bg-primary text-white text-xs
        rounded-full min-w-5 h-5 px-1 flex items-center justify-center">
                {{ shop.cartCount }}
            </span>
        </button>

        <!-- CART POPUP -->
        <transition name="fade">
            <div ref="cartPopup" v-if="showCart" class="absolute right-0 top-full mt-3 w-80
        rounded-xl bg-neutral-900/95 backdrop-blur border border-neutral-700 shadow-2xl z-50">

                <div class="p-4 border-b border-neutral-700 text-white font-semibold">
                    Your Cart
                </div>

                <div class="max-h-72 overflow-y-auto p-4 space-y-3">
                    <p v-if="!shop.cart.length" class="text-neutral-400 text-sm">Cart is empty</p>

                    <div v-for="item in shop.cart" :key="item.id" class="flex items-center gap-3">
                        <div class="flex-1">
                            <p class="text-white text-sm font-medium">{{ item.name }}</p>
                            <p class="text-primary text-sm">Rs {{ item.price }}</p>
                            <div class="flex items-center space-x-2 mt-1">
                                <button @click="decreaseQty(item)"
                                    class="w-6 h-6 rounded border border-neutral-600 text-white flex items-center justify-center hover:bg-white/10 transition">−</button>
                                <span class="text-white text-sm">{{ item.quantity }}</span>
                                <button @click="increaseQty(item)"
                                    class="w-6 h-6 rounded border border-neutral-600 text-white flex items-center justify-center hover:bg-white/10 transition">+</button>
                            </div>
                        </div>
                        <button @click="removeItem(item)" aria-label="Remove item"
                            class="text-white hover:text-red-500 transition">
                            <TrashIcon class="w-6 h-6" />
                        </button>
                    </div>
                </div>

                <div class="p-4 border-t border-neutral-700 space-y-2">
                    <div class="flex justify-between text-white font-semibold">
                        <span>Total:</span>
                        <span>Rs {{ cartTotal }}</span>
                    </div>

                    <button @click="goToCheckout"
                        class="w-full bg-primary text-white py-2 rounded-lg font-semibold hover:bg-primary/80 transition">
                        Checkout
                    </button>
                </div>
            </div>
        </transition>

    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { ShoppingCartIcon, TrashIcon } from 'lucide-vue-next'
import { useShopStore } from '@/stores/useShopStore'

/*  STORE  */
const shop = useShopStore()

/*  LOCAL STATE */
const showCart = ref(false)
const cartButton = ref<HTMLElement | null>(null)
const cartPopup = ref<HTMLElement | null>(null)

/*  COMPUTED  */
const cartTotal = computed(() =>
    shop.cart.reduce((total, item) => total + item.price * item.quantity, 0)
)

/*  METHODS */
const toggleCart = () => {
    showCart.value = !showCart.value
}

const increaseQty = (item: any) => {
    shop.updateCartItemQuantity(item.id, item.quantity + 1)
}

const decreaseQty = (item: any) => {
    if (item.quantity > 1) {
        shop.updateCartItemQuantity(item.id, item.quantity - 1)
    } else {
        removeItem(item)
    }
}

const removeItem = (item: any) => {
    shop.removeFromCart(item.id)
}

const goToCheckout = () => {
    // redirect to checkout page
    window.location.href = '/checkout'
}

/*  CLICK OUTSIDE */
const handleClickOutside = (event: MouseEvent) => {
    const target = event.target as Node
    if (
        showCart.value &&
        cartPopup.value &&
        !cartPopup.value.contains(target) &&
        cartButton.value &&
        !cartButton.value.contains(target)
    ) {
        showCart.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>
