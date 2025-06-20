<template>
  <div class="vet-main-content scroll-sections">
    <div class="vet-header">
      <h1>Editar Cartilla de Vacunación</h1>
      <p class="text-muted">Busca y modifica las cartillas de vacunación existentes.</p>
    </div>

    <div class="modification-container">
      <div class="search-and-list-section">
        <h3><i class="fas fa-search"></i> Buscar Cartilla</h3>
        <label for="search-dni">DNI del Propietario o ID de Mascota:</label>
        <input
          type="text"
          id="search-dni"
          class="vet-form-control"
          placeholder="Ingrese DNI o ID"
          v-model="searchTerm"
          @keyup.enter="buscarCartillas"
        />
        <button @click="buscarCartillas" class="vet-btn">
          <i class="fas fa-search"></i> Buscar
        </button>

        <div class="results-list">
          <p v-if="searchAttempted && cartillasEncontradas.length === 0">
            No se encontraron cartillas.
          </p>
          <div
            v-for="cartilla in cartillasEncontradas"
            :key="cartilla.id"
            class="cartilla-summary-card"
            @click="seleccionarCartilla(cartilla.id)"
            :class="{ 'selected-cartilla': selectedCartillaId === cartilla.id }"
          >
            <h4>{{ cartilla.mascotaNombre }} - ID: {{ cartilla.id }}</h4>
            <p>Propietario: {{ cartilla.propietarioDni }}</p>
            <p>Última actualización: {{ cartilla.ultimaActualizacion }}</p>
          </div>
        </div>
      </div>

      <div v-if="selectedCartillaId" class="edit-form-section">
        <h3><i class="fas fa-edit"></i> Detalle y Edición de Cartilla</h3>
        <div v-if="loadingCartillaDetail" class="loading-message">Cargando detalles de la cartilla...</div>
        <div v-else>
          <h4>Cartilla de: {{ selectedCartilla.mascotaNombre }}</h4>
          <p>DNI Propietario: {{ selectedCartilla.propietarioDni }}</p>

          <form @submit.prevent="guardarCambios">
            <div class="vaccine-row header-row">
              <div>Vacuna</div>
              <div>Fecha de aplicación</div>
              <div>Próxima dosis</div>
            </div>
            <div v-for="(vacuna, index) in selectedCartilla.vacunas" :key="index" class="vaccine-row">
              <label :for="`edit_vacuna_${index}`">
                <input
                  type="checkbox"
                  :id="`edit_vacuna_${index}`"
                  v-model="vacuna.aplicada"
                />
                {{ vacuna.nombre }}
              </label>
              <input
                type="date"
                :id="`edit_vacuna_${index}_fecha`"
                v-model="vacuna.fechaAplicacion"
                :disabled="!vacuna.aplicada"
              />
              <input
                type="date"
                :id="`edit_vacuna_${index}_proxima`"
                v-model="vacuna.proximaDosis"
                :disabled="!vacuna.aplicada"
              />
            </div>
            <button type="submit" class="vet-btn mt-4">
              <i class="fas fa-save"></i> Guardar Cambios
            </button>
          </form>
        </div>
      </div>
      <div v-else class="edit-form-section placeholder-message">
          <p>Seleccione una cartilla de la lista para ver y editar sus detalles.</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ModificarCartillaPage',
  data() {
    return {
      searchTerm: '',
      searchAttempted: false,
      cartillasEncontradas: [],
      selectedCartillaId: null,
      selectedCartilla: null, 
      loadingCartillaDetail: false,

      mockCartillasDB: [
        {
          id: 101,
          mascotaId: 1,
          mascotaNombre: 'Max',
          propietarioDni: '12345678',
          ultimaActualizacion: '2024-05-10',
          vacunas: [
            { nombre: 'Parvovirus', aplicada: true, fechaAplicacion: '2023-05-10', proximaDosis: '2024-05-10' },
            { nombre: 'Distemper (Moquillo)', aplicada: true, fechaAplicacion: '2023-05-10', proximaDosis: '2024-05-10' },
            { nombre: 'Rabia', aplicada: false, fechaAplicacion: '', proximaDosis: '' },
          ],
        },
        {
          id: 102,
          mascotaId: 2,
          mascotaNombre: 'Bella',
          propietarioDni: '12345678',
          ultimaActualizacion: '2023-11-20',
          vacunas: [
            { nombre: 'Leucemia Felina', aplicada: true, fechaAplicacion: '2023-11-20', proximaDosis: '2024-11-20' },
            { nombre: 'Rinotraqueitis', aplicada: false, fechaAplicacion: '', proximaDosis: '' },
          ],
        },
        {
          id: 103,
          mascotaId: 3,
          mascotaNombre: 'Leo',
          propietarioDni: '87654321',
          ultimaActualizacion: '2024-01-15',
          vacunas: [
            { nombre: 'Parvovirus', aplicada: true, fechaAplicacion: '2024-01-15', proximaDosis: '2025-01-15' },
          ],
        },
      ],
      // Lista completa de vacunas para añadir a cartillas existentes si faltan
      allVacunasNames: [
        'Parvovirus', 'Coronavirus', 'Distemper (Moquillo)', 'Hepatitis Canina',
        'Parainfluenza', 'Leptospira', 'Tos de las perreras', 'Rabia',
        'Leucemia Felina', 'Rinotraqueitis', 'Calicivirus', 'Panleucopenia'
      ],
    };
  },
  watch: {
    // Observa cambios en el estado 'aplicada' de las vacunas en la cartilla seleccionada
    'selectedCartilla.vacunas': {
      handler(newVacunas) {
        if (newVacunas) {
          newVacunas.forEach(vacuna => {
            if (vacuna.aplicada && !vacuna.fechaAplicacion) {
              const today = new Date().toISOString().split('T')[0];
              vacuna.fechaAplicacion = today;
              const nextYear = new Date();
              nextYear.setFullYear(nextYear.getFullYear() + 1);
              vacuna.proximaDosis = nextYear.toISOString().split('T')[0];
            } else if (!vacuna.aplicada) {
              vacuna.fechaAplicacion = '';
              vacuna.proximaDosis = '';
            }
          });
        }
      },
      deep: true,
    },
  },
  methods: {
    async buscarCartillas() {
      this.searchAttempted = true;
      this.cartillasEncontradas = [];
      this.selectedCartillaId = null;
      this.selectedCartilla = null;

      if (!this.searchTerm) {
        alert('Por favor, ingrese un DNI o ID para buscar.');
        return;
      }

      console.log(`Buscando cartillas para: ${this.searchTerm}`);

      const results = this.mockCartillasDB.filter(
        (c) => c.propietarioDni === this.searchTerm || c.id.toString() === this.searchTerm
      ).map(c => ({
          id: c.id,
          mascotaNombre: c.mascotaNombre,
          propietarioDni: c.propietarioDni,
          ultimaActualizacion: c.ultimaActualizacion,
      })); 
      this.cartillasEncontradas = results;
    },
    async seleccionarCartilla(cartillaId) {
      this.selectedCartillaId = cartillaId;
      this.loadingCartillaDetail = true;
      this.selectedCartilla = null; 

      console.log(`Cargando detalles de cartilla ID: ${cartillaId}`);

      setTimeout(() => {
        const fullCartilla = this.mockCartillasDB.find(c => c.id === cartillaId);
        if (fullCartilla) {
          this.selectedCartilla = this.prepareCartillaForEditing(fullCartilla);
        } else {
          console.error("Cartilla no encontrada en mock DB.");
        }
        this.loadingCartillaDetail = false;
      }, 500); 
    },
    prepareCartillaForEditing(rawCartilla) {

      const vacunasMap = new Map();
      rawCartilla.vacunas.forEach(v => {
        vacunasMap.set(v.nombre, { ...v, aplicada: true });
      });

      const processedVacunas = this.allVacunasNames.map(vacunaName => {
        if (vacunasMap.has(vacunaName)) {
          return vacunasMap.get(vacunaName);
        } else {
          return { nombre: vacunaName, aplicada: false, fechaAplicacion: '', proximaDosis: '' };
        }
      });
      return { ...rawCartilla, vacunas: processedVacunas };
    },
    async guardarCambios() {
      if (!this.selectedCartilla) {
        alert('No hay ninguna cartilla seleccionada para guardar.');
        return;
      }

      const vacunasToUpdate = this.selectedCartilla.vacunas.filter(v => v.aplicada || v.fechaAplicacion || v.proximaDosis);

      const updatedCartillaData = {
        cartillaId: this.selectedCartilla.id,
        vacunas: vacunasToUpdate.map(v => ({
          nombre: v.nombre,
          aplicada: v.aplicada,
          fechaAplicacion: v.fechaAplicacion,
          proximaDosis: v.proximaDosis,
        })),
      };

      console.log('Datos a actualizar:', updatedCartillaData);

      alert('Cambios guardados en la cartilla (simulado).');
      this.selectedCartillaId = null;
      this.selectedCartilla = null;
      this.buscarCartillas(); 
    },
  },
};
</script>

