<template>
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-4xl lg:text-5xl font-extrabold text-white text-center mb-5 drop-shadow-lg">Featured Products
        </h1>
        <hr class="w-50 mx-auto pb-15 ">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <DarkProductCard v-for="product in productsToShow" :key="product.id" :product="product" />
        </div>

        <!-- VIEW ALL BUTTON -->
        <div class="mt-20 flex flex-row justify-center text-center items-center">
            <button @click="viewAll"
                class="group text-white px-6 py-3 rounded hover:bg-white/80 hover:text-primary transition-colors flex flex-row gap-3">
                <span>View All Products</span>
                <ArrowRightCircle class="w-5 h-5 -rotate-45 bg-primary rounded-full group-hover:bg-white" />
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import DarkProductCard from './DarkProductCard.vue';
import { ArrowRightCircle } from 'lucide-vue-next';
import type { Product } from '@/product';
import { computed } from 'vue';

const props = defineProps({
    products: {
        type: [Array, Object], // Accept both array and paginated object
        default: () => [],
    },
});

const page = usePage()
const products = page.props.products;

const productsToShow = computed<Product[]>(() => {
    if (Array.isArray(props.products)) return props.products;
    if ('data' in props.products && Array.isArray(props.products.data)) {
        return props.products.data;
    }
    return [];
});
function viewAll() {
    router.visit('/products'); // Redirect to full product listing page
}
</script>
