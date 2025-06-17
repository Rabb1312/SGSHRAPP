<template>
  <div id="login-container" class="animation-fadeIn">
    <!-- Login Title -->
    <div class="login-title text-center" style="background-color: #ffffff">
      <img :src="imageUrl" alt="avatar" class="widget-image" width="250" />
    </div>
    <!-- END Login Title -->

    <!-- Login Block -->
    <div class="block push-bit">
      <!-- Login Form -->
      <div class="form-horizontal form-bordered">
        <div class="form-group">
          <div class="col-xs-12">
            <div class="input-group">
              <span class="input-group-addon" style="border-color: #6c6c6c">
                <i class="gi gi-envelope"></i>
              </span>
              <CmpInputText
                id="email"
                name="email"
                type="text"
                v-model="email"
                placeholder="Email"
                :class="
                  errorField.email
                    ? 'form-control input-lg input-error'
                    : 'form-control input-lg'
                "
                @keyup.enter="login"
              />
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-12">
            <div class="input-group">
              <span class="input-group-addon" style="border-color: #6c6c6c">
                <i class="gi gi-asterisk"></i>
              </span>

              <CmpInputText
                id="password"
                name="password"
                :type="showPassword ? 'text' : 'password'"
                v-model="password"
                placeholder="Password"
                :class="
                  errorField.password
                    ? 'form-control input-lg input-error'
                    : 'form-control input-lg'
                "
                @keyup.enter="login"
              />

              <span class="input-group-addon" style="border-color: #6c6c6c">
                <i
                  @click="toggleShow"
                  class="fas"
                  :class="{
                    'gi gi-eye_close': showPassword,
                    'gi gi-eye_open': !showPassword,
                  }"
                ></i>
              </span>
            </div>
          </div>
        </div>

        <div class="form-group form-actions">
          <div class="col-xs-12 text-center">
            <button
              type="button"
              class="btn btn-lg btn-primary btn-block"
              @click="login"
              :disabled="$root.flagButtonLoading || isLoading"
            >
              <i
                v-if="$root.flagButtonLoading || isLoading"
                class="fa fa-spinner fa-spin text-default"
              ></i>
              {{ isLoading ? 'Signing In...' : 'Sign In' }}
            </button>
          </div>
        </div>

        <!-- Demo Login Accounts -->
        <div class="form-group" style="margin-top: 20px;">
          <div class="col-xs-12">
            <div style="background: rgba(240, 248, 255, 0.8); padding: 15px; border-radius: 8px; font-size: 12px;">
              <strong>Demo Accounts:</strong><br>
              <div style="margin: 5px 0;">
                <strong>Admin:</strong> 
                <button @click="fillDemo('admin')" class="demo-btn">admin@test.com</button>
              </div>
              <div style="margin: 5px 0;">
                <strong>HR Manager:</strong> 
                <button @click="fillDemo('hr')" class="demo-btn">hr@test.com</button>
              </div>
              <div style="margin: 5px 0;">
                <strong>User:</strong> 
                <button @click="fillDemo('user')" class="demo-btn">user@test.com</button>
              </div>
              <div style="margin: 5px 0; font-style: italic;">
                Password: admin123
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- END Login Form -->
    </div>
    <!-- END Login Block -->
  </div>
  <!-- END Login Container -->

  <div>
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 1536 714"
      preserveAspectRatio="xMidYMid"
      width="1536"
      height="714"
      style="
        background-color: #87cefa;
        shape-rendering: auto;
        display: block;
        background-position-x: 0%;
        background-position-y: 0%;
        background-size: auto;
        background-origin: padding-box;
        background-clip: border-box;
        width: 1536px;
        height: 714px;
      "
    >
      <defs>
        <linearGradient id="lg-o578w6yh81n" x1="0" y1="0" x2="1" y2="0">
          <stop offset="0" stop-color="#87CEEB"></stop>
          <stop offset="1" stop-color="#00008B"></stop>
        </linearGradient>
      </defs>
      <g>
        <path
          opacity="0.4"
          fill="url(#lg-o578w6yh81n)"
          d="M 0 0 L 0 483.15 Q 153.6 464.97 307.2 444.97 T 614.4 340.43 T 921.6 307.87 T 1228.8 356.83 T 1536 282.94 L 1536 0 Z"
        >
          <animate
            attributeName="d"
            dur="10s"
            repeatCount="indefinite"
            values="
        M 0 0 L 0 483.15 Q 153.6 464.97 307.2 444.97 T 614.4 340.43 T 921.6 307.87 T 1228.8 356.83 T 1536 282.94 L 1536 0 Z;
        M 0 0 L 0 488.423 Q 153.6 423.88 307.2 399.159 T 614.4 417.02 T 921.6 325.007 T 1228.8 353.998 T 1536 278.765 L 1536 0 Z;
        M 0 0 L 0 511.17 Q 153.6 438.853 307.2 417.506 T 614.4 351.648 T 921.6 361.298 T 1228.8 227.621 T 1536 178.912 L 1536 0 Z;
        M 0 0 L 0 446.388 Q 153.6 433.641 307.2 408.817 T 614.4 356.997 T 921.6 291.757 T 1228.8 233.212 T 1536 233.598 L 1536 0 Z;
        M 0 0 L 0 483.15 Q 153.6 464.97 307.2 444.97 T 614.4 340.43 T 921.6 307.87 T 1228.8 356.83 T 1536 282.94 L 1536 0 Z
      "
          />
        </path>
        <path
          opacity="0.4"
          fill="url(#lg-o578w6yh81n)"
          d="M 0 0 L 0 446.388 Q 153.6 433.641 307.2 408.817 T 614.4 356.997 T 921.6 291.757 T 1228.8 233.212 T 1536 233.598 L 1536 0 Z"
        >
          <animate
            attributeName="d"
            dur="10s"
            repeatCount="indefinite"
            values="
        M 0 0 L 0 446.388 Q 153.6 433.641 307.2 408.817 T 614.4 356.997 T 921.6 291.757 T 1228.8 233.212 T 1536 233.598 L 1536 0 Z;
        M 0 0 L 0 457.703 Q 153.6 503.262 307.2 479.042 T 614.4 319.734 T 921.6 348.609 T 1228.8 270.326 T 1536 220.425 L 1536 0 Z;
        M 0 0 L 0 488.423 Q 153.6 423.88 307.2 399.159 T 614.4 417.02 T 921.6 325.007 T 1228.8 353.998 T 1536 278.765 L 1536 0 Z;
        M 0 0 L 0 511.17 Q 153.6 438.853 307.2 417.506 T 614.4 351.648 T 921.6 361.298 T 1228.8 227.621 T 1536 178.912 L 1536 0 Z;
        M 0 0 L 0 446.388 Q 153.6 433.641 307.2 408.817 T 614.4 356.997 T 921.6 291.757 T 1228.8 233.212 T 1536 233.598 L 1536 0 Z
      "
          />
        </path>
        <path
          opacity="0.4"
          fill="url(#lg-o578w6yh81n)"
          d="M 0 0 L 0 457.703 Q 153.6 503.262 307.2 479.042 T 614.4 319.734 T 921.6 348.609 T 1228.8 270.326 T 1536 220.425 L 1536 0 Z"
        >
          <animate
            attributeName="d"
            dur="10s"
            repeatCount="indefinite"
            values="
        M 0 0 L 0 457.703 Q 153.6 503.262 307.2 479.042 T 614.4 319.734 T 921.6 348.609 T 1228.8 270.326 T 1536 220.425 L 1536 0 Z;
        M 0 0 L 0 511.17 Q 153.6 438.853 307.2 417.506 T 614.4 351.648 T 921.6 361.298 T 1228.8 227.621 T 1536 178.912 L 1536 0 Z;
        M 0 0 L 0 488.423 Q 153.6 423.88 307.2 399.159 T 614.4 417.02 T 921.6 325.007 T 1228.8 353.998 T 1536 278.765 L 1536 0 Z;
        M 0 0 L 0 483.15 Q 153.6 464.97 307.2 444.97 T 614.4 340.43 T 921.6 307.87 T 1228.8 356.83 T 1536 282.94 L 1536 0 Z;
        M 0 0 L 0 457.703 Q 153.6 503.262 307.2 479.042 T 614.4 319.734 T 921.6 348.609 T 1228.8 270.326 T 1536 220.425 L 1536 0 Z
      "
          />
        </path>
        <path
          opacity="0.4"
          fill="url(#lg-o578w6yh81n)"
          d="M 0 0 L 0 488.423 Q 153.6 423.88 307.2 399.159 T 614.4 417.02 T 921.6 325.007 T 1228.8 353.998 T 1536 278.765 L 1536 0 Z"
        >
          <animate
            attributeName="d"
            dur="10s"
            repeatCount="indefinite"
            values="
        M 0 0 L 0 488.423 Q 153.6 423.88 307.2 399.159 T 614.4 417.02 T 921.6 325.007 T 1228.8 353.998 T 1536 278.765 L 1536 0 Z;
        M 0 0 L 0 511.17 Q 153.6 438.853 307.2 417.506 T 614.4 351.648 T 921.6 361.298 T 1228.8 227.621 T 1536 178.912 L 1536 0 Z;
        M 0 0 L 0 446.388 Q 153.6 433.641 307.2 408.817 T 614.4 356.997 T 921.6 291.757 T 1228.8 233.212 T 1536 233.598 L 1536 0 Z;
        M 0 0 L 0 457.703 Q 153.6 503.262 307.2 479.042 T 614.4 319.734 T 921.6 348.609 T 1228.8 270.326 T 1536 220.425 L 1536 0 Z;
        M 0 0 L 0 488.423 Q 153.6 423.88 307.2 399.159 T 614.4 417.02 T 921.6 325.007 T 1228.8 353.998 T 1536 278.765 L 1536 0 Z
      "
          />
        </path>
        <path
          opacity="0.4"
          fill="url(#lg-o578w6yh81n)"
          d="M 0 0 L 0 511.17 Q 153.6 438.853 307.2 417.506 T 614.4 351.648 T 921.6 361.298 T 1228.8 227.621 T 1536 178.912 L 1536 0 Z"
        >
          <animate
            attributeName="d"
            dur="10s"
            repeatCount="indefinite"
            values="
        M 0 0 L 0 511.17 Q 153.6 438.853 307.2 417.506 T 614.4 351.648 T 921.6 361.298 T 1228.8 227.621 T 1536 178.912 L 1536 0 Z;
        M 0 0 L 0 446.388 Q 153.6 433.641 307.2 408.817 T 614.4 356.997 T 921.6 291.757 T 1228.8 233.212 T 1536 233.598 L 1536 0 Z;
        M 0 0 L 0 457.703 Q 153.6 503.262 307.2 479.042 T 614.4 319.734 T 921.6 348.609 T 1228.8 270.326 T 1536 220.425 L 1536 0 Z;
        M 0 0 L 0 488.423 Q 153.6 423.88 307.2 399.159 T 614.4 417.02 T 921.6 325.007 T 1228.8 353.998 T 1536 278.765 L 1536 0 Z;
        M 0 0 L 0 511.17 Q 153.6 438.853 307.2 417.506 T 614.4 351.648 T 921.6 361.298 T 1228.8 227.621 T 1536 178.912 L 1536 0 Z
      "
          />
        </path>
      </g>
    </svg>
  </div>
