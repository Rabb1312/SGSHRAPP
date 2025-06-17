<template>
  <!-- Header -->
  <header class="navbar navbar-default">
    <!-- Left Header Navigation -->
    <ul class="nav navbar-nav-custom">
      <!-- Main Sidebar Toggle Button -->
      <li>
        <a href="javascript:void(0)" @click="$root.hide_menu_status_open()">
          <i class="fa fa-bars fa-fw"></i>
        </a>
      </li>
      <!-- END Main Sidebar Toggle Button -->
    </ul>

    <!-- END Left Header Navigation -->

    <!-- Search Form -->
    <!-- <form
      action="page_ready_search_results.html"
      method="post"
      class="navbar-form-custom"
    >
      <div class="form-group">
        <input
          type="text"
          id="top-search"
          name="top-search"
          class="form-control"
          placeholder="Search.."
        />
      </div>
    </form> -->
    <!-- END Search Form -->

    <!-- Right Header Navigation -->
    <ul class="nav navbar-nav-custom pull-right">
      <!-- User Dropdown -->
      <li :class="$root.profile_status ? 'dropdown open' : 'dropdown'">
        <a
          href="javascript:void(0)"
          class="dropdown-toggle"
          data-toggle="dropdown"
          :aria-expanded="$root.profile_status ? 'true' : 'false'"
          @click="$root.status_profil_open()"
        >
          <img :src="vModelAvatar" alt="avatar" />
          <i class="fa fa-angle-down"></i>
        </a>

        <ul class="dropdown-menu dropdown-custom dropdown-menu-right">
          <li class="dropdown-header text-center">Account</li>
          <li>
            <a href="javascript:void(0)" @click="$root.goto('cmpHalProfile')"
              ><i class="fa fa-user fa-fw pull-right"></i> Profile</a
            >
          </li>
          <li>
            <a href="javascript:void(0)" @click="logout"
              ><i class="fa fa-power-off fa-fw pull-right"></i> Logout</a
            >
          </li>
          <li class="divider"></li>
        </ul>
      </li>
      <!-- END User Dropdown -->
    </ul>

    <ul class="nav navbar-nav-custom pull-right">
      <!-- Main Sidebar Toggle Button -->
      <li>
        <!-- <div class="icon">
          <i class="hi hi-bell" @click="show_notifx()"></i>
        </div> -->

        <span class="badge" v-if="$root.notifikasi_total > 0">{{
          $root.notifikasi_total
        }}</span>
        <div
          class="notifications"
          :style="css_notif"
          v-if="$root.notifikasi_detail.length > 0"
        >
          <h2 style="margin: 0"><strong>Notifications</strong></h2>
          <template
            v-for="(value, key, index) in $root.notifikasi_detail"
            :key="key"
          >
            <div
              class="notifications-item"
              :style="value.isSent ? '' : 'background-color: #fff6ed'"
              @click="$root.update_notif(value.id)"
            >
              <div class="text">
                <h4>{{ value.notification_header }}</h4>
                <p>
                  {{ value.notification_body }} <br />
                  {{ value.created_at }}
                </p>
              </div>
            </div>
          </template>
        </div>
      </li>
      <!-- END Main Sidebar Toggle Button -->
    </ul>

    <div
      class="nav navbar-nav-custom pull-right"
      style="margin-top: 7px; margin-right: 10px"
    >
      <b>
        <h5>&nbsp;Hi, {{ userName }}</h5></b
      >
    </div>
    <!-- END Right Header Navigation -->
  </header>
  <!-- END Header -->
</template>

<script>
import { Device } from "@capacitor/device";
import { toast } from "vue3-toastify";
import Swal from 'sweetalert2';
import axios from "axios";


export default {
  data() {
    return {
      userName: null,
      vModelAvatar: null,
    };
  },
  mounted() {
    // Load user data from localStorage
    let uObject = JSON.parse(localStorage.getItem("uObject"));
    if (uObject) {
      this.vModelAvatar = uObject.userAvatar;
      this.userName = uObject.userName;
    }
  },
  methods: {
    async logout() {
    try {
      const result = await Swal.fire({
        title: "Logout Apps",
        text: "Are you sure?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Logout!",
        cancelButtonText: "Cancel",
      });

      if (result.isConfirmed) {
          // Panggil API logout
        const token = localStorage.getItem('token');
          await axios.post(`${this.$root.apiHost}api/logout`, {}, {
            headers: {
              'Authorization': token
            }
          });

          // Clear storage
          localStorage.clear();
        sessionStorage.clear();

        // Tampilkan pesan sukses
        toast.success("Logout Successful", { theme: "colored" });

          // Redirect ke login page
        setTimeout(() => {
            window.location.href = '/login';
        }, 1500);
      }
    } catch (error) {
      console.error('Logout error:', error);
        toast.error("Logout failed", { theme: "colored" });
    }
  }
  }
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Manrope:wght@200&display=swap");

.icon {
  cursor: pointer;
  margin: 15px;
}
.icon span {
  background: #f00;
  padding: 7px;
  border-radius: 50%;
  color: #fff;
  vertical-align: top;
  margin-left: -25px;
}
.icon img {
  display: inline-block;
  width: 26px;
  margin-top: 4px;
}
.icon:hover {
  opacity: 0.7;
}
.logo {
  flex: 1;
  margin-left: 50px;
  color: #eee;
  font-size: 20px;
  font-family: monospace;
}
.notifications {
  width: 500px;
  height: 0px;
  opacity: 0;
  position: absolute;
  right: 12px;
  border-radius: 5px 0px 5px 5px;
  background-color: #fff;
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
}
.notifications h2 {
  font-size: 14px;
  padding: 10px;
  border-bottom: 1px solid #eee;
  color: #999;
}
.notifications h2 span {
  color: #f00;
}
.notifications-item {
  display: flex;
  border-bottom: 1px solid #eee;
  padding: 6px 9px;
  margin-bottom: 0px;
  cursor: pointer;
}
.notifications-item:hover {
  background-color: #eee;
}
.notifications-item img {
  display: block;
  width: 50px;
  height: 50px;
  margin-right: 9px;
  border-radius: 50%;
  margin-top: 2px;
}
.notifications-item .text h4 {
  color: #777;
  font-size: 16px;
  margin-top: 3px;
}
.notifications-item .text p {
  color: #aaa;
  font-size: 12px;
}

.badge {
  position: absolute;
  font-size: 8px;
  top: 2px;
  right: 6px;
  background: red;
  color: white;
  padding: 5px 5px;
  border-radius: 50%;
}
</style>
