<template>
  <div class="vet-main-content scroll-sections">
    <div class="vet-header">
      <h1>Nueva Cartilla de Vacunación</h1>
      <p class="text-muted">Inicia una nueva cartilla para una mascota.</p>
    </div>

    <div class="dividir-pantalla">
      <div class="vet-search-section">
        <h3><i class="fas fa-search"></i> Buscar mascotas</h3>
        <label for="dni">DNI del Propietario:</label>
        <input
          type="text"
          id="dni"
          class="vet-form-control"
          placeholder="Ingrese DNI"
          v-model="searchDni"
          @keyup.enter="buscarMascotas"
        />
        <button @click="buscarMascotas" class="vet-btn">
          <i class="fas fa-search"></i> Buscar
        </button>

        <div id="lista-mascotas">
          <MascotaCard
            v-for="mascota in mascotasEncontradas"
            :key="mascota.id"
            :mascota="mascota"
            :selectedMascotaId="selectedMascotaId"
            @select-mascota="seleccionarMascota"
          />
          <p v-if="mascotasEncontradas.length === 0 && searchDni.length > 0 && searchAttempted">
            No se encontraron mascotas para el DNI ingresado.
          </p>
        </div>
      </div>

      <div class="vet-vaccine-form-section">
        <h3><i class="fas fa-syringe"></i> Registro de Vacunas</h3>
        <form @submit.prevent="guardarCartilla" id="form-vacunas">
          <div class="vaccine-row header-row">
            <div>Vacuna</div>
            <div>Fecha de aplicación</div>
            <div>Próxima dosis</div>
          </div>
          <div v-for="(vacuna, index) in vacunas" :key="index" class="vaccine-row">
            <label :for="`vacuna_${index}`">
              <input
                type="checkbox"
                :id="`vacuna_${index}`"
                v-model="vacuna.aplicada"
              />
              {{ vacuna.nombre }}
            </label>
            <input
              type="date"
              :id="`vacuna_${index}_fecha`"
              v-model="vacuna.fechaAplicacion"
              :disabled="!vacuna.aplicada"
              placeholder="Fecha de aplicación"
            />
            <input
              type="date"
              :id="`vacuna_${index}_proxima`"
              v-model="vacuna.proximaDosis"
              :disabled="!vacuna.aplicada"
              placeholder="Próxima dosis"
            />
          </div>
          <button type="submit" class="vet-btn">
            <i class="fas fa-save"></i> Guardar Cartilla
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import MascotaCard from '../components/MascotaCard.vue';