</template>

<script>
import axios from "axios";

import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

import dummyLogo from "@/assets/img/logo-sgs.png";

import LoadingX from "vue3-loading-overlay";
import "vue3-loading-overlay/dist/vue3-loading-overlay.css";

export default {
  components: { LoadingX },
  data() {
    return {
      isLoading: false,
      title: "Login Page",
      email: null,
      password: null,
      loggedIn: false,
      user: null,
      imageUrl: dummyLogo,
      showPassword: false,
      errorField: {
        email: false,
        password: false,
      },
    };
  },
  mounted() {
    // Check if already logged in
    this.checkExistingLogin();
  },
  methods: {
    checkExistingLogin() {
      const token = localStorage.getItem("token");
      const userObj = localStorage.getItem("uObject");
      
      if (token && userObj) {
        // User already logged in, redirect to dashboard
        this.$root.isLogin = 1;
        this.$root.goto('cmpHalProfile');
      }
    },

    fillDemo(role) {
      switch(role) {
        case 'admin':
          this.email = 'admin@test.com';
          break;
        case 'hr':
          this.email = 'hr@test.com';
          break;
        case 'user':
          this.email = 'user@test.com';
          break;
      }
      this.passwordd = 'admin123';
      this.errorField.email = false;
      this.errorField.password = false;
    },

    async postLogin() {
      try {
        this.isLoading = true;
        this.$root.presentLoading();

        console.log('Attempting login to:', `${this.$root.apiHost}/api/login`);

        // Panggil API login
        const response = await axios.post(`${this.$root.apiHost}/api/login`, {
          email: this.email,
          password: this.password,
        });

        console.log('Login response:', response.data);

        if (response.data.success) {
          const userData = response.data.user;
          const token = response.data.token;

          // Simpan token
          localStorage.setItem("token", token);
          
          // Setup axios header untuk request selanjutnya
          axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

          // Simpan data user dengan format yang konsisten
          const userObject = {
            userId: userData.userId,
            userEmail: userData.userEmail,
            userName: userData.userName,
            userAvatar: userData.userAvatar || 
              this.$root.appBaseUrl + "/public/26954e5e29b5c77937e5098d3e817bb5.png",
            role: userData.role || "user",
          };

          localStorage.setItem("uObject", JSON.stringify(userObject));
          localStorage.setItem("name", this.$root.encryptData(userData.userName));
          localStorage.setItem("registrasi", this.$root.encryptData("sudah"));
          localStorage.setItem("halaman", this.$root.encryptData("cmpHalDashboard"));
          
          // Set session
          sessionStorage.setItem("first", 1);

          // Update root state
          this.$root.isLogin = 1;
          this.$root.currentUser = userObject;

          // Initialize user data in App component
          if (this.$root.initializeUserData) {
            await this.$root.initializeUserData();
          }

          console.log('Login successful for user:', userObject);

          // Tampilkan pesan sukses
          toast.success(`Welcome ${userData.userName}! (${userData.role})`, { 
            theme: "colored",
            position: "top-right",
            autoClose: 3000
          });

          // Wait a bit then reload
          await this.$root.sleep(1500);
          window.location.reload();
        } else {
          throw new Error(response.data.message || 'Login failed');
        }
      } catch (error) {
        console.error("Login error:", error);
        
        // Reset form state
        this.errorField.email = true;
        this.errorField.password = true;

        let errorMessage = "Login failed";
        
        if (error.response) {
          // Server responded with error status
          errorMessage = error.response.data?.message || `Server error: ${error.response.status}`;
        } else if (error.request) {
          // Request was made but no response received
          errorMessage = "Cannot connect to server. Please check your connection.";
        } else {
          // Something else happened
          errorMessage = error.message || "An unexpected error occurred";
        }

        toast.error(errorMessage, { 
          theme: "colored",
          position: "top-right",
          autoClose: 5000
        });
      } finally {
        this.isLoading = false;
        this.$root.flagButtonLoading = false;
        this.$root.stopLoading();
      }
    },

    toggleShow() {
      this.showPassword = !this.showPassword;
    },

    async login() {
      // Prevent double click
      if (this.isLoading || this.$root.flagButtonLoading) {
        return;
      }

      this.$root.flagButtonLoading = true;
      
      try {
        const isValid = await this.validation();
        if (isValid) {
          await this.postLogin();
        }
      } catch (error) {
        console.error("Login process error:", error);
        toast.error("Login process failed", { theme: "colored" });
      } finally {
        this.$root.flagButtonLoading = false;
      }
    },

    async validation() {
      // Reset error states
      this.errorField.email = false;
      this.errorField.password = false;

      if (!this.email || this.email.trim() === '') {
        toast.error("Email cannot be empty", { 
          theme: "colored",
          position: "top-right"
        });
        this.errorField.email = true;
        return false;
      }

      if (!this.password || this.password.trim() === '') {
        toast.error("Password cannot be empty", { 
          theme: "colored",
          position: "top-right"
        });
        this.errorField.password = true;
        return false;
      }

      // Basic email validation
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(this.email)) {
        toast.error("Please enter a valid email address", { 
          theme: "colored",
          position: "top-right"
        });
        this.errorField.email = true;
        return false;
      }

      return true;
    },
  },
};
</script>

