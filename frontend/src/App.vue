<template>
  <LoadingX
    v-model:active="$root.isLoading"
    :can-cancel="false"
    :is-full-page="true"
    :z-index="100000"
    loader="bars"
  />
  <notifications position="bottom right" width="500" />
  <div id="page-wrapper" v-if="isLogin == 1">
    <div
      id="page-container"
      :class="
        $root.hide_menu_status
          ? 'sidebar-no-animations sidebar-visible-lg sidebar-visible-xs sidebar-visible-sm sidebar-visible-md'
          : 'sidebar-no-animations'
      "
    >
      <component :is="_header"></component>
      <div id="main-container">
        <component :is="_logout"></component>
        <div id="container">
          <!-- Role-based access check -->
          <div v-if="hasAccessToCurrentRoute">
            <component :is="activeRoute"></component>
          </div>
          <!-- Unauthorized access message -->
          <div v-else class="unauthorized-access">
            <div class="alert alert-danger">
              <h4><i class="fa fa-ban"></i> Access Denied</h4>
              <p>You don't have permission to access this page.</p>
              <button @click="goToDashboard" class="btn btn-primary">
                Go to Dashboard
              </button>
            </div>
          </div>
          <component :is="_footer"></component>
        </div>
      </div>
    </div>
  </div>
  <div v-else>
    <component :is="activeRoute"></component>
  </div>
</template>

<script>
import { doc, getDoc, onSnapshot, updateDoc } from "firebase/firestore";
import db from "@/firebase/init.js";

import CryptoJS from "crypto-js";
import axios from "axios";
import { markRaw } from "vue";

// Import all your existing components (keeping the same imports)
import LoadingX from "vue3-loading-overlay";
import "vue3-loading-overlay/dist/vue3-loading-overlay.css";

import { Device } from "@capacitor/device";

import _logout from "@/views/auth/_logout.vue";
import _header from "@/views/auth/_header.vue";
import _footer from "@/views/auth/_footer.vue";
import _Login from "@/views/auth/_Login.vue";

import cmpHalDashboard from "@/views/pages/cmpHalDashboard.vue";
import cmpHalProfile from "@/views/pages/cmpHalProfile.vue";
import cmpHalDashboard1 from "@/views/pages/cmpHalDashboard.vue";
import cmpHalDashboard2 from "@/views/pages/cmpHalDashboard.vue";
import cmpHalDashboard3 from "@/views/pages/cmpHalDashboard.vue";
import cmpMasterDocStatus from "@/views/pages/test/cmpMasterDocStatus.vue";
import cmpMasterCabang from "@/views/pages/test/cmpMasterCabang.vue";
import cmpMasterUnit from "@/views/pages/test/cmpMasterUnit.vue";
import cmpMasterJabatan from "@/views/pages/test/cmpMasterJabatan.vue";
import cmpMasterOrganisasi from "@/views/pages/test/cmpMasterOrganisasi.vue";
import cmpMasterBank from "@/views/pages/test/cmpMasterBank.vue";
import cmpMasterSallary from "@/views/pages/test/cmpMasterSallary.vue";
import cmpMasterBpjs from "@/views/pages/test/cmpMasterBpjs.vue";
import cmpMasterNpwp from "@/views/pages/test/cmpMasterNpwp.vue";
import cmpMasterKodeLokasi from "@/views/pages/test/cmpMasterKodeLokasi.vue";
import cmpMasterProfile from "@/views/pages/test/cmpMasterProfile.vue";
import cmpMasterProfileDetail from "@/views/pages/test/cmpMasterProfileDetail.vue";
import cmpMasterKontrak from "@/views/pages/test/cmpMasterKontrak.vue";
import cmpRekKar from "@/views/pages/test/cmpRekKar.vue";
import cmpMasterTemplate from "@/views/pages/test/cmpMasterTemplate.vue";
import cmpMasterDocument from "@/views/pages/test/cmpMasterDocument.vue";
import cmpUsers from "@/views/pages/test/cmpUsers.vue";
import cmpBobotPkSoba from "@/views/pages/test/cmpBobotPkSoba.vue";
import cmpHalRoute from "@/views/pages/cmpHalRoute.vue";
import cmpTempPos from "@/views/pages/test/cmpTempPos.vue";
import cmpTempPosDetail from "@/views/pages/test/cmpTempPosDetail.vue";
import cmpSignatureMaster from "@/views/pages/test/cmpSignatureMaster.vue";
import MasterKriteria from "@/views/pages/test/MasterKriteria.vue";
import KriteriaDetail from "@/views/pages/test/KriteriaDetail.vue";
import cmpMasterRapor from "@/views/pages/test/cmpMasterRapor.vue";

