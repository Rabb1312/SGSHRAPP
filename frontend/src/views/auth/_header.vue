<template>
  <div id="container" v-if="isLogin == 1">
    <!-- Main Sidebar -->
    <div id="sidebar">
      <!-- Wrapper for scrolling functionality -->
      <div id="sidebar-scroll">
        <!-- Sidebar Content -->
        <div class="sidebar-content">
          <!-- Brand -->
          <a
            href="javascript:void(0);"
            class="sidebar-brand"
            style="pointer-events: none"
          >
            <img :src="imageUrl" alt="avatar" class="widget-image" width="50" />
          </a>

          <!-- Sidebar Navigation -->
          <ul class="sidebar-nav">
            <template v-if="isRegistrasi == 'sudah'">
              <!-- Dashboard - Always visible for logged-in users -->
              <li>
                <template v-if="this.$root.halamanSekarang == 'cmpHalDashboard'">
                  <a
                    href="javascript:void(0);"
                    @click="$root.goto('cmpHalDashboard')"
                    class="active"
                  >
                    <i class="gi gi-stopwatch sidebar-nav-icon"></i>
                    <span class="sidebar-nav-mini-hide">DASHBOARD</span>
                  </a>
                </template>
                <template v-else>
                  <a
                    href="javascript:void(0);"
                    @click="$root.goto('cmpHalDashboard')"
                  >
                    <i class="gi gi-stopwatch sidebar-nav-icon"></i>
                    <span class="sidebar-nav-mini-hide">DASHBOARD</span>
                  </a>
                </template>
              </li>

              <!-- Role-based Menu Items -->
              <li v-for="(value, key, index) in filteredMenuHeader" :key="key">
                <a
                  href="javascript:void(0);"
                  :class="
                    $root.menuHeader_status[key]
                      ? 'sidebar-nav-menu open'
                      : 'sidebar-nav-menu'
                  "
                  @click="$root.status_menu_open(key)"
                >
                  <i class="fa fa-angle-left sidebar-nav-indicator sidebar-nav-mini-hide"></i>
                  <i :class="menuHeader_logo[key]"></i>
                  <span class="sidebar-nav-mini-hide">{{ key }}</span>
                </a>
                <ul
                  :style="
                    $root.menuHeader_status[key]
                      ? 'display: block;'
                      : 'display: none;'
                  "
                >
                  <li v-for="(value2, key2, index2) in value" :key="key2">
                    <template v-if="typeof value2 === 'object' && value2 !== null">
                      <a
                        href="javascript:void(0);"
                        :class="
                          $root.menuHeader_status[key2]
                            ? 'sidebar-nav-menu open'
                            : 'sidebar-nav-menu'
                        "
                        @click="$root.status_menu_open(key2)"
                      >
                        <i class="fa fa-angle-left sidebar-nav-indicator"></i>
                        {{ key2 }}
                      </a>
                      <ul
                        :style="
                          $root.menuHeader_status[key2]
                            ? 'display: block;'
                            : 'display: none;'
                        "
                      >
                        <li v-for="(value3, key3, index3) in value2" :key="key3">
                          <template v-if="typeof value3 === 'object' && value3 !== null">
                            <a
                              href="javascript:void(0);"
                              :class="
                                $root.menuHeader_status[key3]
                                  ? 'sidebar-nav-menu open'
                                  : 'sidebar-nav-menu'
                              "
                              @click="$root.status_menu_open(key3)"
                            >
                              <i class="fa fa-angle-left sidebar-nav-indicator"></i>
                              {{ key3 }}
                            </a>
                            <ul
                              :style="
                                $root.menuHeader_status[key3]
                                  ? 'display: block;'
                                  : 'display: none;'
                              "
                            >
                              <li v-for="(value4, key4, index4) in value3" :key="key4">
                                <template v-if="typeof value4 === 'object' && value4 !== null">
                                  <a
                                    href="javascript:void(0);"
                                    :class="
                                      $root.menuHeader_status[key4]
                                        ? 'sidebar-nav-menu open'
                                        : 'sidebar-nav-menu'
                                    "
                                    @click="$root.status_menu_open(key4)"
                                  >
                                    <i class="fa fa-angle-left sidebar-nav-indicator"></i>
                                    {{ key4 }}
                                  </a>
                                  <ul
                                    :style="
                                      $root.menuHeader_status[key4]
                                        ? 'display: block;padding-left: 25px;'
                                        : 'display: none;padding-left: 25px;'
                                    "
                                  ></ul>
                                </template>
                                <template v-else>
                                  <template v-if="key4 != 'cmpHalProfile' && hasComponentAccess(key4)">
                                    <template v-if="this.$root.halamanSekarang == key4">
                                      <li>
                                        <a
                                          href="javascript:void(0);"
                                          @click="$root.goto(key4)"
                                          class="active"
                                        >
                                          {{ menuUserHtml[key4] }}
                                        </a>
                                      </li>
                                    </template>
                                    <template v-else>
                                      <li>
                                        <a
                                          href="javascript:void(0);"
                                          @click="$root.goto(key4)"
                                        >
                                          {{ menuUserHtml[key4] }}
                                        </a>
                                      </li>
                                    </template>
                                  </template>
                                </template>
                              </li>
                            </ul>
                          </template>
                          <template v-else>
                            <template v-if="key3 != 'cmpHalProfile' && hasComponentAccess(key3)">
                              <template v-if="this.$root.halamanSekarang == key3">
                                <a
                                  href="javascript:void(0);"
                                  @click="$root.goto(key3)"
                                  class="active"
                                >
                                  {{ menuUserHtml[key3] }}
                                </a>
                              </template>
                              <template v-else>
                                <a
                                  href="javascript:void(0);"
                                  @click="$root.goto(key3)"
                                >
                                  {{ menuUserHtml[key3] }}
                                </a>
                              </template>
                            </template>
                          </template>
                        </li>
                      </ul>
                    </template>
                    <template v-else>
                      <template v-if="key2 != 'cmpHalProfile' && hasComponentAccess(key2)">
                        <template v-if="this.$root.halamanSekarang == key2">
                          <a
                            href="javascript:void(0);"
                            @click="$root.goto(key2)"
                            class="active"
                          >
                            {{ menuUserHtml[key2] }}
                          </a>
                        </template>
                        <template v-else>
                          <a
                            href="javascript:void(0);"
                            @click="$root.goto(key2)"
                          >
                            {{ menuUserHtml[key2] }}
                          </a>
                        </template>
                      </template>
                    </template>
                  </li>
                </ul>
              </li>

              <!-- User Role Badge (Optional - untuk menampilkan role user) -->
              <li class="user-role-badge" v-if="currentUser">
                <a href="javascript:void(0);" style="pointer-events: none; opacity: 0.7;">
                  <i class="fa fa-user sidebar-nav-icon"></i>
                  <span class="sidebar-nav-mini-hide">
                    {{ currentUser.role.toUpperCase() }}
                  </span>
                </a>
              </li>
            </template>
          </ul>
          <!-- END Sidebar Navigation -->
        </div>
        <!-- END Sidebar Content -->
      </div>
      <!-- END Wrapper for scrolling functionality -->
    </div>
    <!-- END Main Sidebar -->
  </div>
