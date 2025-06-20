<template>
  <div
    class="mascota-card"
    :class="{ 'mascota-card-selected': isSelected }"
    @click="$emit('select-mascota', mascota.id)"
  >
    {{ mascota.nombre }} ({{ mascota.especie }})
  </div>
</template>

<script>
export default {
  name: 'MascotaCard',
  props: {
    mascota: {
      type: Object,
      required: true,
      validator: (value) => value.id && value.nombre && value.especie,
    },
    selectedMascotaId: {
      type: [Number, null],
      default: null,
    },
  },
  computed: {
    isSelected() {
      return this.mascota.id === this.selectedMascotaId;
    },
  },
};
</script>

<style scoped>
.mascota-card {
  padding: 10px 15px;
  background-color: var(--bg-color-light);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  margin-bottom: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
  color: var(--text-secondary);
}

.mascota-card:hover {
  background-color: var(--hover-bg-light);
  border-color: var(--primary-light);
}

.mascota-card-selected {
  background-color: var(--selected-bg) !important;
  border-color: var(--primary) !important;
  box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
  font-weight: bold;
  color: var(--text-primary);
}
</style>