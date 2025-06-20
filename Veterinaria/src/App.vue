<template>
  <div class="vet-dashboard">
    <Sidebar />
    <div class="vet-main-content scroll-sections">
      <router-view /> </div>
  </div>
</template>

<script>
import Sidebar from './components/Sidebar.vue';

export default {
  name: 'App',
  components: {
    Sidebar,
  },
};
</script>

