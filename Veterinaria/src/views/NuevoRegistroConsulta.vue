<template>
  <div class="vet-main-content scroll-sections">
    <div class="vet-header">
      <h1>Iniciar Consulta</h1>
      <p class="text-muted">Registra los detalles clínicos de la atención.</p>
    </div>

    <div class="consulta-form">
      <form @submit.prevent="guardarConsulta">
        <div class="search-mascota-section">
          <h3><i class="fas fa-paw"></i> Seleccionar Mascota</h3>
          <label for="dni-propietario">DNI del Propietario:</label>
          <input
            type="text"
            id="dni-propietario"
            class="vet-form-control"
            placeholder="Ingrese DNI para buscar mascota"
            v-model="searchDni"
            @keyup.enter="buscarMascotas"
          />
          <button type="button" @click="buscarMascotas" class="vet-btn-small">
            <i class="fas fa-search"></i> Buscar Mascota
          </button>

          <div class="mascota-results" v-if="searchAttempted">
            <p v-if="mascotasEncontradas.length === 0" class="no-results-message">
              No se encontraron mascotas para este DNI.
            </p>
            <MascotaCard
              v-for="mascota in mascotasEncontradas"
              :key="mascota.id"
              :mascota="mascota"
              :selectedMascotaId="selectedMascotaId"
              @select-mascota="seleccionarMascota"
            />
          </div>
          <p v-if="selectedMascotaDetails" class="selected-mascota-info">
            Mascota seleccionada: <strong>{{ selectedMascotaDetails.nombre }}</strong>
            (ID: {{ selectedMascotaDetails.id }})
          </p>
          <hr />
        </div>

        <h3><i class="fas fa-clipboard-list"></i> Detalles de la Consulta</h3>

        <div class="form-grid">
          <div class="full-width">
            <label for="motivo">Motivo de la Atención:</label>
            <input type="text" id="motivo" v-model="consulta.motivo" class="vet-form-control" required>
          </div>

          <div class="full-width">
            <label for="sintomas">Síntomas:</label>
            <textarea id="sintomas" v-model="consulta.sintomas" class="vet-form-control" rows="3"></textarea>
          </div>

          <div class="full-width">
            <label for="observaciones">Observaciones:</label>
            <textarea id="observaciones" v-model="consulta.observaciones" class="vet-form-control" rows="3"></textarea>
          </div>

          <div class="full-width">
            <label for="diagnostico">Diagnóstico:</label>
            <textarea id="diagnostico" v-model="consulta.diagnostico" class="vet-form-control" rows="3"></textarea>
          </div>

          <div class="full-width">
            <label for="tratamiento">Tratamiento:</label>
            <textarea id="tratamiento" v-model="consulta.tratamiento" class="vet-form-control" rows="3"></textarea>
          </div>

          <div>
            <label for="vacunas-aplicadas">Vacunas Aplicadas (separar por comas):</label>
            <input type="text" id="vacunas-aplicadas" v-model="consulta.vacunasAplicadas" class="vet-form-control" placeholder="Ej: Rabia, Moquillo">
          </div>

          <div>
            <label for="tipo">Tipo de Consulta:</label>
            <select id="tipo" v-model="consulta.tipo" class="vet-form-control" required>
              <option value="" disabled>Seleccione</option>
              <option>Consulta General</option>
              <option>Emergencia</option>
              <option>Vacunación</option>
              <option>Revisión Postoperatoria</option>
              <option>Otro</option>
            </select>
          </div>

          <div>
            <label for="veterinario">Nombre del Veterinario:</label>
            <select id="veterinario" v-model="consulta.veterinario" class="vet-form-control" required>
              <option value="" disabled>Seleccione un nombre</option>
              <option v-for="vet in veterinarios" :key="vet.id" :value="vet.nombre">
                {{ vet.nombre }}
              </option>
            </select>
          </div>

          <div>
            <label for="fecha">Fecha de Atención:</label>
            <input type="date" id="fecha" v-model="consulta.fecha" class="vet-form-control" required>
          </div>
        </div>

        <button type="submit" class="vet-btn-submit">
          <i class="fas fa-save"></i> Registrar Consulta
        </button>
      </form>
    </div>
  </div>
</template>

<script>
import MascotaCard from '../components/MascotaCard.vue';