<style scoped>

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

.cartilla-summary-card {
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 15px;
  margin-bottom: 10px;
  cursor: pointer;
  transition: background-color 0.2s ease, border-color 0.2s ease;
}

.cartilla-summary-card:hover {
  background-color: #e2f0fb;
  border-color: #a4d8ff;
}

.cartilla-summary-card.selected-cartilla {
  background-color: #e3f2fd !important;
  border-color: #3498db !important;
  box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
  font-weight: bold;
}

.cartilla-summary-card h4 {
  margin-top: 0;
  color: #333;
}

.cartilla-summary-card p {
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

/* Estilos para el formulario de edición de vacunas  */
.vaccine-row {
  display: grid;
  grid-template-columns: 2fr 1.5fr 1.5fr; 
  gap: 15px;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px dashed #eee;
}

.vaccine-row.header-row {
  font-weight: bold;
  border-bottom: 2px solid #3498db;
  padding-bottom: 15px;
  margin-bottom: 10px;
  color: #34495e;
}

.vaccine-row label {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

.vaccine-row input[type="checkbox"] {
  transform: scale(1.2); 
}

.vaccine-row input[type="date"] {
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 0.95em;
  box-sizing: border-box;
  width: 100%; 
}

.vaccine-row input[type="date"]:disabled {
  background-color: #f0f0f0;
  cursor: not-allowed;
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
}
</style>