<template>
  <div class="vet-dashboard">
    <div class="vet-sidebar">
      <div class="vet-sidebar-header">
        <img src="@/assets/images/happy_dog.png" alt="Medical Vice Logo">
        <h3>Medical Vice</h3>
        <p>Panel Veterinario</p>
      </div>
      <ul class="vet-sidebar-nav">
        <li><a href="1_inicio.php"><i class="fas fa-home"></i> Inicio</a></li>
        <li><a href="2_agenda.php"><i class="fas fa-calendar-alt"></i> Agenda</a></li>
        <li><a href="3_0_cartilla_vacunacion.php"><i class="fas fa-syringe"></i> Cartilla de Vacunación</a></li>
        <li><a href="4_0_consulta_medica.php"><i class="fas fa-stethoscope"></i> Consulta Médica</a></li>
        <li><a href="5_0_examenes_medicos.php"><i class="fas fa-microscope"></i> Exámenes Médicos</a></li>
        <li><a href="6_historial_medico.php" class="active"><i class="fas fa-file-medical"></i> Historial Médico</a></li>
      </ul>
      <a href="../pages/login.php" class="vet-logout">
        <i class="fas fa-sign-out-alt"></i>
        Cerrar sesión
      </a>
    </div>

    <div class="vet-main-content scroll-sections">
      <div class="vet-header">
        <div>
          <h1>Historial Médico</h1>
          <p class="text-muted">Consulta integral del historial clínico de tus pacientes</p>
        </div>
      </div>

      <div class="vet-card">
        <div class="vet-card-header">
          <h3><i class="fas fa-filter"></i> Filtros de Búsqueda</h3>
        </div>
        <div class="vet-form-row">
          <div class="vet-form-group">
            <label for="filtro-nombre">Nombre de la Mascota</label>
            <input type="text" id="filtro-nombre" class="vet-form-control" placeholder="Ej: Max" v-model="filtros.nombreMascota">
          </div>
          <div class="vet-form-group">
            <label for="filtro-dni">DNI del Propietario</label>
            <input type="text" id="filtro-dni" class="vet-form-control" placeholder="Ej: 12345678" v-model="filtros.dniPropietario">
          </div>
          <div class="vet-form-group" style="align-self: flex-end;">
            <button class="vet-btn" @click="buscarRegistros">
              <i class="fas fa-search"></i> Buscar
            </button>
          </div>
        </div>
      </div>

      <div class="vet-card" style="margin-top: 20px;">
        <div class="vet-card-header">
          <h3><i class="fas fa-search"></i> Resultados</h3>
        </div>
        <div class="vet-pets-list">
          <div class="vet-pet-item" v-for="mascota in mascotasFiltradas" :key="mascota.id">
            <label>
              <input type="radio" name="paciente" :value="mascota.id" v-model="idMascotaSeleccionada" @change="seleccionarMascota(mascota)">
              <div class="d-flex align-items-center">
                <img :src="mascota.avatar" :alt="mascota.nombre" class="vet-pet-avatar">
                <div>
                  <h4>{{ mascota.nombre }}</h4>
                  <p class="text-muted">{{ mascota.raza }} - {{ mascota.edad }} años</p>
                  <p><strong>Propietario:</strong> {{ mascota.propietario.nombre }} (DNI: {{ mascota.propietario.dni }})</p>
                  <p><strong>ID Mascota:</strong> {{ mascota.id }}</p>
                </div>
              </div>
            </label>
          </div>
          <div v-if="mascotasFiltradas.length === 0">
              <p class="text-muted" style="text-align: center;">No se encontraron mascotas.</p>
          </div>
        </div>
      </div>

      <div class="vet-card" style="margin-top: 20px;" v-if="mascotaSeleccionada">
        <div class="vet-tabs" style="display: flex; border-bottom: 1px solid #eee;">
          <button
            class="vet-tab"
            :class="{ 'active': pestañaActiva === 'vacunacion' }"
            @click="establecerPestanaActiva('vacunacion')"
            data-tab="vacunacion"
          >Vacunación</button>
          <button
            class="vet-tab"
            :class="{ 'active': pestañaActiva === 'consultas' }"
            @click="establecerPestanaActiva('consultas')"
            data-tab="consultas"
          >Consultas Médicas</button>
          <button
            class="vet-tab"
            :class="{ 'active': pestañaActiva === 'examenes' }"
            @click="establecerPestanaActiva('examenes')"
            data-tab="examenes"
          >Exámenes Médicos</button>
        </div>

        <div class="vet-tab-content" id="vacunacion" v-show="pestañaActiva === 'vacunacion'">
          <div class="vet-card-section">
            <div class="vet-vaccine-grid">
              <div class="vet-vaccine-category">
                <h4>Vacunas Obligatorias</h4>
                <div class="vet-vaccine-item" v-for="(vacuna, index) in mascotaSeleccionada.historialMedico.vacunacion.obligatorias" :key="`obligatoria-${index}`">
                  <div class="vet-checkbox">
                    <input type="checkbox" :checked="vacuna.recibida" disabled>
                    <span class="vet-checkmark"></span>
                    <span>{{ vacuna.nombre }}</span>
                  </div>
                  <div class="vet-vaccine-details">
                    <span v-if="vacuna.recibida">{{ vacuna.fechaRecibida }}</span>
                    <span v-if="vacuna.proximaDosis">Próxima: {{ vacuna.proximaDosis }}</span>
                    <span v-else class="vet-missing">Pendiente</span>
                  </div>
                </div>
              </div>

              <div class="vet-vaccine-category">
                <h4>Vacunas Opcionales</h4>
                <div class="vet-vaccine-item" v-for="(vacuna, index) in mascotaSeleccionada.historialMedico.vacunacion.opcionales" :key="`opcional-${index}`">
                  <div class="vet-checkbox">
                    <input type="checkbox" :checked="vacuna.recibida" disabled>
                    <span class="vet-checkmark"></span>
                    <span>{{ vacuna.nombre }}</span>
                  </div>
                  <div class="vet-vaccine-details">
                    <span v-if="vacuna.recibida">{{ vacuna.fechaRecibida }}</span>
                    <span v-if="vacuna.proximaDosis">Próxima: {{ vacuna.proximaDosis }}</span>
                    <span v-else class="vet-missing">Pendiente</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="vet-tab-content" id="consultas" v-show="pestañaActiva === 'consultas'">
          <div class="vet-card-section">
            <div class="vet-consultas-list">
              <div class="vet-card" style="padding: 15px; margin-bottom: 15px;" v-for="(consulta, index) in mascotaSeleccionada.historialMedico.consultas" :key="`consulta-${index}`">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h5>{{ consulta.titulo }}</h5>
                    <p class="text-muted">{{ consulta.fecha }} - {{ consulta.doctor }}</p>
                  </div>
                  <button class="vet-btn-icon" @click="alternarDetallesConsulta(index)">
                    <i :class="['fas', consulta.expandida ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
                  </button>
                </div>
                <div class="consulta-detalle" :style="{ display: consulta.expandida ? 'block' : 'none' }" style="margin-top: 10px;">
                  <p><strong>Síntomas:</strong> {{ consulta.sintomas }}</p>
                  <p><strong>Diagnóstico:</strong> {{ consulta.diagnostico }}</p>
                  <p><strong>Tratamiento:</strong> {{ consulta.tratamiento }}</p>
                  <p><strong>Exámenes solicitados:</strong> {{ consulta.examenesSolicitados }}</p>
                  <p><strong>Observaciones:</strong> {{ consulta.observaciones }}</p>
                </div>
              </div>
              <div v-if="mascotaSeleccionada.historialMedico.consultas.length === 0">
                <p class="text-muted" style="text-align: center;">No hay consultas médicas registradas para esta mascota.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="vet-tab-content" id="examenes" v-show="pestañaActiva === 'examenes'">
          <div class="vet-card-section">
            <div class="vet-examenes-list">
              <div class="vet-card" style="padding: 15px; margin-bottom: 15px;" v-for="(examen, index) in mascotaSeleccionada.historialMedico.examenes" :key="`examen-${index}`">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h5>{{ examen.titulo }}</h5>
                    <p class="text-muted">{{ examen.fecha }} - Asociado a Consulta #{{ examen.idConsultaAsociada }}</p>
                  </div>
                  <div>
                    <a href="#" class="vet-btn-icon" title="Ver resultado" @click.prevent="verResultadoExamen(examen)">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="#" class="vet-btn-icon" title="Descargar" @click.prevent="descargarResultadoExamen(examen)">
                      <i class="fas fa-download"></i>
                    </a>
                  </div>
                </div>
                <div style="margin-top: 10px;">
                  <p><strong>Resultados:</strong> {{ examen.resultados }}</p>
                  <p><strong>Observaciones:</strong> {{ examen.observaciones }}</p>
                </div>
              </div>
              <div v-if="mascotaSeleccionada.historialMedico.examenes.length === 0">
                <p class="text-muted" style="text-align: center;">No hay exámenes médicos registrados para esta mascota.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'HistorialMedico',
  data() {
    return {
      pestañaActiva: 'vacunacion', 
      filtros: {
        nombreMascota: '',
        dniPropietario: ''
      },
      idMascotaSeleccionada: 'MV-001', 
      mascotas: [
        {
          id: 'MV-001',
          nombre: 'Max',
          raza: 'Labrador Retriever',
          edad: 3,
          avatar: '@/assets/images/dog_avatar.jpg',
          propietario: {
            nombre: 'Juan Pérez',
            dni: '12345678'
          },
          historialMedico: {
            vacunacion: {
              obligatorias: [
                { nombre: 'Rabia', recibida: true, fechaRecibida: '10/03/2024', proximaDosis: '10/03/2025' },
                { nombre: 'Moquillo', recibida: true, fechaRecibida: '15/01/2025', proximaDosis: '15/01/2026' }
              ],
              opcionales: [
                { nombre: 'Leptospirosis', recibida: true, fechaRecibida: '15/01/2025', proximaDosis: '15/07/2025' },
                { nombre: 'Bordetella', recibida: false, fechaRecibida: null, proximaDosis: null }
              ]
            },
            consultas: [
              {
                titulo: 'Consulta de rutina',
                fecha: '15/04/2025',
                doctor: 'Dr. Veterinario',
                sintomas: 'Ninguno reportado (control preventivo)',
                diagnostico: 'Saludable, peso ideal',
                tratamiento: 'Continuar con dieta actual',
                examenesSolicitados: 'Hematología completa',
                observaciones: 'Se recomienda ejercicio diario de 30 minutos',
                expandida: false 
              },
              {
                titulo: 'Revisión post-vacunación',
                fecha: '10/03/2025',
                doctor: 'Dr. Veterinario',
                sintomas: 'Ligera inflamación en zona de inyección',
                diagnostico: 'Reacción normal a vacuna',
                tratamiento: 'Aplicación de compresa fría',
                examenesSolicitados: 'Ninguno',
                observaciones: 'Reacción desapareció en 24 horas',
                expandida: false 
              }
            ],
            examenes: [
              {
                titulo: 'Hematología completa',
                fecha: '15/04/2025',
                idConsultaAsociada: '001',
                resultados: 'Valores dentro de parámetros normales',
                observaciones: 'Ligera elevación en glóbulos blancos, posiblemente por estrés',
                archivo: '/path/to/hematologia_max_15042025.pdf' 
              },
              {
                titulo: 'Radiografía torácica',
                fecha: '10/03/2024',
                idConsultaAsociada: '002',
                resultados: 'No se observan anomalías',
                observaciones: 'Estructura ósea y pulmonar normal',
                archivo: '/path/to/radiografia_max_10032024.pdf' 
              }
            ]
          }
        },
        {
          id: 'MV-002',
          nombre: 'Luna',
          raza: 'Pastor Alemán',
          edad: 5,
          avatar: '@/assets/images/dog_avatar.jpg', 
          propietario: {
            nombre: 'María García',
            dni: '87654321'
          },
          historialMedico: {
            vacunacion: {
              obligatorias: [
                { nombre: 'Rabia', recibida: true, fechaRecibida: '01/02/2025', proximaDosis: '01/02/2026' },
                { nombre: 'Moquillo', recibida: true, fechaRecibida: '05/03/2025', proximaDosis: '05/03/2026' }
              ],
              opcionales: [
                { nombre: 'Bordetella', recibida: true, fechaRecibida: '10/01/2025', proximaDosis: '10/01/2026' }
              ]
            },
            consultas: [
              {
                titulo: 'Control anual',
                fecha: '20/05/2025',
                doctor: 'Dr. Veterinario',
                sintomas: 'Revisión general',
                diagnostico: 'Buena salud',
                tratamiento: 'Ninguno',
                examenesSolicitados: 'Coprologico',
                observaciones: 'Mantener peso y dieta',
                expandida: false
              }
            ],
            examenes: [
               {
                titulo: 'Coprológico',
                fecha: '20/05/2025',
                idConsultaAsociada: '003',
                resultados: 'Sin parásitos',
                observaciones: 'Resultados normales',
                archivo: '/path/to/coprologico_luna_20052025.pdf'
              }
            ]
          }
        }
      ],
      mascotaSeleccionada: null 
    };
  },
  computed: {

    mascotasFiltradas() {
      const filtroNombre = this.filtros.nombreMascota.toLowerCase();
      const filtroDni = this.filtros.dniPropietario.toLowerCase();

      return this.mascotas.filter(mascota => {
        const coincideNombre = mascota.nombre.toLowerCase().includes(filtroNombre);
        const coincideDni = mascota.propietario.dni.includes(filtroDni);
        return coincideNombre && coincideDni;
      });
    }
  },
  methods: {
    establecerPestanaActiva(idTab) {
      this.pestañaActiva = idTab;
    },
    alternarDetallesConsulta(index) {
      this.mascotaSeleccionada.historialMedico.consultas[index].expandida = !this.mascotaSeleccionada.historialMedico.consultas[index].expandida;
    },
    buscarRegistros() {
      console.log('Buscando con filtros:', this.filtros);
      if (this.mascotasFiltradas.length > 0) {
        this.idMascotaSeleccionada = this.mascotasFiltradas[0].id;
        this.seleccionarMascota(this.mascotasFiltradas[0]);
      } else {
        this.mascotaSeleccionada = null;
      }
    },
    seleccionarMascota(mascota) {
      this.mascotaSeleccionada = mascota;
      this.pestañaActiva = 'vacunacion';
      this.mascotaSeleccionada.historialMedico.consultas.forEach(c => {
        c.expandida = false;
      });
      console.log('Mascota seleccionada:', this.mascotaSeleccionada.nombre);
    },
    verResultadoExamen(examen) {
      
      if (examen.archivo) {
        window.open(examen.archivo, '_blank');
      } else {
        alert('No hay archivo disponible para este examen.');
      }
      console.log('Viendo resultado de examen para:', examen.titulo);
    },
    descargarResultadoExamen(examen) {

      if (examen.archivo) {
        const link = document.createElement('a');
        link.href = examen.archivo;
        link.download = `${examen.titulo.replace(/\s/g, '_')}_${this.mascotaSeleccionada.nombre}.pdf`; 
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      } else {
        alert('No hay archivo disponible para descargar.');
      }
      console.log('Descargando resultado de examen para:', examen.titulo);
    },
   
    obtenerHistorialMedico(idMascota) {
       console.log(`Obteniendo historial médico para la mascota ID: ${idMascota} desde el backend (simulado)`);
    }
  },
  mounted() {
 
    if (this.idMascotaSeleccionada) {
      const mascotaInicial = this.mascotas.find(mascota => mascota.id === this.idMascotaSeleccionada);
      if (mascotaInicial) {
        this.seleccionarMascota(mascotaInicial);
      }
    }
  }
};
</script>