<style scoped>
/* ===== GLOBAL RESET ===== */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

html, body {
  height: 100%;
  width: 100%;
  overflow-x: hidden;
}

/* ===== PERFECT CENTERING FIX ===== */
#login-container {
  position: fixed !important;
  top: 50% !important;
  left: 50% !important;
  transform: translate(-50%, -50%) !important;
  width: auto !important;
  height: auto !important;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 10;
  padding: 20px;
  /* Remove any conflicting positioning */
  right: auto !important;
  bottom: auto !important;
}

.login-title,
.block {
  position: relative;
  z-index: 11;
}

.login-title {
  background: rgba(255, 255, 255, 0.95) !important;
  border-radius: 15px 15px 0 0;
  padding: 30px 25px 20px 25px;
  margin-bottom: 0;
  box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-bottom: none;
  max-width: 400px;
  width: 100%;
}

.block {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 0 0 15px 15px;
  padding: 20px 25px 30px 25px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-top: none;
  max-width: 400px;
  width: 100%;
}

.widget-image {
  width: 200px !important;
  max-width: 100%;
  height: auto;
  display: block;
  margin: 0 auto;
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
}

.form-horizontal {
  width: 100%;
}

.form-group {
  margin-bottom: 20px;
}

.col-xs-12 {
  width: 100%;
  padding: 0;
}