export default {
  name: 'NuevoRegistroConsultaPage',
  components: {
    MascotaCard,
  },
  data() {
    return {
      searchDni: '',
      mascotasEncontradas: [],
      selectedMascotaId: null,
      selectedMascotaDetails: null, 
      searchAttempted: false,

      consulta: {
        mascotaId: null,
        motivo: '',
        sintomas: '',
        observaciones: '',
        diagnostico: '',
        tratamiento: '',
        vacunasAplicadas: '', 
        tipo: '',
        veterinario: '',
        fecha: '',
      },
      veterinarios: [
        { id: 1, nombre: 'Eduardo' },
        { id: 2, nombre: 'Joao' },
        { id: 3, nombre: 'Dra. Ana' },
        { id: 4, nombre: 'Dr. Carlos' },
      ], 
      
      // Datos estáticos de ejemplo de mascotas para simular la DB 
      mockMascotasDB: [
        { id: 1, dniPropietario: '12345678', nombre: 'Max', especie: 'Canino' },
        { id: 2, dniPropietario: '12345678', nombre: 'Bella', especie: 'Felino' },
        { id: 3, dniPropietario: '87654321', nombre: 'Leo', especie: 'Canino' },
        { id: 4, dniPropietario: '11223344', nombre: 'Luna', especie: 'Felino' },
      ],
    };
  },
  mounted() {

    const today = new Date().toISOString().split('T')[0];
    this.consulta.fecha = today;
  },
  methods: {
    async buscarMascotas() {
      this.searchAttempted = true;
      this.mascotasEncontradas = [];
      this.selectedMascotaId = null;
      this.selectedMascotaDetails = null; 

      if (!this.searchDni) {
        alert('Por favor, ingrese el DNI del propietario para buscar mascotas.');
        return;
      }

      console.log(`Buscando mascotas para DNI: ${this.searchDni}`);

     
      const results = this.mockMascotasDB.filter(
        (m) => m.dniPropietario === this.searchDni
      );
      this.mascotasEncontradas = results;
    },
    seleccionarMascota(mascotaId) {
      this.selectedMascotaId = mascotaId;
      const selected = this.mascotasEncontradas.find(m => m.id === mascotaId);
      this.selectedMascotaDetails = selected;
      this.consulta.mascotaId = mascotaId; 
      console.log(`Mascota seleccionada para la consulta: ${selected.nombre} (ID: ${mascotaId})`);
    },
    async guardarConsulta() {
      if (!this.selectedMascotaId) {
        alert('Por favor, seleccione una mascota antes de registrar la consulta.');
        return;
      }

    
      if (!this.consulta.motivo || !this.consulta.tipo || !this.consulta.veterinario || !this.consulta.fecha) {
        alert('Por favor, complete los campos obligatorios (Motivo, Tipo, Veterinario, Fecha).');
        return;
      }

      const datosConsulta = {
        mascota_id: this.consulta.mascotaId,
        motivo: this.consulta.motivo,
        sintomas: this.consulta.sintomas,
        observaciones: this.consulta.observaciones,
        diagnostico: this.consulta.diagnostico,
        tratamiento: this.consulta.tratamiento,
        vacunas_aplicadas: this.consulta.vacunasAplicadas.split(',').map(v => v.trim()).filter(v => v), 
        tipo_consulta: this.consulta.tipo,
        veterinario_nombre: this.consulta.veterinario,
        fecha_atencion: this.consulta.fecha,
       
      };

      console.log('Datos de la consulta a guardar:', datosConsulta);

     
      alert('Consulta registrada correctamente (simulado).');
      this.resetForm();
    },
    resetForm() {
      this.searchDni = '';
      this.mascotasEncontradas = [];
      this.selectedMascotaId = null;
      this.selectedMascotaDetails = null;
      this.searchAttempted = false;

      this.consulta = {
        mascotaId: null,
        motivo: '',
        sintomas: '',
        observaciones: '',
        diagnostico: '',
        tratamiento: '',
        vacunasAplicadas: '',
        tipo: '',
        veterinario: '',
        fecha: new Date().toISOString().split('T')[0], 
      };
    },
  },
};
</script>

<style scoped>
/* Estilos adicionales */

.consulta-form {
  background-color: #ffffff;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  margin-top: 25px;
}

.consulta-form h3 {
  color: #34495e;
  margin-bottom: 20px;
  font-size: 1.6em;
  border-bottom: 1px solid #eee;
  padding-bottom: 10px;
}

.consulta-form h3 i {
  margin-right: 10px;
  color: #3498db;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr; 
  gap: 20px;
  margin-top: 20px;
}

.full-width {
  grid-column: 1 / -1; 
}

.consulta-form label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #555;
}

.vet-form-control {
  width: 100%;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1em;
  box-sizing: border-box; 
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

.search-mascota-section {
  padding-bottom: 20px;
  margin-bottom: 20px;
  border-bottom: 1px solid #eee;
}

.vet-btn-small {
  background-color: #3498db;
  color: white;
  padding: 8px 15px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.9em;
  transition: background-color 0.3s ease;
  margin-top: 5px;
}

.vet-btn-small:hover {
  background-color: #2980b9;
}

.mascota-results {
  margin-top: 15px;
  max-height: 180px;
  overflow-y: auto;
  padding-right: 5px;
}

.no-results-message {
  color: #888;
  font-style: italic;
  text-align: center;
  padding: 10px;
}

.selected-mascota-info {
  margin-top: 15px;
  padding: 10px 15px;
  background-color: #e3f2fd;
  border: 1px solid #a4d8ff;
  border-radius: 8px;
  color: #34495e;
  font-weight: 500;
  text-align: center;
}

hr {
  margin: 30px 0;
  border: 0;
  border-top: 1px solid #eee;
}

@media (max-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr; 
  }
}
</style>