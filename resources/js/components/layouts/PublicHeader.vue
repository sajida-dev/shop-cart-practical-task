<script setup lang="ts">
import { ref, onMounted, reactive, Ref, computed } from 'vue'
import { storeToRefs } from 'pinia'
import { usePage, Link } from '@inertiajs/vue3'
import AppLogo from '@/components/AppLogo.vue'
import { useShopStore } from '@/stores/useShopStore'
import { dashboard, login, logout, register } from '@/routes'
import CartWrapper from '../CartWrapper.vue'

const shop = useShopStore()

const { cart } = storeToRefs(shop)

const showCart = ref(false)
const mobileMenuOpen = ref(false)

defineProps<{ canRegister?: boolean }>()
const page = usePage()
const user = page.props.auth.user
const canRegister = computed(() => page.props.canRegister)

function toggleCart() {
    showCart.value = !showCart.value
}

function updateQuantityHandler(itemId: number, quantity: number) {
    shop.updateCartItemQuantity(itemId, quantity)
}

function removeItemHandler(itemId: number) {
    shop.removeFromCart(itemId)
}

function toggleMobileMenu() {
    mobileMenuOpen.value = !mobileMenuOpen.value
}
onMounted(() => {
    if (cart) cart.value = page.props.shop.cart
})


function goToCheckoutHandler() {
    window.location.href = '/checkout'
}
</script>

<template>
    <header class="fixed top-0 left-0 right-0 z-50 mx-2 md:mx-auto mt-4 max-w-6xl
       rounded-xl bg-white/20 backdrop-blur-md border border-white/30
       px-6 py-3 flex items-center justify-between">
        <!-- Left: Logo + Mobile hamburger -->
        <div class="flex items-center gap-2">
            <button @click="toggleMobileMenu" class="md:hidden p-2 rounded hover:bg-white/30 transition"
                aria-label="Toggle mobile menu">
                <svg v-if="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <Link href="/" class="flex items-center gap-2">
                <AppLogo />
            </Link>
        </div>

        <!-- Center Menu -->
        <nav class="hidden md:flex items-center gap-6 text-white/80 font-medium">
            <Link href="/" class="hover:text-white transition">Home</Link>
            <Link href="/features" class="hover:text-white transition">Features</Link>
            <Link href="/about" class="hover:text-white transition">About</Link>
            <Link href="/contact" class="hover:text-white transition">Contact</Link>
        </nav>

        <!-- Right: Auth + Cart + Wishlist -->
        <div class="flex items-center gap-3 relative">
            <template v-if="user">
                <Link :href="dashboard()"
                    class="rounded-full hidden md:flex bg-primary px-5 py-2 text-sm font-semibold text-white hover:bg-white/30 transition">
                    Dashboard</Link>
                <Link :href="logout()"
                    class="rounded-full bg-primary px-5 py-2 text-sm font-semibold text-white hover:bg-white/30 transition">
                    Logout</Link>
            </template>
            <template v-else>
                <Link :href="login()"
                    class="rounded-full bg-primary px-5 py-2 text-sm font-semibold text-white hover:bg-primary/50 transition">
                    Log in</Link>
                <Link v-if="canRegister" :href="register()"
                    class="rounded-full border border-white/40 px-5 py-2 text-sm font-semibold text-white hover:bg-white/20 transition">
                    Register</Link>
            </template>

            <CartWrapper :shop="shop" :showCart="showCart" @toggle-cart="toggleCart"
                @update-quantity="updateQuantityHandler" @remove-item="removeItemHandler"
                @checkout="goToCheckoutHandler" />
        </div>
    </header>
</template>
