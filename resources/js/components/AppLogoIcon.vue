<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <img :src="'/e-com-logo.png'" :alt="'Reviews Based Product Recommendation System'" :class="className" />
</template>
