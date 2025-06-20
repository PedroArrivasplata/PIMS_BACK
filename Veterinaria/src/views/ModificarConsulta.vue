<template>
  <div class="vet-main-content scroll-sections">
    <div class="vet-header">
      <h1>Editar Consulta Médica</h1>
      <p class="text-muted">Busca y modifica los registros de consultas existentes.</p>
    </div>

    <div class="modification-container">
      <div class="search-and-list-section">
        <h3><i class="fas fa-search"></i> Buscar Consulta</h3>
        <label for="search-term">DNI del Propietario, ID Mascota o ID Consulta:</label>
        <input
          type="text"
          id="search-term"
          class="vet-form-control"
          placeholder="Ingrese término de búsqueda"
          v-model="searchTerm"
          @keyup.enter="buscarConsultas"
        />
        <button @click="buscarConsultas" class="vet-btn">
          <i class="fas fa-search"></i> Buscar
        </button>

        <div class="results-list">
          <p v-if="searchAttempted && consultasEncontradas.length === 0" class="no-results-message">
            No se encontraron consultas.
          </p>
          <div
            v-for="consulta in consultasEncontradas"
            :key="consulta.id"
            class="consulta-summary-card"
            @click="seleccionarConsulta(consulta.id)"
            :class="{ 'selected-consulta': selectedConsultaId === consulta.id }"
          >
            <h4>Consulta ID: {{ consulta.id }} - {{ consulta.mascotaNombre }}</h4>
            <p>Fecha: {{ consulta.fechaAtencion }} | Veterinario: {{ consulta.veterinarioNombre }}</p>
            <p>Motivo: {{ consulta.motivo }}</p>
          </div>
        </div>
      </div>

      <div v-if="selectedConsultaId" class="edit-form-section">
        <h3><i class="fas fa-edit"></i> Detalle y Edición de Consulta</h3>
        <div v-if="loadingConsultaDetail" class="loading-message">Cargando detalles de la consulta...</div>
        <div v-else-if="selectedConsulta">
          <h4>Mascota: {{ selectedConsulta.mascotaNombre }} (ID: {{ selectedConsulta.mascotaId }})</h4>
          <p class="text-muted">Fecha de Consulta: {{ selectedConsulta.fechaAtencion }}</p>

          <form @submit.prevent="guardarCambios">
            <div class="form-grid">
              <div class="full-width">
                <label for="edit_motivo">Motivo de la Atención:</label>
                <input type="text" id="edit_motivo" v-model="selectedConsulta.motivo" class="vet-form-control" required>
              </div>

              <div class="full-width">
                <label for="edit_sintomas">Síntomas:</label>
                <textarea id="edit_sintomas" v-model="selectedConsulta.sintomas" class="vet-form-control" rows="3"></textarea>
              </div>

              <div class="full-width">
                <label for="edit_observaciones">Observaciones:</label>
                <textarea id="edit_observaciones" v-model="selectedConsulta.observaciones" class="vet-form-control" rows="3"></textarea>
              </div>

              <div class="full-width">
                <label for="edit_diagnostico">Diagnóstico:</label>
                <textarea id="edit_diagnostico" v-model="selectedConsulta.diagnostico" class="vet-form-control" rows="3"></textarea>
              </div>

              <div class="full-width">
                <label for="edit_tratamiento">Tratamiento:</label>
                <textarea id="edit_tratamiento" v-model="selectedConsulta.tratamiento" class="vet-form-control" rows="3"></textarea>
              </div>

              <div>
                <label for="edit_vacunas">Vacunas Aplicadas (separar por comas):</label>
                <input type="text" id="edit_vacunas" v-model="selectedConsulta.vacunasAplicadas" class="vet-form-control" placeholder="Ej: Rabia, Moquillo">
              </div>

              <div>
                <label for="edit_tipo">Tipo de Consulta:</label>
                <select id="edit_tipo" v-model="selectedConsulta.tipoConsulta" class="vet-form-control" required>
                  <option>Consulta General</option>
                  <option>Emergencia</option>
                  <option>Vacunación</option>
                  <option>Revisión Postoperatoria</option>
                  <option>Otro</option>
                </select>
              </div>

              <div>
                <label for="edit_veterinario">Nombre del Veterinario:</label>
                <select id="edit_veterinario" v-model="selectedConsulta.veterinarioNombre" class="vet-form-control" required>
                  <option v-for="vet in veterinarios" :key="vet.id" :value="vet.nombre">
                    {{ vet.nombre }}
                  </option>
                </select>
              </div>

              <div>
                <label for="edit_fecha">Fecha de Atención:</label>
                <input type="date" id="edit_fecha" v-model="selectedConsulta.fechaAtencion" class="vet-form-control" required>
              </div>
            </div>
            <button type="submit" class="vet-btn-submit">
              <i class="fas fa-save"></i> Guardar Cambios
            </button>
          </form>
        </div>
      </div>
      <div v-else class="edit-form-section placeholder-message">
          <p>Seleccione una consulta de la lista para ver y editar sus detalles.</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ModificarConsultaPage',
  data() {
    return {
      searchTerm: '',
      searchAttempted: false,
      consultasEncontradas: [],
      selectedConsultaId: null,
      selectedConsulta: null, 
      loadingConsultaDetail: false,
      veterinarios: [
        { id: 1, nombre: 'Eduardo' },
        { id: 2, nombre: 'Joao' },
        { id: 3, nombre: 'Dra. Ana' },
        { id: 4, nombre: 'Dr. Carlos' },
      ], 

      mockConsultasDB: [
        {
          id: 201,
          mascotaId: 1,
          mascotaNombre: 'Max',
          propietarioDni: '12345678',
          motivo: 'Tos persistente',
          sintomas: 'Tos seca, decaimiento ocasional',
          observaciones: 'Ha estado en contacto con otros perros en el parque.',
          diagnostico: 'Traqueobronquitis infecciosa (tos de las perreras)',
          tratamiento: 'Antibióticos, antiinflamatorios y reposo.',
          vacunasAplicadas: 'Ninguna',
          tipoConsulta: 'Consulta General',
          veterinarioNombre: 'Eduardo',
          fechaAtencion: '2024-05-20',
        },
        {
          id: 202,
          mascotaId: 2,
          mascotaNombre: 'Bella',
          propietarioDni: '12345678',
          motivo: 'Vómitos y diarrea',
          sintomas: 'Vómitos frecuentes, diarrea líquida, pérdida de apetito.',
          observaciones: 'El dueño dice que comió algo de la basura.',
          diagnostico: 'Gastroenteritis aguda',
          tratamiento: 'Fluidoterapia, protectores gástricos, dieta blanda.',
          vacunasAplicadas: '',
          tipoConsulta: 'Emergencia',
          veterinarioNombre: 'Joao',
          fechaAtencion: '2024-06-01',
        },
        {
          id: 203,
          mascotaId: 3,
          mascotaNombre: 'Leo',
          propietarioDni: '87654321',
          motivo: 'Chequeo anual',
          sintomas: 'Ninguno',
          observaciones: 'Mascota en excelente estado general.',
          diagnostico: 'Saludable',
          tratamiento: 'Recomendación de desparasitación y próxima vacuna.',
          vacunasAplicadas: 'Rabia',
          tipoConsulta: 'Consulta General',
          veterinarioNombre: 'Dra. Ana',
          fechaAtencion: '2024-01-15',
        },
      ],
    };
  },
  methods: {
    async buscarConsultas() {
      this.searchAttempted = true;
      this.consultasEncontradas = [];
      this.selectedConsultaId = null;
      this.selectedConsulta = null;

      if (!this.searchTerm) {
        alert('Por favor, ingrese un término de búsqueda (DNI, ID Mascota o ID Consulta).');
        return;
      }

      console.log(`Buscando consultas para: ${this.searchTerm}`);

      const results = this.mockConsultasDB.filter(
        (c) => c.propietarioDni === this.searchTerm ||
               c.mascotaId.toString() === this.searchTerm ||
               c.id.toString() === this.searchTerm
      ).map(c => ({
          id: c.id,
          mascotaNombre: c.mascotaNombre,
          fechaAtencion: c.fechaAtencion,
          veterinarioNombre: c.veterinarioNombre,
          motivo: c.motivo, 
      })); 
      this.consultasEncontradas = results;
    },
    async seleccionarConsulta(consultaId) {
      this.selectedConsultaId = consultaId;
      this.loadingConsultaDetail = true;
      this.selectedConsulta = null;

      console.log(`Cargando detalles de consulta ID: ${consultaId}`);

      setTimeout(() => {
        const fullConsulta = this.mockConsultasDB.find(c => c.id === consultaId);
        if (fullConsulta) {
          this.selectedConsulta = { ...fullConsulta };
        } else {
          console.error("Consulta no encontrada en mock DB.");
        }
        this.loadingConsultaDetail = false;
      }, 500); 
    },
    async guardarCambios() {
      if (!this.selectedConsulta) {
        alert('No hay ninguna consulta seleccionada para guardar.');
        return;
      }

      // Validaciones básicas del formulario
      if (!this.selectedConsulta.motivo || !this.selectedConsulta.tipoConsulta ||
          !this.selectedConsulta.veterinarioNombre || !this.selectedConsulta.fechaAtencion) {
        alert('Por favor, complete los campos obligatorios (Motivo, Tipo, Veterinario, Fecha).');
        return;
      }

      const updatedConsultaData = {
        consulta_id: this.selectedConsulta.id,
        mascota_id: this.selectedConsulta.mascotaId, 
        motivo: this.selectedConsulta.motivo,
        sintomas: this.selectedConsulta.sintomas,
        observaciones: this.selectedConsulta.observaciones,
        diagnostico: this.selectedConsulta.diagnostico,
        tratamiento: this.selectedConsulta.tratamiento,
        vacunas_aplicadas: this.selectedConsulta.vacunasAplicadas.split(',').map(v => v.trim()).filter(v => v),
        tipo_consulta: this.selectedConsulta.tipoConsulta,
        veterinario_nombre: this.selectedConsulta.veterinarioNombre,
        fecha_atencion: this.selectedConsulta.fechaAtencion,
      };

      console.log('Datos de la consulta a actualizar:', updatedConsultaData);

      alert('Cambios guardados en la consulta (simulado).');

      this.selectedConsultaId = null;
      this.selectedConsulta = null;
      this.buscarConsultas(); 
    },
  },
};
</script>