<style scoped>

body {
    font-family: Arial, sans-serif;
    margin: 0;
    background-color: #f4f7f6;
}

.vet-dashboard {
    display: flex;
    min-height: 100vh;
}

.vet-sidebar {
    width: 250px;
    background-color: #2c3e50;
    color: #ecf0f1;
    padding: 20px;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
}

.vet-sidebar-header {
    text-align: center;
    margin-bottom: 30px;
}

.vet-sidebar-header img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin-bottom: 10px;
    object-fit: cover;
}

.vet-sidebar-header h3 {
    margin: 5px 0;
    color: #ecf0f1;
}

.vet-sidebar-header p {
    font-size: 0.9em;
    color: #bdc3c7;
}

.vet-sidebar-nav {
    list-style: none;
    padding: 0;
    margin: 0;
}

.vet-sidebar-nav li {
    margin-bottom: 10px;
}

.vet-sidebar-nav a {
    display: block;
    color: #ecf0f1;
    text-decoration: none;
    padding: 10px 15px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.vet-sidebar-nav a:hover,
.vet-sidebar-nav a.active {
    background-color: #34495e;
}

.vet-sidebar-nav a i {
    margin-right: 10px;
}

.vet-logout {
    display: block;
    margin-top: 50px;
    color: #e74c3c;
    text-decoration: none;
    padding: 10px 15px;
    border-radius: 5px;
    border: 1px solid #e74c3c;
    text-align: center;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.vet-logout:hover {
    background-color: #e74c3c;
    color: #fff;
}

.vet-main-content {
    flex-grow: 1;
    padding: 20px;
    overflow-y: auto;
}

.vet-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.vet-header h1 {
    margin: 0;
    color: #2c3e50;
}

.text-muted {
    color: #7f8c8d;
}

.vet-card {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    padding: 20px;
    margin-bottom: 20px;
}

.vet-card-header {
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
}

.vet-card-header h3 {
    margin: 0;
    color: #34495e;
}

.vet-card-header i {
    margin-right: 10px;
    color: #3498db;
}

.vet-form-row {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.vet-form-group {
    flex: 1;
    min-width: 200px;
}

.vet-form-group label {
    display: block;
    margin-bottom: 5px;
    color: #555;
    font-size: 0.9em;
}

.vet-form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}

.vet-btn {
    background-color: #3498db;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1em;
    transition: background-color 0.3s ease;
}

.vet-btn:hover {
    background-color: #2980b9;
}

.vet-btn i {
    margin-right: 5px;
}

.vet-pets-list .vet-pet-item {
    border: 1px solid #eee;
    border-radius: 8px;
    margin-bottom: 10px;
    padding: 15px;
    transition: box-shadow 0.2s ease;
}

.vet-pets-list .vet-pet-item:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.vet-pets-list .vet-pet-item label {
    display: flex;
    align-items: center;
    cursor: pointer;
    width: 100%;
}

.vet-pets-list .vet-pet-item input[type="radio"] {
    margin-right: 15px;
}

.vet-pet-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    margin-right: 15px;
    object-fit: cover;
}

.vet-pets-list .vet-pet-item h4 {
    margin: 0 0 5px 0;
    color: #34495e;
}

.vet-pets-list .vet-pet-item p {
    margin: 0;
    font-size: 0.9em;
    line-height: 1.4;
}

.d-flex {
    display: flex;
}

.align-items-center {
    align-items: center;
}

.justify-content-between {
    justify-content: space-between;
}

/* Tabs styling */
.vet-tabs {
    display: flex;
    border-bottom: 1px solid #eee;
    margin-bottom: 20px;
}

.vet-tab {
    background-color: #f8f8f8;
    border: none;
    padding: 12px 20px;
    cursor: pointer;
    font-size: 1em;
    color: #555;
    transition: all 0.3s ease;
    border-bottom: 3px solid transparent; 
}

.vet-tab:hover {
    background-color: #e9e9e9;
}

.vet-tab.active {
    background-color: #fff;
    color: #3498db;
    border-bottom: 3px solid #3498db;
    font-weight: bold;
}

.vet-tab-content {
    padding-top: 20px;
}

.vet-card-section {
    padding: 15px;
    border: 1px solid #eee;
    border-radius: 8px;
    background-color: #fff;
}


.vet-vaccine-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.vet-vaccine-category h4 {
    color: #34495e;
    margin-top: 0;
    margin-bottom: 15px;
    padding-bottom: 5px;
    border-bottom: 1px dashed #eee;
}

.vet-vaccine-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f9f9f9;
}

