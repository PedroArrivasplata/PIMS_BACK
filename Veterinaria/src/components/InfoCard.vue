<template>
  <div class="vet-card" :class="type" role="region" :aria-label="`Tarjeta de ${title}`"></div>
  <div class="vet-card" :class="type">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="m-0">{{ title }}</h3>
      <div class="vet-card-icon">
        <i :class="iconClass"></i>
      </div>
    </div>
    <div class="mb-3">
      <p>{{ description1 }}</p>
      <p>{{ description2 }}</p>
    </div>
    <div class="vet-card-footer">
      <a :href="link" class="vet-btn">{{ buttonText }}</a>
    </div>
  </div>
</template>

<script>
export default {
  name: 'InfoCard',
  props: {
    title: {
      type: String,
      required: true,
    },
    iconClass: {
      type: String,
      required: true,
    },
    description1: {
      type: String,
      required: true,
    },
    description2: {
      type: String,
      default: '',
    },
    link: {
      type: String,
      default: '#',
    },
    buttonText: {
      type: String,
      default: 'Ver más',
    },
    type: {
      type: String,
      default: '', // Puede ser 'info', 'warning', 'alert'
    },
  },
};
</script>


