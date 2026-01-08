<script setup lang="ts">
import PublicHeader from '@/components/layouts/PublicHeader.vue'
import Footer from '@/components/layouts/Footer.vue'
import { Link } from '@inertiajs/vue3'

interface HeroButton {
    label: string
    href: string
    primary?: boolean
}

withDefaults(
    defineProps<{
        showHero?: boolean
        heroTitle?: string
        heroSubtitle?: string
        heroButtons?: HeroButton[]
        url?: string
    }>(),
    {
        showHero: true,
        heroButtons: () => [],
    }
)
</script>

<template>
    <!-- HERO -->
    <section v-if="showHero" class="flex min-h-screen flex-col items-center justify-center p-6 text-white" :style="{
        backgroundImage: `url(${url ?? 'https://mir-s3-cdn-cf.behance.net/project_modules/source/244d47129717169.6170bea8eee75.gif'
            })`,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
    }">
        <div class="absolute inset-0 bg-black bg-opacity-50 mix-blend-saturation filter grayscale"></div>
        <PublicHeader />

        <div v-if="heroTitle" class="mt-28 text-center max-w-3xl">
            <h1 class="text-4xl lg:text-6xl font-extrabold drop-shadow-lg">
                {{ heroTitle }}
            </h1>

            <p class="mt-4 text-lg lg:text-2xl text-white/80">
                {{ heroSubtitle }}
            </p>

            <div v-if="heroButtons.length" class="mt-8 flex gap-4 justify-center">
                <Link v-for="(btn, index) in heroButtons" :key="index" :href="btn.href"
                    class="rounded-full px-6 py-3 text-sm font-semibold transition" :class="btn.primary
                        ? 'bg-primary text-white hover:bg-primary/80'
                        : 'border border-white/30 text-white hover:bg-white/20'">
                    {{ btn.label }}
                </Link>
            </div>
        </div>
    </section>

    <!-- PAGE CONTENT -->
    <main class="bg-neutral-900">
        <slot />
    </main>

    <Footer logo-text="E-Com" />
</template>