.input-group {
  display: flex;
  width: 100%;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.input-group:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.input-group-addon {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 15px;
  background: #f8f9fa;
  border: 1px solid #dee2e6 !important;
  border-right: none !important;
  min-width: 50px;
  color: #6c757d;
  transition: all 0.3s ease;
}

.form-control {
  flex: 1;
  padding: 15px;
  border: 1px solid #dee2e6 !important;
  border-left: none !important;
  border-right: none !important;
  font-size: 16px;
  background: white;
  outline: none;
  transition: all 0.3s ease;
}

.input-group-addon:last-child {
  border-left: none !important;
  border-right: 1px solid #dee2e6 !important;
  cursor: pointer;
}

.input-group:focus-within {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 123, 255, 0.2);
}

.input-group:focus-within .input-group-addon {
  border-color: #80bdff !important;
  background: #e7f3ff;
}

.input-group:focus-within .form-control {
  border-color: #80bdff !important;
  box-shadow: none;
}

.form-control:focus {
  border-color: #80bdff !important;
  box-shadow: none !important;
}

.btn {
  width: 100%;
  padding: 16px 20px;
  font-size: 16px;
  font-weight: 600;
  border-radius: 8px;
  border: none;
  background: linear-gradient(135deg, #007bff, #0056b3);
  color: white;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}

.btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
  background: linear-gradient(135deg, #0056b3, #003d82);
}

.btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.input-error {
  border-color: #dc3545 !important;
}

.input-group:has(.input-error) .input-group-addon {
  border-color: #dc3545 !important;
  background: #f8d7da;
}

/* ===== BACKGROUND SVG POSITIONING ===== */
svg {
  position: fixed !important;
  top: 0;
  left: 0;
  width: 100vw !important;
  height: 100vh !important;
  z-index: -1;
  object-fit: cover;
}

.fas {
  cursor: pointer;
}

.animation-fadeIn {
  animation: fadeInUp 0.8s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translate(-50%, -40%);
  }
  to {
    opacity: 1;
    transform: translate(-50%, -50%);
  }
}