</template>

<script>
import dummyLogo from "@/assets/img/logo-sgs3.png";

export default {
  data() {
    return {
      menu: ["header"],
      userName: null,
      vModelAvatar: null,
      menuUser: null,
      menuUserHtml: null,
      menuHeader: null,
      isLogin: localStorage.getItem("token") != null ? 1 : 0,
      activemenu: null,
      isRegistrasi: null,
      timestamp: "",
      imageUrl: dummyLogo,
      currentUser: null,
      
      // Menu icons mapping
      menuHeader_logo: {
        DATA: "gi gi-group sidebar-nav-icon",
        MASTERDATA: "gi gi-settings sidebar-nav-icon",
        HRIS: "gi gi-group sidebar-nav-icon",
        WMS: "gi gi-shopping_cart sidebar-nav-icon",
        SALES: "gi gi-money sidebar-nav-icon",
        MASTER: "gi gi-settings sidebar-nav-icon",
        TRANSACTION: "gi gi-transfer sidebar-nav-icon",
      },
      
      monthNames: [
        "Jan", "Feb", "Mar", "Apr", "Mei", "Jun",
        "Jul", "Ags", "Sept", "Okt", "Nov", "Des",
      ],
      
      // Role permissions (synced with backend)
      rolePermissions: {
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
        hr_manager: {
          components: [
            'cmpHalDashboard', 'cmpHalProfile', 'cmpMasterProfile', 'cmpMasterProfileDetail',
            'cmpMasterKontrak', 'cmpMasterRapor', 'MasterKriteria', 'KriteriaDetail',
            'cmpSignatureMaster', 'cmpTempPos', 'cmpTempPosDetail', 'cmpMasterBpjs',
            'cmpMasterNpwp', 'cmpRekKar'
          ],
          menus: ['DATA']
        },
        user: {
          components: [
            'cmpHalDashboard', 'cmpHalProfile'
          ],
          menus: []
        }
      },
    };
  },

  computed: {
    // Get filtered menu based on user role
    filteredMenuHeader() {
      if (!this.currentUser || !this.$root.menuHeader) {
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
        if (this.$root.menuHeader[menuCategory]) {
          filteredMenu[menuCategory] = {};
          
          // Filter components within each category
          Object.keys(this.$root.menuHeader[menuCategory]).forEach(componentKey => {
            if (rolePermission.components.includes(componentKey)) {
              filteredMenu[menuCategory][componentKey] = this.$root.menuHeader[menuCategory][componentKey];
            }
          });
        }
      });

      return filteredMenu;
    }
  },

  async mounted() {
    // Initialize menu data from root
    this.menuUser = this.$root.menuUser;
    this.menuUserHtml = this.$root.menuUserHtml;

    // Get user registration status
    if (localStorage.getItem("registrasi") != null) {
      this.isRegistrasi = this.$root.decryptData(
        localStorage.getItem("registrasi")
      );
      this.userName = this.$root.decryptData(localStorage.getItem("name"));
    }

    // Get current page
    if (localStorage.getItem("halaman") != null) {
      this.$root.halamanSekarang = this.$root.decryptData(
        localStorage.getItem("halaman")
      );
    }

    // Initialize user data
    await this.initializeUserData();

    // Get user avatar
    if (localStorage.getItem("uObject") != null) {
      let uObject = JSON.parse(localStorage.getItem("uObject"));
      this.vModelAvatar = uObject.userAvatar;
      this.currentUser = uObject;
    }
  },

  created() {
    // Initialize any periodic tasks if needed
  },

  methods: {
    // Initialize user data
    async initializeUserData() {
      try {
        // Get user from localStorage first
        const userStr = localStorage.getItem("uObject");
        if (userStr) {
          this.currentUser = JSON.parse(userStr);
        }

        // Verify with backend if token exists
        const token = localStorage.getItem("token");
        if (token && !this.currentUser) {
          const response = await axios.get('/api/check-login', {
            headers: {
              'Authorization': `Bearer ${token}`
            }
          });

          if (response.data.success && response.data.user) {
            this.currentUser = response.data.user;
            localStorage.setItem("uObject", JSON.stringify(this.currentUser));
          }
        }
      } catch (error) {
        console.error("Error initializing user data:", error);
        // Clear invalid data
        localStorage.removeItem("token");
        localStorage.removeItem("uObject");
        this.currentUser = null;
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

    getNow: function () {
      const today = new Date();
      const date =
        today.getDate() +
        " " +
        this.monthNames[today.getMonth()] +
        " " +
        today.getFullYear();
      const time =
        today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
      const dateTime = date + " " + time;
      this.timestamp = dateTime;
    },
  },
};
</script>

<style scoped>
.user-role-badge {
  margin-top: 10px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding-top: 10px;
}

.user-role-badge a {
  color: #3498db !important;
  font-size: 12px;
  font-weight: bold;
}

/* Hide menu items that user doesn't have access to */
.sidebar-nav li[v-if="false"] {
  display: none !important;
}
</style>