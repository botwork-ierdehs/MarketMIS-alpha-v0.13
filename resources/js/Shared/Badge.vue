<template>
  <span
    class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
    :class="badgeClasses"
  >
    <slot />
  </span>
</template>

<script setup>
const props = defineProps({
  color: {
    type: String,
    default: 'gray', // default fallback
    validator: (value) => ['green', 'red', 'yellow', 'blue', 'gray'].includes(value),
  },
})

const colorMap = {
  green: {
    bg: 'bg-green-50',
    text: 'text-green-700',
    ring: 'ring-green-600/20',
  },
  red: {
    bg: 'bg-red-50',
    text: 'text-red-700',
    ring: 'ring-red-600/20',
  },
  yellow: {
    bg: 'bg-yellow-50',
    text: 'text-yellow-800',
    ring: 'ring-yellow-600/20',
  },
  blue: {
    bg: 'bg-blue-50',
    text: 'text-blue-700',
    ring: 'ring-blue-600/20',
  },
  gray: {
    bg: 'bg-gray-50',
    text: 'text-gray-700',
    ring: 'ring-gray-600/20',
  },
}

const badgeClasses = [
  colorMap[props.color].bg,
  colorMap[props.color].text,
  colorMap[props.color].ring,
]
</script>
