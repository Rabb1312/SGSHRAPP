<template>
  <!-- Main Container -->
  <!-- Page content -->
  <div id="page-content">
    <!-- Orders and Products -->
    <div class="main-content-wrapper">
      <div class="profile-section">
        <!-- Latest Orders Block -->
        <div class="profile-card">
          <!-- Latest Orders Title -->
          <div class="profile-card-header">
            <h2><strong>Foto Profil</strong></h2>
          </div>
          <!-- END Latest Orders Title -->

          <!-- Latest Orders Content -->
          <div class="profile-avatar-section">
            <a href="javascript:void(0)">
              <img :src="vModelAvatar" alt="avatar" class="avatar-image" />
            </a>
            <h3 class="profile-name">
              <strong>{{ model.profile.name }}</strong>
            </h3>
          </div>

          <div class="profile-info-table">
            <div class="info-row">
              <div class="info-label">
                <strong>Status Registrasi</strong>
              </div>
              <div class="info-value">
                <template v-if="isHiddenRegistrasi">
                  <img :src="loadingBar" width="100" />
                </template>
                <template v-else>
                  <template v-if="isRegistrasi != 'sudah'">
                    <span class="status-badge danger">Belum Teregistrasi</span>
                  </template>
                  <template v-else>
                    <span class="status-badge success">Sudah Teregistrasi</span>
                  </template>
                </template>
              </div>
            </div>

            <div class="info-row">
              <div class="info-label">
                <strong>Status User</strong>
              </div>
              <div class="info-value">
                <template v-if="isHiddenRegistrasi">
                  <img :src="loadingBar" width="100" />
                </template>
                <template v-else>
                  <template v-if="isRegistrasi != 'sudah'">
                    <span class="status-badge danger">Belum Aktif</span>
                  </template>
                  <template v-else>
                    <span class="status-badge success">Aktif</span>
                  </template>
                </template>
              </div>
            </div>

            <div class="info-row">
              <div class="info-label">
                <strong>Themes</strong>
              </div>
              <div class="info-value">
                <div class="themes-container">
                  <div class="theme-item" @click="$root.changeColorBG('night.css')" title="Night">
                    <div class="theme-color night"></div>
                  </div>
                  <div class="theme-item" @click="$root.changeColorBG('amethyst.css')" title="Amethyst">
                    <div class="theme-color amethyst"></div>
                  </div>
                  <div class="theme-item" @click="$root.changeColorBG('modern.css')" title="Modern">
                    <div class="theme-color modern"></div>
                  </div>
                  <div class="theme-item" @click="$root.changeColorBG('autumn.css')" title="Autumn">
                    <div class="theme-color autumn"></div>
                  </div>
                  <div class="theme-item" @click="$root.changeColorBG('flatie.css')" title="Flatie">
                    <div class="theme-color flatie"></div>
                  </div>
                  <div class="theme-item" @click="$root.changeColorBG('spring.css')" title="Spring">
                    <div class="theme-color spring"></div>
                  </div>
                  <div class="theme-item" @click="$root.changeColorBG('fancy.css')" title="Fancy">
                    <div class="theme-color fancy"></div>
                  </div>
                  <div class="theme-item" @click="$root.changeColorBG('fire.css')" title="Fire">
                    <div class="theme-color fire"></div>
                  </div>
                  <div class="theme-item" @click="$root.changeColorBG('coral.css')" title="Coral">
                    <div class="theme-color coral"></div>
                  </div>
                  <div class="theme-item" @click="$root.changeColorBG('lake.css')" title="Lake">
                    <div class="theme-color lake"></div>
                  </div>
                  <div class="theme-item" @click="$root.changeColorBG('forest.css')" title="Forest">
                    <div class="theme-color forest"></div>
                  </div>
                  <div class="theme-item" @click="$root.changeColorBG('waterlily.css')" title="Waterlily">
                    <div class="theme-color waterlily"></div>
                  </div>
                  <div class="theme-item" @click="$root.changeColorBG('emerald.css')" title="Emerald">
                    <div class="theme-color emerald"></div>
                  </div>
                  <div class="theme-item" @click="$root.changeColorBG('blackberry.css')" title="Blackberry">
                    <div class="theme-color blackberry"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- END Latest Orders Content -->
        </div>
        <!-- END Latest Orders Block -->
      </div>

      <div class="detail-section">
        <!-- Top Products Block -->
        <div class="detail-card">
          <!-- Top Products Title -->
          <div class="detail-card-header">
            <h2><strong>Detail Account</strong></h2>
          </div>
          <!-- END Top Products Title -->

          <!-- Top Products Content -->
          <div class="detail-content">
            <div class="qr-section">
              <QRCodeVue3
                v-bind:image="iconUrl"
                :width="200"
                :height="200"
                value="ABCDEFGHIJKMLMONPQRSTUVWXYZ"
                :qrOptions="{
                  typeNumber: 0,
                  mode: 'Byte',
                  errorCorrectionLevel: 'H',
                }"
                :imageOptions="{
                  hideBackgroundDots: true,
                  imageSize: 0.4,
                  margin: 0,
                }"
                :dotsOptions="{
                  type: 'dots',
                  color: '#394263',
                  gradient: {
                    type: 'linear',
                    rotation: 0,
                    colorStops: [
                      { offset: 0, color: '#26249a' },
                      { offset: 1, color: '#26249a' },
                    ],
                  },
                }"
                :backgroundOptions="{ color: '#ffffff' }"
                :cornersSquareOptions="{ type: 'dot', color: '#000000' }"
                :cornersDotOptions="{ type: undefined, color: '#000000' }"
              />

              <h3 class="user-id-title">
                <strong>ID User</strong><br />
                <small>{{ model.profile.uuid }}</small>
              </h3>
            </div>

            <div class="form-section">
              <div class="form-group">
                <label class="form-label">Email</label>
                <div class="form-input">
                  <CmpInputText
                    id="email"
                    name="email"
                    type="text"
                    v-model="model.profile.email"
                    placeholder="Input email"
                    readonly
                  />
                </div>
              </div>

              <div class="form-group">
                <label class="form-label">Name</label>
                <div class="form-input">
                  <CmpInputText
                    id="name"
                    name="name"
                    type="text"
                    v-model="model.profile.name"
                    placeholder="Input name"
                    readonly
                  />
                </div>
              </div>
            </div>
          </div>
          <!-- END Top Products Content -->
        </div>
        <!-- END Top Products Block -->
      </div>
    </div>
    <!-- END Orders and Products -->
  </div>
  <!-- END Page Content -->
