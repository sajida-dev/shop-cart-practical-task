<template>
    <div class="relative overflow-hidden rounded-2xl bg-neutral-800 border border-neutral-700
           shadow-lg transition-all duration-300 hover:shadow-2xl hover:-tranneutral-y-1">

        <!-- Product Info -->
        <div class="bg-neutral-800 text-white px-4 pt-4 pb-6 rounded-t-3xl">

            <!-- Title & Price -->
            <Link v-if="product.id" :href="`/products/${product.id}`" class="flex justify-between items-center mb-3">
                <div>
                    <h2 class="font-semibold text-lg">{{ name }}</h2>
                    <p>Stock Quantity: {{ product.stock_quantity }}</p>
                </div>

                <div class="flex flex-col">
                    <span class="font-semibold text-lg">Rs {{ price }}</span>
                </div>
            </Link>

            <div class="flex items-center justify-between gap-2">
                <button
                    class="flex-1 bg-primary text-white py-3 rounded-xl font-semibold
                 transition hover:bg-primary disabled:cursor-not-allowed disabled:bg-neutral-700 disabled:text-neutral-400"
                    :disabled="!inStock" @click="onAddToCart">
                    {{ inStock ? 'ADD TO CART' : 'OUT OF STOCK' }}
                </button>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useShopStore } from '@/stores/useShopStore'
import { Link } from '@inertiajs/vue3'
import type { Product } from '@/product'

const shop = useShopStore()


const props = defineProps<{
    product: Product
}>()

/* 
   Computed bindings
 */

const name = computed(() => props.product.name)
const price = computed(() => props.product.price)
const inStock = computed(() => props.product.inStock)

/* 
   Cart
 */

function onAddToCart() {
    shop.addToCart({
        id: props.product.id,
        name: props.product.name,
        stock_quantity: props.product.stock_quantity,
        price: price.value,
        inStock: inStock.value,
    })
}
</script>


<style scoped>
/* Smooth scroll for thumbnails */
::-webkit-scrollbar {
    height: 4px;
}

::-webkit-scrollbar-thumb {
    background: #555;
    border-radius: 4px;
}
</style>