.vet-vaccine-item:last-child {
    border-bottom: none;
}

.vet-checkbox {
    display: flex;
    align-items: center;
}

.vet-checkbox input[type="checkbox"] {
    margin-right: 10px;
    width: 20px;
    height: 20px;
    accent-color: #2ecc71;
}

.vet-checkmark {
}

.vet-vaccine-details {
    font-size: 0.9em;
    color: #7f8c8d;
    text-align: right;
}

.vet-missing {
    color: #e74c3c;
    font-weight: bold;
}

.vet-consultas-list .vet-card,
.vet-examenes-list .vet-card {
    border: 1px solid #eee;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 8px;
    background-color: #fff;
}

.vet-consultas-list .vet-card h5,
.vet-examenes-list .vet-card h5 {
    margin: 0 0 5px 0;
    color: #34495e;
}

.vet-btn-icon {
    background: none;
    border: none;
    color: #3498db;
    cursor: pointer;
    font-size: 1.2em;
    padding: 5px;
    border-radius: 50%;
    transition: background-color 0.2s ease;
}

.vet-btn-icon:hover {
    background-color: #ecf0f1;
}

.consulta-detalle p {
    margin: 5px 0;
    font-size: 0.95em;
    color: #444;
}

.consulta-detalle strong {
    color: #333;
}
</style>