<style scoped>
/* Estilos específicos para esta página. */

.modification-container {
  display: flex;
  justify-content: space-between;
  gap: 30px;
  padding: 20px;
  flex-wrap: wrap;
}

.search-and-list-section,
.edit-form-section {
  background-color: #ffffff;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  flex: 1;
  min-width: 400px;
}

.search-and-list-section h3,
.edit-form-section h3 {
  color: #34495e;
  margin-bottom: 25px;
  font-size: 1.8em;
  border-bottom: 2px solid #eee;
  padding-bottom: 15px;
}

.search-and-list-section h3 i,
.edit-form-section h3 i {
  margin-right: 10px;
  color: #3498db;
}

.vet-form-control {
  width: 100%;
  padding: 12px;
  margin-bottom: 15px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1em;
  box-sizing: border-box;
}

.vet-btn {
  background-color: #3498db;
  color: white;
  padding: 12px 25px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1em;
  transition: background-color 0.3s ease;
  width: auto;
  margin-top: 10px;
}

.vet-btn:hover {
  background-color: #2980b9;
}

.vet-btn i {
  margin-right: 8px;
}

.results-list {
  margin-top: 20px;
  max-height: 400px;
  overflow-y: auto;
  padding-right: 5px;
}

.consulta-summary-card {
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 15px;
  margin-bottom: 10px;
  cursor: pointer;
  transition: background-color 0.2s ease, border-color 0.2s ease;
}