const routeComponent = {
  cmpHalRoute: markRaw(cmpHalRoute),
  cmpHalDashboard: markRaw(cmpHalDashboard),
  cmpHalProfile: markRaw(cmpHalProfile),
  cmpHalDashboard1: markRaw(cmpHalDashboard1),
  cmpHalDashboard2: markRaw(cmpHalDashboard2),
  cmpHalDashboard3: markRaw(cmpHalDashboard3),
  cmpMasterDocStatus: markRaw(cmpMasterDocStatus),
  cmpMasterCabang: markRaw(cmpMasterCabang),
  cmpMasterUnit: markRaw(cmpMasterUnit),
  cmpMasterJabatan: markRaw(cmpMasterJabatan),
  cmpMasterOrganisasi: markRaw(cmpMasterOrganisasi),
  cmpMasterBank: markRaw(cmpMasterBank),
  cmpMasterSallary: markRaw(cmpMasterSallary),
  cmpMasterBpjs: markRaw(cmpMasterBpjs),
  cmpMasterNpwp: markRaw(cmpMasterNpwp),
  cmpMasterKodeLokasi: markRaw(cmpMasterKodeLokasi),
  cmpMasterProfile: markRaw(cmpMasterProfile),
  cmpMasterProfileDetail: markRaw(cmpMasterProfileDetail),
  cmpMasterKontrak: markRaw(cmpMasterKontrak),
  cmpRekKar: markRaw(cmpRekKar),
  cmpMasterTemplate: markRaw(cmpMasterTemplate),
  cmpMasterDocument: markRaw(cmpMasterDocument),
  cmpUsers: markRaw(cmpUsers),
  cmpBobotPkSoba: markRaw(cmpBobotPkSoba),
  cmpTempPos: markRaw(cmpTempPos),
  cmpTempPosDetail: markRaw(cmpTempPosDetail),
  cmpSignatureMaster: markRaw(cmpSignatureMaster),
  MasterKriteria: markRaw(MasterKriteria),
  KriteriaDetail: markRaw(KriteriaDetail),
  cmpMasterRapor: markRaw(cmpMasterRapor),
  Login: markRaw(_Login),
  _logout: markRaw(_logout),
  _header: markRaw(_header),
  _footer: markRaw(_footer),
};

