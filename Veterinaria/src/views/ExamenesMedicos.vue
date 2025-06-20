<template>
  <div class="vet-dashboard">
    <Sidebar />
    <div class="vet-main-content">
      <div class="vet-content-header">
        <h1 class="vet-page-title"><i class="fas fa-microscope"></i> Exámenes Médicos</h1>
        <div class="vet-breadcrumb">
          <router-link to="/inicio">Inicio</router-link> /
          <span>Exámenes Médicos</span>
        </div>
      </div>

      <div class="vet-card">
        <div class="vet-card-header">
          <h2 class="vet-card-title">Listado de Exámenes</h2>
          <div class="vet-card-actions">
            <input type="text" class="vet-input" placeholder="Buscar examen..." v-model="searchTerm">
            <button class="vet-btn primary">
              <i class="fas fa-plus"></i> Añadir Nuevo
            </button>
          </div>
        </div>
        <div class="vet-card-body">
          <div class="vet-examen-list">
            <ExamenMedicoCard
              v-for="examen in filteredExams"
              :key="examen.id"
              :examen="examen"
              @delete-examen="handleDeleteExamen"
            />
            <p v-if="filteredExams.length === 0" class="text-center text-muted mt-4">No se encontraron exámenes médicos.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <ConfirmDeleteModal
    :is-visible="showDeleteModal"
    @cancel="showDeleteModal = false"
    @confirm="confirmDeleteExamen"
  />
</template>

<script>
import Sidebar from '@/components/Sidebar.vue';
import ExamenCard from '@/components/ExamenCard.vue';
import EliminarExamen from '@/components/EliminarExamen.vue';

export default {
  name: 'ExamenesMedicos',
  components: {
    Sidebar,
    ExamenMedicoCard,
    ConfirmDeleteModal
  },
  data() {
    return {
      searchTerm: '',
      examenes: [
        { id: 1, nombre: 'Análisis de sangre completo', fecha: '01/03/2025', consultaId: '001', estado: 'Completado' },
        { id: 2, nombre: 'Radiografía torácica', fecha: '10/03/2025', consultaId: '002', estado: 'Completado' },
        { id: 3, nombre: 'Examen de orina', fecha: '15/03/2025', consultaId: '003', estado: 'Pendiente' },
        { id: 4, nombre: 'Ecografía abdominal', fecha: '20/03/2025', consultaId: '004', estado: 'Completado' },
        { id: 5, nombre: 'Copia de seguridad del examen', fecha: '25/03/2025', consultaId: '005', estado: 'Pendiente' },
      ],
      showDeleteModal: false,
      examenToDeleteId: null
    };
  },
  computed: {
    filteredExams() {
      if (!this.searchTerm) {
        return this.examenes;
      }
      const lowerCaseSearchTerm = this.searchTerm.toLowerCase();
      return this.examenes.filter(examen =>
        examen.nombre.toLowerCase().includes(lowerCaseSearchTerm) ||
        examen.fecha.includes(lowerCaseSearchTerm) ||
        examen.consultaId.includes(lowerCaseSearchTerm)
      );
    }
  },
  methods: {
    handleDeleteExamen(examenId) {
      this.examenToDeleteId = examenId;
      this.showDeleteModal = true;
    },
    confirmDeleteExamen() {
      this.examenes = this.examenes.filter(examen => examen.id !== this.examenToDeleteId);
      alert('Examen médico eliminado correctamente');
      this.showDeleteModal = false;
      this.examenToDeleteId = null;
    }
  }
};
</script>