</template>

<script>
import axios from "axios";
import { markRaw } from "vue";

import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

import cmpHalLogout from "@/views/auth/_logout.vue";
// import footerLayout from "@/views/template/_footer.vue";

import CmpInputText from "@/components/inputText/inputText.vue";

import imageX from "@/assets/img/avatar@2x.jpg";

import LoadingX from "vue3-loading-overlay";
import "vue3-loading-overlay/dist/vue3-loading-overlay.css";

import QRCodeVue3 from "qrcode-vue3";

import loadingBar from "@/assets/img/Moving_train.gif";
import iconUrl from "@/assets/img/logo-sgs.png";

import { Device } from "@capacitor/device";

export default {
  components: { LoadingX, CmpInputText, QRCodeVue3 },
  data() {
    return {
      iconUrl: iconUrl,
      isLoading: false,
      errorList: "",
      isHidden: false,
      isHiddenRegistrasi: true,
      loadingBar: loadingBar,
      howDismissibleAlert: false,
      returnApiEmail: "",
      name: "QRCodeVue3Example",
      name: "dashboard",
      // logoutLayout: markRaw(cmpHalLogout),
      // footerLayout: markRaw(footerLayout),
      token: localStorage.getItem("token"),
      isLogin: localStorage.getItem("token") != null ? 1 : 0,
      activemenu: null,
      params: null,
      first: null,
      imageX: imageX,
      vModelAvatar: null,
      isRegistrasi: null,
      model: {
        profile: {
          uuid: null,
          email: null,
          name: null,
          phone: null,
          password_old: null,
          password: null,
          password_reenter: null,
        },
      },

      errorField: {
        id: false,
        description: false,
        status: false,
      },

      showPassword: false,
      showPassword_old: false,
      showPassword_reenter: false,

      rules: [
        { message: "One lowercase letter required.", regex: /[a-z]+/ },
        { message: "One uppercase letter required.", regex: /[A-Z]+/ },
        { message: "8 characters minimum.", regex: /.{8,}/ },
        { message: "One number required.", regex: /[0-9]+/ },
      ],

      password: "",
      password_old: "",
      checkPassword: "",
      passwordVisible: false,
      submitted: false,

      modal: false,

      device_info: "",
      userid: 0,
    };
  },
  created() {
    // let uObject = JSON.parse(localStorage.getItem("uObject"));
    // this.model.profile.uuid = uObject.userUuid;
  },
  async mounted() {
    // await this.$root.refreshToken(localStorage.getItem("token"));
    this.isHiddenRegistrasi = false;
    await this.settingConfig();
    this.welcomeinfo();

    this.isRegistrasi = this.$root.decryptData(
      localStorage.getItem("registrasi")
    );
  },
  computed: {
    notSamePasswords() {
      if (this.passwordsFilled) {
        return this.password !== this.checkPassword;
      } else {
        return false;
      }
    },
    passwordsFilled() {
      return this.password !== "" && this.checkPassword !== "";
    },
    passwordValidation() {
      let errors = [];
      for (let condition of this.rules) {
        if (!condition.regex.test(this.password)) {
          errors.push(condition.message);
        }
      }
      if (errors.length === 0) {
        return { valid: true, errors };
      } else {
        return { valid: false, errors };
      }
    },
  },
  methods: {
    welcomeinfo() {
      this.first = sessionStorage.getItem("first");
      if (this.first == 1) {
        toast.info(
          "Hi, " +
            this.$root.decryptData(localStorage.getItem("name")) +
            ". Welcome!",
          {
            theme: "colored",
            position: "top-center",
            transition: "flip",
          }
        );
        sessionStorage.clear();
      }
    },
    saveDeviceLogin(device_info, user_id, info) {
      const mythis = this;
      const AuthStr = "bearer " + localStorage.getItem("token");
      const config = {
        headers: {
          Authorization: AuthStr,
        },
      };
      var url = mythis.$root.apiHostJWT + "user/logUsers";
      axios
        .post(
          url,
          {
            id_users: user_id,
            user_info: device_info,
            info: info,
          },
          config
        )
        .then((res) => {
          console.log(res);
        })
        .catch(function (error) {
          alert(error);
        });
    },
    async settingConfig() {
      await this.$root.presentLoading();
      let uObject = JSON.parse(localStorage.getItem("uObject"));
      this.vModelAvatar = uObject.userAvatar;
      this.model.profile.name = uObject.userName;
      this.model.profile.phone = uObject.userPhone;
      this.model.profile.email = uObject.userEmail;
      await this.$root.stopLoading();
    },
    show_modal() {
      this.modal = !this.modal;
    },
    timerExp() {
      toast.info(
        "Your session will be expired in 5 seconds. Please Re-Login.",
        { theme: "colored" }
      );
      setTimeout(this.$root.endSesi, 5000);
    },
    async changePassPresent() {
      var idx = this.$root.decryptData(localStorage.getItem("unique"));
      const myArray = idx.split("ABCDEFG");
      let word = myArray[1];

      Swal.fire({
        title: "Change Password",
        text: "Are you sure?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Change Password!",
        cancelButtonText: "Cancel",
      }).then(async (result) => {
        if (result.isConfirmed) {
          await this.changePass(
            //localStorage.getItem("unique"),
            word,
            this.model.profile.email,
            this.password_old,
            this.password
          );
        }
      });
    },
    async changePass(id, email, oldpass, pass) {
      var mythis = this;
      //mythis.$root.presentLoading();

      mythis.$root.flagButtonLoading = true;

      await this.$root.refreshToken(localStorage.getItem("token"));

      const that = this;
      const AuthStr = "bearer " + localStorage.getItem("token");
      const URL = this.$root.apiHostJWT + "user/update/" + id;

      const config = {
        headers: {
          Authorization: AuthStr,
        },
      };

      await axios
        .put(
          URL,
          {
            password: oldpass,
            email: email,
            newpass: pass,
          },
          config
        )
        .then(async (res) => {
          //console.log(res);
          await mythis.$root.stopLoading();
          mythis.$root.flagButtonLoading = false;
          await mythis.$root.sleep(500);
          // mythis.$root.presentAlert(
          //   "Update Password Success",
          //   res.data.message
          // );

          toast.success("Update Password Success", { theme: "colored" });
          mythis.timerExp();
        })
        .catch(async function (error) {
          //console.log(error);
          await mythis.$root.stopLoading();
          mythis.$root.flagButtonLoading = false;
          // //console.log(error);
          // mythis.$root.presentAlert(
          //   "Update Password Failed",
          //   error.response.data.message
          // );
          toast.error("Update Password Failed", { theme: "colored" });
          toast.error(error.response.data.message, { theme: "colored" });
        });

      mythis.password = "";
      mythis.password_old = "";
      mythis.checkPassword = "";
      //mythis.txtInput.password1 = "";

      mythis.model.profile.email = "";
    },

    toggleShow() {
      this.showPassword = !this.showPassword;
    },
    toggleShow_old() {
      this.showPassword_old = !this.showPassword_old;
    },
    toggleShow_reenter() {
      this.showPassword_reenter = !this.showPassword_reenter;
    },
  },
};
</script>