export default {
  components: { LoadingX },
  data() {
    const apiHost = import.meta.env.VITE_API_HOST,
      apiHostJWT = import.meta.env.VITE_API_HOST_JWT,
      appHost = import.meta.env.VITE_APP_HOST,
      appID = import.meta.env.VITE_APP_ID,
      appBaseUrl = import.meta.env.VITE_APP_BASE_URL,
      appSsoUrl = import.meta.env.VITE_APP_SSO_URL,
      appSsoTokenStatus = import.meta.env.VITE_APP_SSO_TOKEN_STATUS,
      pagingTabel1 = import.meta.env.VITE_APP_PAGING_TABEL1,
      timerLoginDetik = import.meta.env.VITE_APP_TIMER_LOGIN_DETIK,
      secretCodeEnc = import.meta.env.VITE_APP_SECRET_CODE_ENC,
      appName = import.meta.env.VITE_APP_NAMA_APLIKASI,
      apiHostEXPRESS_1 = import.meta.env.VITE_APP_BASE_URL_EXPRESS_VUE_1,
      apiHostAttendance = import.meta.env.VITE_API_HOST_ATTENDANCE,
      apiTokenFCM = import.meta.env.VITE_APP_FCM_WEB_PUSH_CERTIFICATE,
      apiTokenWA = import.meta.env.VITE_APP_FONNTE_TOKEN,
      apiFCMStatus = import.meta.env.VITE_APP_FCM_STATUS,
      menuUser = routeComponent;

    return {
      // All your existing data properties...
      apiFCMStatus: apiFCMStatus,
      apiTokenFCM: apiTokenFCM,
      apiTokenWA: apiTokenWA,
      apiHostAttendance: apiHostAttendance,
      apiHostEXPRESS_1: apiHostEXPRESS_1,
      isLoading: false,
      halamanSekarang: localStorage.getItem("halaman"),
      userName: localStorage.getItem("userName") != "null" ? localStorage.getItem("userName") : "Selamat Datang.",
      appName: appName,
      apiHost: apiHost,
      appHost: appHost,
      appID: appID,
      apiHostJWT: apiHostJWT,
      appBaseUrl: appBaseUrl,
      appSsoUrl: appSsoUrl,
      appSsoTokenStatus: appSsoTokenStatus,
      menuUser: menuUser,
      timerLoginDetik: timerLoginDetik,
      isLogin: localStorage.getItem("token") != null ? 1 : 0,
      activeRoute: null,
      _header: routeComponent["_header"],
      _logout: routeComponent["_logout"],
      _footer: routeComponent["_footer"],
      params: null,
      cmpMenu: null,
      pagingTabel1: pagingTabel1,
      secretCodeEnc: secretCodeEnc,

      // Keep all your existing properties...
      docFirebase: doc,
      getDocFirebase: getDoc,
      onSnapshotFirebase: onSnapshot,
      updateDocFirebase: updateDoc,
      dbFirebase: db,

      users: { doc1: "doc1" },
      flagFirebaseNotif: false,
      flagButtonLoading: false,

      // ROLE-BASED ACCESS CONTROL CONFIGURATION
      rolePermissions: {
        // ADMIN - Full access to everything
        admin: {
          components: [
            'cmpHalDashboard', 'cmpHalProfile', 'cmpUsers', 'cmpMasterCabang', 
            'cmpMasterUnit', 'cmpMasterJabatan', 'cmpMasterOrganisasi', 'cmpMasterBank',
            'cmpMasterSallary', 'cmpMasterBpjs', 'cmpMasterNpwp', 'cmpMasterKodeLokasi',
            'cmpMasterProfile', 'cmpMasterProfileDetail', 'cmpMasterKontrak', 'cmpRekKar',
            'cmpMasterTemplate', 'cmpMasterDocument', 'cmpBobotPkSoba', 'cmpTempPos',
            'cmpTempPosDetail', 'cmpSignatureMaster', 'MasterKriteria', 'KriteriaDetail',
            'cmpMasterRapor'
          ],
          menus: ['DATA', 'MASTERDATA']
        },
        
        // HR MANAGER - HR related access
        hr_manager: {
          components: [
            'cmpHalDashboard', 'cmpHalProfile', 'cmpMasterProfile', 'cmpMasterProfileDetail',
            'cmpMasterKontrak', 'cmpMasterRapor', 'MasterKriteria', 'KriteriaDetail',
            'cmpSignatureMaster', 'cmpTempPos', 'cmpTempPosDetail', 'cmpMasterBpjs',
            'cmpMasterNpwp', 'cmpRekKar'
          ],
          menus: ['DATA']
        },
        
        // REGULAR USER - Limited access
        user: {
          components: [
            'cmpHalDashboard', 'cmpHalProfile'
          ],
          menus: []
        }
      },

      // Current user data
      currentUser: null,

      menuHeader: {
        DATA: {
          cmpMasterProfile: "KARYAWAN",
          cmpMasterKontrak: "KONTRAK",
          cmpMasterProfileDetail: "KARYAWAN DETAIL",
          cmpTempPos: "POS",
          cmpTempPosDetail: "POS Detail",
          cmpMasterRapor: "Rapor",
        },
        MASTERDATA: {
          cmpUsers: "USER",
          cmpMasterCabang: "CABANG",
          cmpMasterKodeLokasi: "KODE LOKASI",
          cmpMasterUnit: "UNIT",
          cmpMasterOrganisasi: "ORGANISASI",
          cmpMasterJabatan: "JABATAN",
          cmpMasterSallary: "SALLARY",
          cmpMasterBank: "BANK",
          cmpBobotPkSoba: "BobotPkSoba",
          cmpSignatureMaster: "Tanda Tangan",
          MasterKriteria: "Kriteria",
          KriteriaDetail: "Kriteria Detail",
        },
      },

      menuUserHtml: {
        cmpMasterRapor: "Rapor",
        MasterKriteria: "Kriteria",
        KriteriaDetail: "Kriteria Detail",
        cmpSignatureMaster: "Tanda Tangan",
        cmpBobotPkSoba: "BobotPkSoba",
        cmpUsers: "USER",
        cmpTempPos: "POS",
        cmpTempPosDetail: "POS Detail",
        cmpMasterCabang: "CABANG",
        cmpMasterUnit: "UNIT",
        cmpMasterJabatan: "JABATAN",
        cmpMasterOrganisasi: "ORGANISASI",
        cmpMasterBank: "BANK",
        cmpMasterSallary: "SALLARY",
        cmpMasterBpjs: "BPJS",
        cmpMasterNpwp: "NPWP",
        cmpRekKar: "REKENING",
        cmpMasterKodeLokasi: "KODE LOKASI",
        cmpMasterProfile: "KARYAWAN",
        cmpMasterProfileDetail: "KARYAWAN DETAIL",
        cmpMasterKontrak: "KONTRAK",
        cmpMasterTemplate: "TEMPLATE",
        cmpMasterDocument: "Document",
      },

      menuHeader_status: {
        DATA: false,
        MASTERDATA: false,
      },

      accessRoles: {
        // Your existing accessRoles configuration...
        cmpMasterDocStatus: { create: true, update: true, delete: true },
        cmpMasterCabang: { create: true, update: true, delete: true },
        cmpMasterUnit: { create: true, update: true, delete: true },
        cmpMasterJabatan: { create: true, update: true, delete: true },
        cmpMasterOrganisasi: { create: true, update: true, delete: true },
        cmpMasterBank: { create: true, update: true, delete: true },
        cmpMasterSallary: { create: true, update: true, delete: true },
        cmpMasterBpjs: { create: true, update: true, delete: true },
        cmpMasterNpwp: { create: true, update: true, delete: true },
        cmpMasterKodeLokasi: { create: true, update: true, delete: true },
        cmpMasterProfile: { create: true, update: true, delete: true },
        cmpMasterProfileDetail: { create: true, update: true, delete: true },
        cmpMasterKontrak: { create: true, update: true, delete: true },
        cmpMasterTemplate: { create: true, update: true, delete: true },
        cmpMasterDocument: { create: true, update: true, delete: true },
        cmpRekKar: { create: true, update: true, delete: true },
        cmpUsers: { create: true, update: true, delete: true },
        cmpTempPos: { create: true, update: true, delete: true },
        cmpBobotPkSoba: { create: true, update: true, delete: true },
        cmpSignatureMaster: { create: true, update: true, delete: true },
        MasterKriteria: { create: true, update: true, delete: true },
        KriteriaDetail: { create: true, update: true, delete: true },
        cmpMasterRapor: { create: true, update: true, delete: true },
      },

      profile_status: false,
      hide_menu_status: true,
      judulHalaman: "",
      ref: "",
      device_info: "",
      userid: 0,
      token_firebase: "",
      notifikasi_total: 0,
      notifikasi_detail: {},
    };
  },

  computed: {
    // Check if current user has access to the current route
    hasAccessToCurrentRoute() {
      if (!this.currentUser || !this.cmpMenu) {
        return true; // Default allow if no user or menu
      }

      const userRole = this.currentUser.role;
      const rolePermission = this.rolePermissions[userRole];
      
      if (!rolePermission) {
        return false; // No permission found for this role
      }

      return rolePermission.components.includes(this.cmpMenu);
    },

    // Get filtered menu based on user role
    filteredMenuHeader() {
      if (!this.currentUser) {
        return {};
      }

      const userRole = this.currentUser.role;
      const rolePermission = this.rolePermissions[userRole];
      
      if (!rolePermission) {
        return {};
      }

      const filteredMenu = {};
      
      // Filter main menu categories
      rolePermission.menus.forEach(menuCategory => {
        if (this.menuHeader[menuCategory]) {
          filteredMenu[menuCategory] = {};
          
          // Filter components within each category
          Object.keys(this.menuHeader[menuCategory]).forEach(componentKey => {
            if (rolePermission.components.includes(componentKey)) {
              filteredMenu[menuCategory][componentKey] = this.menuHeader[menuCategory][componentKey];
            }
          });
        }
      });

      return filteredMenu;
    }
  },

  async created() {
    // Initialize user data
    await this.initializeUserData();
  },

  async mounted() {
    this.activePage();
    document.getElementById("BG_flag").remove();
    document.head.innerHTML +=
      '<link href="/src/assets/css/themes/' +
      localStorage.getItem("themes") +
      '" rel="stylesheet" id="BG_flag"/>';
  },

  methods: {
    // Initialize user data from localStorage or API
    async initializeUserData() {
      const userStr = localStorage.getItem("uObject");
      if (userStr) {
        try {
          this.currentUser = JSON.parse(userStr);
        } catch (error) {
          console.error("Error parsing user data:", error);
          this.currentUser = null;
        }
      }

      // If no user data in localStorage, try to get from API
      if (!this.currentUser && localStorage.getItem("token")) {
        try {
          const response = await axios.get('/api/check-login');
          if (response.data.success && response.data.user) {
            this.currentUser = response.data.user;
            localStorage.setItem("uObject", JSON.stringify(this.currentUser));
          }
        } catch (error) {
          console.error("Error fetching user data:", error);
        }
      }
    },

    // Check if user has access to specific component
    hasComponentAccess(componentName) {
      if (!this.currentUser) {
        return false;
      }

      const userRole = this.currentUser.role;
      const rolePermission = this.rolePermissions[userRole];
      
      return rolePermission && rolePermission.components.includes(componentName);
    },

    // Check if user has access to specific menu category
    hasMenuAccess(menuCategory) {
      if (!this.currentUser) {
        return false;
      }

      const userRole = this.currentUser.role;
      const rolePermission = this.rolePermissions[userRole];
      
      return rolePermission && rolePermission.menus.includes(menuCategory);
    },

    // Navigate to dashboard when access denied
    goToDashboard() {
      this.goto('cmpHalDashboard');
    },

    // Modified goto method with role checking
    goto: function (comp, p) {
      var that = this;

      // Check if user has access to this component
      if (!this.hasComponentAccess(comp) && comp !== 'cmpHalDashboard') {
        this.$notify({
          title: 'Access Denied',
          text: 'You do not have permission to access this page.',
          type: 'error'
        });
        return;
      }

      that.judulHalaman = "";
      that.$root.profile_status = false;
      
      if (typeof p != "undefined") {
        this.params = p;
      }
      
      localStorage.setItem("halaman", this.encryptData(comp));
      if (comp != "cmpHalDashboard") {
        sessionStorage.removeItem("first");
      }

      const tokenx = localStorage.getItem("token");
      that.cmpMenu = comp;
      that.activeRoute = that.menuUser[comp];
      that.halamanSekarang = that.cmpMenu;
    },

    // All your existing methods remain the same...
    async changeColorBG(themes) {
      this.$root.presentLoading();
      document.getElementById("BG_flag").innerHTML =
        '<link href="/src/assets/css/themes/' + themes + '" rel="stylesheet" id="BG_flag"/>';
      localStorage.setItem("themes", themes);
      this.$root.stopLoading();
    },

    async sleep(ms) {
      return new Promise((resolve) => setTimeout(resolve, ms));
    },

    get_id_user(unique) {
      if (!unique) {
        const uObject = localStorage.getItem("uObject");
        if (uObject) {
          const userData = JSON.parse(uObject);
          return userData.userId;
        }
        return 0;
      }

      if (unique == null || unique == undefined) {
        return 0;
      }
      var idx = this.decryptData(unique);
      const myArray = idx.split("ABCDEFG");
      return myArray[1] || 0;
    },

    hide_menu_status_open() {
      this.hide_menu_status = !this.hide_menu_status;
    },

    status_profil_open() {
      this.profile_status = !this.profile_status;
    },

    status_menu_open(header_menu) {
      this.menuHeader_status[header_menu] = !this.menuHeader_status[header_menu];
    },

    async presentLoading() {
      this.isLoading = true;
    },

    async stopLoading() {
      this.isLoading = false;
    },

    activePage: function () {
      console.log(this.decryptData(localStorage.getItem("halaman")));
      let tmpactiveRoute =
        localStorage.getItem("token") != null
          ? localStorage.getItem("halaman") == null ||
            localStorage.getItem("halaman") == "" ||
            localStorage.getItem("halaman") == undefined
            ? this.menuUser["cmpHalDashboard"]
            : this.menuUser[this.decryptData(localStorage.getItem("halaman"))]
          : this.menuUser["Login"];

      this.activeRoute = tmpactiveRoute;
    },

    encryptData(x) {
      const data = CryptoJS.AES.encrypt(x, this.secretCodeEnc).toString();
      return data;
    },

    decryptData(y) {
      if (y == undefined) return false;
      const data = CryptoJS.AES.decrypt(y, this.secretCodeEnc).toString(CryptoJS.enc.Utf8);
      return data;
    },

    // Add other existing methods here...
  },
};
</script>

<style scoped>
.unauthorized-access {
  padding: 20px;
  text-align: center;
}

.alert {
  padding: 15px;
  margin-bottom: 20px;
  border: 1px solid transparent;
  border-radius: 4px;
}

.alert-danger {
  color: #721c24;
  background-color: #f8d7da;
  border-color: #f5c6cb;
}

.btn {
  padding: 10px 20px;
  margin: 5px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.btn-primary {
  background-color: #007bff;
  color: white;
}

.btn-primary:hover {
  background-color: #0056b3;
}
</style>