/* ===== DEMO BUTTONS STYLES ===== */
.demo-btn {
  background: none;
  border: none;
  color: #007bff;
  text-decoration: underline;
  cursor: pointer;
  font-size: 12px;
  padding: 2px 4px;
  margin: 0 5px;
  border-radius: 3px;
  transition: all 0.2s ease;
}

.demo-btn:hover {
  background: #007bff;
  color: white;
  text-decoration: none;
}

/* ===== RESPONSIVE STYLES ===== */
@media screen and (max-width: 480px) {
  #login-container {
    padding: 15px;
    max-width: 90vw;
  }
  
  .login-title {
    padding: 20px 15px 15px 15px;
    max-width: 320px;
  }
  
  .block {
    padding: 15px 15px 20px 15px;
    max-width: 320px;
  }
  
  .widget-image {
    width: 150px !important;
  }
  
  .form-control,
  .input-group-addon {
    padding: 12px;
    font-size: 16px;
  }
  
  .input-group-addon {
    min-width: 45px;
  }
  
  .btn {
    padding: 14px 16px;
    font-size: 16px;
  }
}

@media screen and (min-width: 481px) and (max-width: 767px) {
  #login-container {
    padding: 20px;
  }
  
  .login-title {
    padding: 25px 20px 18px 20px;
    max-width: 350px;
  }
  
  .block {
    padding: 18px 20px 25px 20px;
    max-width: 350px;
  }
  
  .widget-image {
    width: 170px !important;
  }
  
  .form-control,
  .input-group-addon {
    padding: 14px;
  }
}