<style scoped>
/* Global Styles */
* {
  box-sizing: border-box;
  scroll-behavior: smooth;
}

/* Main Container */
#page-content {
  min-height: 100vh;
  width: 100%;
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
  overflow-x: auto;
}

/* Main Content Wrapper */
.main-content-wrapper {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 30px;
  width: 100%;
  min-height: calc(100vh - 40px);
}

/* Profile Section */
.profile-section {
  display: flex;
  flex-direction: column;
}

.profile-card {
  background: white;
  border-radius: 15px;
  padding: 25px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  height: fit-content;
  min-height: 500px;
}

.profile-card-header {
  margin-bottom: 25px;
  border-bottom: 2px solid #f8f9fa;
  padding-bottom: 15px;
}

.profile-card-header h2 {
  margin: 0;
  color: #333;
  font-size: 1.5rem;
}

/* Avatar Section */
.profile-avatar-section {
  text-align: center;
  margin-bottom: 30px;
}

.avatar-image {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  border: 4px solid #007bff;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  transition: transform 0.3s ease;
}

.avatar-image:hover {
  transform: scale(1.05);
}

.profile-name {
  margin: 20px 0 0 0;
  color: #333;
  font-size: 1.4rem;
}

/* Profile Info Table */
.profile-info-table {
  width: 100%;
}