.consulta-summary-card:hover {
  background-color: #e2f0fb;
  border-color: #a4d8ff;
}

.consulta-summary-card.selected-consulta {
  background-color: #e3f2fd !important;
  border-color: #3498db !important;
  box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
  font-weight: bold;
}

.consulta-summary-card h4 {
  margin-top: 0;
  color: #333;
}

.consulta-summary-card p {
  margin-bottom: 5px;
  font-size: 0.9em;
  color: #666;
}

.loading-message, .placeholder-message {
  text-align: center;
  padding: 40px;
  color: #7f8c8d;
  font-style: italic;
}

/* Estilos para el formulario de edición de consulta */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-top: 20px;
}

.full-width {
  grid-column: 1 / -1;
}

textarea.vet-form-control {
  resize: vertical;
  min-height: 80px;
}

.vet-btn-submit {
  background-color: #2ecc71;
  color: white;
  padding: 15px 30px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1.1em;
  font-weight: 600;
  transition: background-color 0.3s ease, transform 0.2s ease;
  width: auto;
  display: block;
  margin-top: 30px;
  margin-left: auto;
  margin-right: auto;
}

.vet-btn-submit:hover {
  background-color: #27ae60;
  transform: translateY(-2px);
}

.vet-btn-submit i {
  margin-right: 10px;
}

@media (max-width: 992px) {
  .modification-container {
    flex-direction: column;
  }
  .search-and-list-section,
  .edit-form-section {
    min-width: unset;
    width: 100%;
  }
  .form-grid {
    grid-template-columns: 1fr;
  }
}
</style>