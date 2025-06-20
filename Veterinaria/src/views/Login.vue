<template>
  <div class="login-body">
    <div v-if="mensaje" class="container mt-4">
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ mensaje }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar" @click="mensaje = ''"></button>
      </div>
    </div>

    <div id="background-image"></div>

    <div class="login-wrapper d-flex justify-content-center align-items-center">
      <div class="d-none d-md-block">
        <img src="@/assets/images/happy_dog.png" alt="perrito" class="mascota-img-side" />
      </div>

      <main class="login-container animate__animated animate__fadeInDown mx-3">
        <div class="text-center mb-3">
          <i class="bi bi-person-circle login-icon"></i>
        </div>
        <h1 class="text-center fs-2 fw-bold mb-2">Bienvenidos</h1>
        <h2 class="text-center fs-4 fw-bold mb-4 text-primary">a Medical Vice</h2>
        
        <form @submit.prevent="iniciarSesion">
          <div class="input-group mb-3">
            <span class="input-group-text login-input-icon">
              <i class="bi bi-person-fill"></i>
            </span>
            <input type="text" class="form-control" placeholder="Ingresa tu usuario" v-model="correo" required />
          </div>

          <div class="input-group mb-3">
            <span class="input-group-text login-input-icon">
              <i class="bi bi-lock-fill"></i>
            </span>
            <input type="password" class="form-control" placeholder="Ingresa tu contraseña" v-model="clave" required />
          </div>

          <button type="submit" class="btn login-btn w-100 fw-semibold shadow-sm mb-3">Iniciar Sesión</button>
        </form>
        <p class="text-center">
          ¿No tienes cuenta? <a href="../pages/registrar_nuevo_usuario.php" class="text-primary">Regístrate aquí</a>
        </p>
      </main>

      <div class="d-none d-md-block">
        <img src="@/assets/images/happy_cat.png" alt="gatito" class="mascota-img-side" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Login',
  data() {
    return {
      correo: '',
      clave: '',
      mensaje: ''
    };
  },
  methods: {
    async iniciarSesion() { 
      if (!this.correo || !this.clave) {
        this.mensaje = 'Debe ingresar usuario y contraseña.';
        return;
      }

      try {
        const response = await fetch('http://localhost/repo_Oficial/PetInformationManagementSystem/app/controllers/api_login.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ correo: this.correo, clave: this.clave })
        });

        const data = await response.json();

        if (response.ok) { 
          const usuario = data.usuario;
          if (usuario) {
       
            if (usuario.tipo_usuario === 'recepcionista') {
              window.location.href = '../pages/oficial.html';
            } else if (usuario.tipo_usuario === 'veterinario') {
              window.location.href = '../pages/1_inicio.php';
            } else {
              window.location.href = '../pages/oficial.php';
            }
          } else {
            this.mensaje = data.error || 'Respuesta de usuario inválida del servidor.';
          }
        } else {
          
          this.mensaje = data.error || 'Usuario o contraseña incorrectos.';
        }
      } catch (error) {
        console.error('Error al iniciar sesión:', error);
        this.mensaje = 'Error de conexión. Inténtelo de nuevo.';
      }
    }
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
  position: relative;
  overflow: hidden;
}

#background-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: url('@/assets/images/happy_dog.png');
  background-size: cover;
  background-position: center;
  filter: brightness(0.6);
  z-index: 0;
}

.login-wrapper {
  position: relative;
  z-index: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  height: 100%;
}

.mascota-img-side {
  width: 250px;
  height: auto;
  object-fit: contain;
  margin: 0 20px;
  opacity: 0.95;
  transition: transform 0.3s ease;
}

.mascota-img-side:hover {
  transform: scale(1.1);
}

.login-container {
  background-color: #fff;
  padding: 40px;
  border-radius: 10px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 450px;
  z-index: 10;
}

.login-icon {
  font-size: 4rem;
  color: #007bff;
}

.login-input-icon {
  background-color: #e9ecef;
  border-right: none;
  color: #495057;
}

.form-control {
  border-left: none;
}

.login-btn {
  background-color: #007bff;
  color: white;
  padding: 10px 15px;
  font-size: 1.1rem;
  border-radius: 5px;
  transition: background-color 0.3s ease;
  border: none;
}

.login-btn:hover {
  background-color: #0056b3;
}

.login-link {
  color: #007bff;
  text-decoration: none;
}

.login-link:hover {
  text-decoration: underline;
}

@media (max-width: 768px) {
  .mascota-img-side {
    display: none;
  }

  .login-container {
    padding: 30px;
    margin: 0 15px;
  }
}
</style>
