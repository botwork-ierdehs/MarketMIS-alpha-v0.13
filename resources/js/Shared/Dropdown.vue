<template>
  <button ref="button" type="button" @click="toggle">
    <slot />
  </button>

  <teleport to="body">
    <div v-if="show">
      <!-- Background overlay to close -->
      <div
      style="position: fixed; top: 0; right: 0; left: 0; bottom: 0; z-index: 99998; background: black; opacity: 0.2"
        @click="close"
      />

      <!-- Dropdown menu -->
      <div
        ref="dropdown"
        style="position: absolute; z-index: 99999"
        @click.stop
      >
        <slot name="dropdown" />
      </div>
    </div>
  </teleport>
</template>

<script>
import { createPopper } from '@popperjs/core'

export default {
  props: {
    placement: {
      type: String,
      default: 'bottom-end',
    },
    autoClose: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      show: false,
      popperInstance: null,
    }
  },
  methods: {
    toggle() {
      this.show = !this.show

      this.$nextTick(() => {
        if (this.show && this.$refs.button && this.$refs.dropdown) {
          if (this.popperInstance) {
            this.popperInstance.destroy()
          }
          this.popperInstance = createPopper(this.$refs.button, this.$refs.dropdown, {
            placement: this.placement,
          })
        }
      })
    },
    close() {
      this.show = false
    },
  },
  beforeUnmount() {
    if (this.popperInstance) {
      this.popperInstance.destroy()
      this.popperInstance = null
    }
  },
  mounted() {
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        this.close()
      }
    })
  },
}
</script>

<style scoped>
/* Optional: Smooth transition or size constraints */
</style>
