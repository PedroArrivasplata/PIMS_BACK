import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import agenda from '../views/agenda.vue';
import CartillaVacunacion from '../views/CartillaVacunacion.vue';
import NuevaCartilla from '../views/NuevaCartilla.vue'; 
import ModificarCartilla from '../views/ModificarCartilla.vue'; 
import ConsultaMedica from '../views/ConsultaMedica.vue';
import NuevoRegistroConsulta from '../views/NuevoRegistroConsulta.vue'; 
import ModificarConsulta from '../views/ModificarConsulta.vue'; 
import ExamenesMedicos from '../views/ExamenesMedicos.vue';
import EditarExamenMedico from '@/views/EditarExamenMedico.vue';
import HistorialMedico from '../views/HistorialMedico.vue';
import Login from '../views/Login.vue';
import RegistroUsuario from '../views/RegistroUsuario.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
    path: '/agenda',
    name: 'agenda',
    component: agenda,
  },
  {
    path: '/cartilla-vacunacion',
    name: 'CartillaVacunacion',
    component: CartillaVacunacion,
  },
  {
    path: '/cartilla-vacunacion/nueva', 
    name: 'NuevaCartilla',
    component: NuevaCartilla,
  },
  {
    path: '/cartilla-vacunacion/modificar', 
    name: 'ModificarCartilla',
    component: ModificarCartilla,
  },
  {
    path: '/consulta-medica',
    name: 'ConsultaMedica',
    component: ConsultaMedica,
  },
   {
    path: '/consulta-medica/nuevo-registro', 
    name: 'NuevoRegistroConsulta',
    component: NuevoRegistroConsulta,
  },
  {
    path: '/consulta-medica/modificar', 
    name: 'ModificarConsulta',
    component: ModificarConsulta,
  },
  {
    path: '/examenes-medicos',
    name: 'ExamenesMedicos',
    component: ExamenesMedicos,
  },
  {
    path: '/editar-examen-medico/:id', 
    name: 'EditarExamenMedico',
    component: EditarExamenMedico,
    props: true 
  },
  {
    path: '/historial-medico',
    name: 'HistorialMedico',
    component: HistorialMedico,
  },
  {
    path: '/login', 
    name: 'login',
    component: Login
  },
   {
    path: '/registrar-nuevo-usuario', 
    name: 'registrarNuevoUsuario',
    component: RegistroUsuario,
  }
  ],
})

export default router