@media screen and (min-width: 768px) and (max-width: 1024px) {
  #login-container {
    padding: 25px;
  }
  
  .login-title {
    padding: 30px 25px 20px 25px;
    max-width: 380px;
  }
  
  .block {
    padding: 20px 25px 30px 25px;
    max-width: 380px;
  }
  
  .widget-image {
    width: 190px !important;
  }
}

@media screen and (min-width: 1025px) and (max-width: 1440px) {
  #login-container {
    padding: 30px;
  }
  
  .login-title {
    max-width: 400px;
  }
  
  .block {
    max-width: 400px;
  }
  
  .widget-image {
    width: 200px !important;
  }
}

@media screen and (min-width: 1441px) {
  #login-container {
    padding: 40px;
  }
  
  .login-title {
    padding: 35px 30px 25px 30px;
    max-width: 450px;
  }
  
  .block {
    padding: 25px 30px 35px 30px;
    max-width: 450px;
  }
  
  .widget-image {
    width: 220px !important;
  }
  
  .form-control,
  .input-group-addon {
    padding: 16px;
    font-size: 17px;
  }
  
  .btn {
    padding: 18px 24px;
    font-size: 17px;
  }
}

@media screen and (-webkit-min-device-pixel-ratio: 1.5) {
  .login-title,
  .block {
    max-width: 350px;
  }
}

@media screen and (-webkit-min-device-pixel-ratio: 2) {
  .login-title,
  .block {
    max-width: 320px;
  }
  
  .widget-image {
    width: 180px !important;
  }
}

@media screen and (-webkit-min-device-pixel-ratio: 2.5) {
  .login-title,
  .block {
    max-width: 300px;
  }
  
  .widget-image {
    width: 160px !important;
  }
}

@media screen and (orientation: landscape) and (max-height: 600px) {
  #login-container {
    padding: 15px;
  }
  
  .login-title {
    padding: 20px 25px 15px 25px;
  }
  
  .block {
    padding: 15px 25px 20px 25px;
  }
  
  .widget-image {
    width: 120px !important;
  }
  
  .form-group {
    margin-bottom: 15px;
  }
}

@media screen and (-webkit-min-device-pixel-ratio: 3) {
  .form-control,
  .input-group-addon,
  .btn {
    border-width: 0.5px;
  }
}

@media (prefers-reduced-motion: reduce) {
  * {
    animation: none !important;
    transition: none !important;
  }
}

@media print {
  svg {
    display: none;
  }
  
  #login-container {
    position: static;
    box-shadow: none;
  }
  
  .login-title,
  .block {
    background: white !important;
    border: 1px solid #ccc;
  }
}
</style>