.info-row {
  display: flex;
  align-items: flex-start;
  padding: 15px 0;
  border-bottom: 1px solid #f0f0f0;
  gap: 15px;
}

.info-row:last-child {
  border-bottom: none;
}

.info-label {
  flex: 0 0 40%;
  font-weight: 600;
  color: #555;
  text-align: right;
  padding-top: 5px;
}

.info-value {
  flex: 1;
  padding-left: 10px;
}

/* Status Badges */
.status-badge {
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-badge.success {
  background: #28a745;
  color: white;
}

.status-badge.danger {
  background: #dc3545;
  color: white;
}

/* Themes Container */
.themes-container {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 10px;
  max-width: 200px;
}

.theme-item {
  cursor: pointer;
  transition: transform 0.2s ease;
}

.theme-item:hover {
  transform: scale(1.1);
}

.theme-color {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  border: 2px solid #ddd;
  transition: border-color 0.2s ease;
}

.theme-color:hover {
  border-color: #007bff;
}

/* Theme Colors */
.theme-color.night { background: #2c3e50; }
.theme-color.amethyst { background: #9b59b6; }
.theme-color.modern { background: #34495e; }
.theme-color.autumn { background: #e67e22; }
.theme-color.flatie { background: #1abc9c; }
.theme-color.spring { background: #2ecc71; }
.theme-color.fancy { background: #e91e63; }
.theme-color.fire { background: #e74c3c; }
.theme-color.coral { background: #ff7675; }
.theme-color.lake { background: #0984e3; }
.theme-color.forest { background: #00b894; }
.theme-color.waterlily { background: #a29bfe; }
.theme-color.emerald { background: #00cec9; }
.theme-color.blackberry { background: #2d3436; }

/* Detail Section */
.detail-section {
  display: flex;
  flex-direction: column;
}

.detail-card {
  background: white;
  border-radius: 15px;
  padding: 25px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  height: fit-content;
  min-height: 500px;
}

.detail-card-header {
  margin-bottom: 25px;
  border-bottom: 2px solid #f8f9fa;
  padding-bottom: 15px;
}

.detail-card-header h2 {
  margin: 0;
  color: #333;
  font-size: 1.5rem;
}

/* Detail Content */
.detail-content {
  display: flex;
  flex-direction: column;
  gap: 30px;
}

/* QR Section */
.qr-section {
  text-align: center;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 15px;
  border: 2px dashed #dee2e6;
}

.user-id-title {
  margin: 20px 0 0 0;
  color: #333;
  font-size: 1.2rem;
}

.user-id-title small {
  color: #666;
  font-size: 0.9rem;
  word-break: break-all;
}

/* Form Section */
.form-section {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  align-items: center;
  gap: 20px;
}

.form-label {
  flex: 0 0 100px;
  font-weight: 600;
  color: #555;
  text-align: right;
}

.form-input {
  flex: 1;
}

/* Input styles would be handled by CmpInputText component */

/* Responsive Design */
@media (max-width: 1200px) {
  .main-content-wrapper {
    grid-template-columns: 1fr 1.5fr;
    gap: 25px;
  }
}

@media (max-width: 992px) {
  .main-content-wrapper {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  #page-content {
    padding: 15px;
  }
  
  .themes-container {
    grid-template-columns: repeat(7, 1fr);
    max-width: 280px;
  }
}

@media (max-width: 768px) {
  #page-content {
    padding: 10px;
  }
  
  .profile-card,
  .detail-card {
    padding: 20px;
  }
  
  .info-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
  
  .info-label {
    flex: none;
    text-align: left;
    padding-top: 0;
  }
  
  .info-value {
    padding-left: 0;
  }
  
  .form-group {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
  
  .form-label {
    flex: none;
    text-align: left;
  }
  
  .avatar-image {
    width: 100px;
    height: 100px;
  }
  
  .themes-container {
    grid-template-columns: repeat(5, 1fr);
    max-width: 200px;
    margin: 0 auto;
  }
}

@media (max-width: 480px) {
  .profile-card,
  .detail-card {
    padding: 15px;
  }
  
  .profile-card-header h2,
  .detail-card-header h2 {
    font-size: 1.3rem;
  }
  
  .themes-container {
    grid-template-columns: repeat(4, 1fr);
  }
  
  .theme-color {
    width: 25px;
    height: 25px;
  }
}

/* Utility Classes */
.fas {
  cursor: pointer;
}

.input-error {
  border: red 1px solid;
}

/* Print Styles */
@media print {
  .themes-container,
  .theme-item {
    display: none;
  }
  
  .profile-card,
  .detail-card {
    box-shadow: none;
    border: 1px solid #ddd;
  }
}
</style>