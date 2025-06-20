<template>
  <div class="vet-modal" v-if="isVisible" role="dialog" aria-modal="true"></div>
  <div class="vet-modal" v-if="isVisible">
    <div class="vet-modal-content">
      <div class="vet-modal-header">
        <h4>Confirmar Eliminación</h4>
        <button class="vet-modal-close" @click="$emit('cancel')">&times;</button>
      </div>
      <div style="padding: 20px;">
        <p>¿Estás seguro que deseas eliminar este examen médico?</p>
        <p><strong>Esta acción no se puede deshacer.</strong></p>
      </div>
      <div class="vet-card-footer">
        <button type="button" class="vet-btn secondary" @click="$emit('cancel')">
          <i class="fas fa-times"></i> Cancelar
        </button>
        <button type="button" class="vet-btn alert" @click="$emit('confirm')">
          <i class="fas fa-trash"></i> Eliminar
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EliminarExamen',
  props: {
    isVisible: {
      type: Boolean,
      default: false
    }
  }
}
</script>

<style scoped>
.vet-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.vet-modal-content {
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  width: 90%;
  max-width: 400px;
  overflow: hidden;
}

.vet-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 20px;
  border-bottom: 1px solid #eee;
  background-color: #f8f9fa;
}

.vet-modal-header h4 {
  margin: 0;
  color: #333;
}

.vet-modal-close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #aaa;
  line-height: 1;
  padding: 0;
}

.vet-modal-close:hover {
  color: #333;
}
</style>