export default {
  name: 'NuevaCartillaPage',
  components: {
    MascotaCard,
  },
  data() {
    return {
      searchDni: '',
      mascotasEncontradas: [],
      selectedMascotaId: null, // ID de la mascota seleccionada
      searchAttempted: false, // Para controlar el mensaje "No se encontraron mascotas"
      vacunas: [
        { nombre: 'Parvovirus', aplicada: false, fechaAplicacion: '', proximaDosis: '' },
        { nombre: 'Coronavirus', aplicada: false, fechaAplicacion: '', proximaDosis: '' },
        { nombre: 'Distemper (Moquillo)', aplicada: false, fechaAplicacion: '', proximaDosis: '' },
        { nombre: 'Hepatitis Canina', aplicada: false, fechaAplicacion: '', proximaDosis: '' },
        { nombre: 'Parainfluenza', aplicada: false, fechaAplicacion: '', proximaDosis: '' },
        { nombre: 'Leptospira', aplicada: false, fechaAplicacion: '', proximaDosis: '' },
        { nombre: 'Tos de las perreras', aplicada: false, fechaAplicacion: '', proximaDosis: '' },
        { nombre: 'Rabia', aplicada: false, fechaAplicacion: '', proximaDosis: '' },
        { nombre: 'Leucemia Felina', aplicada: false, fechaAplicacion: '', proximaDosis: '' },
        { nombre: 'Rinotraqueitis', aplicada: false, fechaAplicacion: '', proximaDosis: '' },
        { nombre: 'Calicivirus', aplicada: false, fechaAplicacion: '', proximaDosis: '' },
        { nombre: 'Panleucopenia', aplicada: false, fechaAplicacion: '', proximaDosis: '' },
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
  watch: {
    // Observa los cambios en cada vacuna.aplicada
    'vacunas': {
      handler(newVacunas) {
        newVacunas.forEach(vacuna => {
          if (vacuna.aplicada && !vacuna.fechaAplicacion) {
            // Si se marca y no hay fecha, establecer fecha actual
            const today = new Date().toISOString().split('T')[0];
            vacuna.fechaAplicacion = today;
            // Calcular próxima dosis (1 año después por defecto)
            const nextYear = new Date();
            nextYear.setFullYear(nextYear.getFullYear() + 1);
            vacuna.proximaDosis = nextYear.toISOString().split('T')[0];
          } else if (!vacuna.aplicada) {
            // Si se desmarca, limpiar fechas
            vacuna.fechaAplicacion = '';
            vacuna.proximaDosis = '';
          }
        });
      },
      deep: true // Observar cambios profundos en el array y objetos
    }
  },
  methods: {
    async buscarMascotas() {
      this.searchAttempted = true;
      this.mascotasEncontradas = [];
      this.selectedMascotaId = null; // Reiniciar selección al buscar
      
      if (!this.searchDni) {
        alert('Por favor, ingrese el DNI del propietario.');
        return;
      }

      console.log(`Buscando mascotas para DNI: ${this.searchDni}`);

      // Simulación de llamada a API (backend PHP)

      // Usamos los datos estáticos de ejemplo por ahora
      const results = this.mockMascotasDB.filter(
        (m) => m.dniPropietario === this.searchDni
      );
      this.mascotasEncontradas = results;
    },
    seleccionarMascota(mascotaId) {
      this.selectedMascotaId = mascotaId;
      const selectedMascota = this.mascotasEncontradas.find(m => m.id === mascotaId);
      console.log(`Mascota seleccionada: ${selectedMascota.nombre} (ID: ${mascotaId})`);

    },
    async guardarCartilla() {
      if (this.selectedMascotaId === null) {
        alert('Por favor, seleccione una mascota para guardar la cartilla.');
        return;
      }

      const vacunasAplicadas = this.vacunas.filter(v => v.aplicada);

      if (vacunasAplicadas.length === 0) {
        alert('Por favor, seleccione al menos una vacuna aplicada.');
        return;
      }

      const datosCartilla = {
        mascotaId: this.selectedMascotaId,
        vacunas: vacunasAplicadas.map(v => ({
          nombre: v.nombre,
          fechaAplicacion: v.fechaAplicacion,
          proximaDosis: v.proximaDosis,
        })),
      };

      console.log('Datos a guardar:', datosCartilla);

     

      alert('Cartilla de vacunación guardada (simulado).');
      this.resetForm();
    },
    resetForm() {
      this.searchDni = '';
      this.mascotasEncontradas = [];
      this.selectedMascotaId = null;
      this.searchAttempted = false;
      this.vacunas.forEach(vacuna => {
        vacuna.aplicada = false;
        vacuna.fechaAplicacion = '';
        vacuna.proximaDosis = '';
      });
    }
  },
};
</script>

<style scoped>

.dividir-pantalla {
  display: flex;
  justify-content: space-between;
  gap: 30px;
  padding: 20px;
  flex-wrap: wrap; 
}

.vet-search-section,
.vet-vaccine-form-section {
  background-color: #ffffff;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  flex: 1; 
  min-width: 350px; 
}

.vet-search-section h3,
.vet-vaccine-form-section h3 {
  color: #34495e;
  margin-bottom: 25px;
  font-size: 1.8em;
  border-bottom: 2px solid #eee;
  padding-bottom: 15px;
}

.vet-search-section h3 i,
.vet-vaccine-form-section h3 i {
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

/* Estilos específicos para el formulario de vacunas */
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

@media (max-width: 768px) {
  .dividir-pantalla {
    flex-direction: column;
  }
  .vet-search-section,
  .vet-vaccine-form-section {
    min-width: unset; 
    width: 100%; 
  }
}
</style>