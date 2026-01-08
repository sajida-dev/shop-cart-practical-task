<template>
    <footer class="bg-neutral-900 text-neutral-300 border-t border-neutral-700 px-6 py-12">
        <div class="max-w-6xl mx-auto flex flex-wrap md:justify-between justify-center gap-10 text-center">

            <!-- Logo + Social Icons -->
            <div class="flex flex-col gap-4 min-w-[180px] items-center ">
                <div class="flex items-center gap-2">
                    <AppLogo />
                </div>
                <div class="flex gap-3">
                    <button v-for="(icon, index) in socialIcons" :key="index" @click="$emit('social-click', icon)"
                        class="text-neutral-400 hover:text-primary transition-colors text-xl">
                        <i :class="icon"></i>
                    </button>
                </div>
            </div>

            <!-- Footer Links -->
            <div v-for="(column, index) in footerLinks" :key="index" class="flex flex-col gap-2 min-w-[180px] ">
                <h3 class="text-white font-semibold text-lg">{{ column.title }}</h3>
                <ul class="flex flex-col gap-2">
                    <li v-for="(item, i) in column.items" :key="i" @click="$emit('link-click', item)"
                        class="hover:text-primary cursor-pointer transition-colors">
                        {{ item }}
                    </li>
                </ul>
            </div>

            <!-- Subscribe -->
            <div class="flex-1 min-w-[220px] flex flex-col gap-3 text-left">
                <h3 class="text-white font-semibold text-lg">{{ subscribeTitle }}</h3>
                <p class="text-neutral-400 text-sm">{{ subscribeText }}</p>
                <div class="flex gap-2">
                    <input type="email" v-model="email" :placeholder="subscribePlaceholder"
                        class="flex-1 px-4 py-2 rounded-lg bg-neutral-800 border border-neutral-700 text-neutral-200 placeholder-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary" />
                    <button @click="handleSubscribe"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary transition-colors">
                        {{ subscribeButton }}
                    </button>
                </div>
            </div>

        </div>

        <div class="mt-10 text-center text-neutral-500 text-sm">
            © {{ new Date().getFullYear() }} {{ logoText }}. All rights reserved.
        </div>
    </footer>
</template>

<script setup>
import { ref } from "vue";
import AppLogo from "../AppLogo.vue";

const props = defineProps({
    logoText: { type: String, default: "SPARK" },
    socialIcons: {
        type: Array,
        default: () => [
            "fab fa-facebook-f",
            "fab fa-twitter",
            "fab fa-youtube",
            "fab fa-instagram",
            "fab fa-amazon",
        ],
    },
    footerLinks: {
        type: Array,
        default: () => [
            {
                title: "Ultras",
                items: [
                    "About us",
                    "Conditions",
                    "Our Journals",
                    "Careers",
                    "Affiliate Programme",
                    "Ultras Press",
                ],
            },
            {
                title: "Customer Service",
                items: [
                    "FAQ",
                    "Contact",
                    "Privacy Policy",
                    "Returns & Refunds",
                    "Cookie Guidelines",
                    "Delivery Information",
                ],
            },
        ],
    },
    subscribeTitle: { type: String, default: "Subscribe Us" },
    subscribeText: {
        type: String,
        default:
            "Subscribe to our newsletter to get updates about our grand offers.",
    },
    subscribePlaceholder: { type: String, default: "Email Address" },
    subscribeButton: { type: String, default: "Subscribe" },
});

const email = ref("");

const handleSubscribe = () => {
    if (!email.value.trim()) {
        alert("Please enter your email address!");
        return;
    }
    alert(`Subscribed successfully with: ${email.value}`);
    email.value = "";
};
</script>
