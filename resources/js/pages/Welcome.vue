<script setup lang="ts">
import FeatureProductsSection from '@/components/FeatureProductsSection.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { mapProducts, type Product } from '@/product'
import { AppPageProps } from '@/types';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
)
const page = usePage<AppPageProps<{ products: Product[] }>>()
const products: Product[] = mapProducts(page.props.products ?? [])

</script>

<template>
    <PublicLayout hero-title="Welcome to Your Next Adventure"
        hero-subtitle="Seamless login, personalized experience, and a world of possibilities." :hero-buttons="[
            { label: 'Get Started', href: '/register', primary: true },
            { label: 'Login', href: '/login' }
        ]">

        <Head title="Welcome">
            <link rel="preconnect" href="https://rsms.me/" />
            <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
        </Head>

        <div class="bg-neutral-900 max-w-full py-15">
            <FeatureProductsSection :products="products" />
        </div>
    </PublicLayout>
</template>

<style scoped>
/* Hero animation */
h1 {
    animation: fadeInUp 1s ease forwards;
    opacity: 0;
}

p {
    animation: fadeInUp 1.3s ease forwards;
    opacity: 0;
}

@keyframes fadeInUp {
    0% {
        opacity: 0;
        transform: translateY(20px);
    }

    100% {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
