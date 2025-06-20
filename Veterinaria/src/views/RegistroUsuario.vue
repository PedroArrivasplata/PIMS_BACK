<template>
  <div class="login-body">
    <div class="login-wrapper d-flex justify-content-center align-items-center">
      <main class="login-container animate__animated animate__fadeInDown mx-3">
        <div class="text-center mb-3">
          <i class="bi bi-person-plus-fill login-icon"></i>
        </div>
        <h1 class="text-center fs-2 fw-bold mb-2">Crear cuenta</h1>
        <h2 class="text-center fs-5 fw-semibold text-primary mb-4">Medical Vice</h2>

        <div v-if="mensaje" class="alert-container">
          <div :class="{'alert-success': mensajeClase === 'success', 'alert-danger': mensajeClase === 'danger', 'alert-warning': mensajeClase === 'warning'}"
               class="alert alert-dismissible fade show" role="alert">
            <span v-html="mensaje"></span> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar" @click="mensaje = ''"></button>
          </div>
        </div>

        <form @submit.prevent="registrarUsuario" class="mb-3">
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="bi bi-credit-card-2-front"></i></span>
            <input type="text" class="form-control" v-model="dni" placeholder="DNI" required />
          </div>

          <div class="input-group mb-3">
            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
            <input type="text" class="form-control" v-model="nombres" placeholder="Nombres" required />
          </div>

          <div class="input-group mb-3">
            <span class="input-group-text"><i class="bi bi-person-lines-fill"></i></span>
            <input type="text" class="form-control" v-model="apellidos" placeholder="Apellidos" required />
          </div>

          <div class="input-group mb-3">
            <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
            <input type="email" class="form-control" v-model="correo" placeholder="Correo electrónico" required />
          </div>

          <div class="input-group mb-3">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" class="form-control" v-model="clave" placeholder="Contraseña" required />
          </div>

          <div class="input-group mb-4">
            <span class="input-group-text"><i class="bi bi-person-badge-fill"></i></span>
            <select class="form-select" v-model="tipo_usuario_id" required>
              <option value="" disabled selected>Selecciona tipo de usuario</option>
              <option v-for="tipo in tiposUsuario" :key="tipo.id_tipo_usuario" :value="tipo.id_tipo_usuario">
                {{ tipo.nombre_usuario }}
              </option>
            </select>
          </div>

          <button type="submit" class="btn login-btn w-100 fw-semibold shadow-sm mb-3">
            Registrar
          </button>
        </form>

        <div class="text-center">
          <span>¿Ya tienes cuenta?</span>
          <a href="login.php" class="text-decoration-none login-link fw-semibold">Inicia sesión</a>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RegistrarNuevoUsuario', 
  data() {
    return {
      dni: '',
      nombres: '',
      apellidos: '',
      correo: '',
      clave: '',
      tipo_usuario_id: '',
      mensaje: '',
      mensajeClase: '', 
      tiposUsuario: [] 
    };
  },
  methods: {
    async obtenerTiposDeUsuario() { 
      try {
       
        const response = await fetch('http://localhost/repo_Oficial/PetInformationManagementSystem/app/controllers/api_tipos_usuario.php');
        if (response.ok) {
          const data = await response.json();
          this.tiposUsuario = data.tipos_usuario || []; 
        } else {
          console.error('Error al obtener tipos de usuario:', response.statusText);
          this.mensaje = 'No se pudieron cargar los tipos de usuario.';
          this.mensajeClase = 'danger';
        }
      } catch (error) {
        console.error('Error de red al obtener tipos de usuario:', error);
        this.mensaje = 'Error de conexión al cargar los tipos de usuario.';
        this.mensajeClase = 'danger';
      }
    },
    async registrarUsuario() {
      if (!this.dni || !this.nombres || !this.apellidos || !this.correo || !this.clave || !this.tipo_usuario_id) {
        this.mensaje = 'Todos los campos son obligatorios.';
        this.mensajeClase = 'warning';
        return;
      }

      const data = {
        dni: this.dni,
        nombres: this.nombres,
        apellidos: this.apellidos,
        correo: this.correo,
        clave: this.clave,
        tipo_usuario_id: this.tipo_usuario_id,
        estado_logico_id: 1 
      };

      try {
        const response = await fetch('http://localhost/repo_Oficial/PetInformationManagementSystem/app/controllers/api_usuarios.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(data)
        });

        const respuesta = await response.json();

        if (response.status === 201) { 
          this.mensaje = 'Registro exitoso. Ya puedes <a href="login.php" class="alert-link">iniciar sesión</a>.';
          this.mensajeClase = 'success';
          setTimeout(() => {
            window.location.href = 'login.php'; 
          }, 2000);
        } else {
          this.mensaje = 'Error: ' + (respuesta.error || 'Error desconocido');
          this.mensajeClase = 'danger';
        }
      } catch (error) {
        console.error('Error al registrar usuario:', error);
        this.mensaje = 'Error de conexión. Inténtelo de nuevo.';
        this.mensajeClase = 'danger';
      }
    }
  },
  mounted() {

    this.obtenerTiposDeUsuario();
  }
};
</script>

<style scoped>
.login-body {
  font-family: Arial, sans-serif;
  margin: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f0f2f5;
}

.login-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  min-height: 100vh;
}

.login-container {
  background-color: #fff;
  padding: 40px;
  border-radius: 10px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 500px;
  z-index: 10;
}

</style>