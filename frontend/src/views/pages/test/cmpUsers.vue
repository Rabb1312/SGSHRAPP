<template>
  <!---------------------------- Modal -->
  <div
    :class="modal ? 'modal fade in' : 'modal fade'"
    id="exampleModalCenter"
    tabindex="-1"
    role="dialog"
    aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true"
    data-keyboard="false"
    data-backdrop="true"
    :style="modal ? 'display: block;' : 'display: none;'"
  >
    <div class="modal-dialog2 modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          {{ flagButtonAdd ? "ADD" : "UPDATE" }} DATA {{ $root.judulHalaman }}
          <button
            id="closeModal"
            type="button"
            class="close"
            data-dismiss="modal"
            aria-label="Close"
            :disabled="$root.flagButtonLoading"
            @click="show_modal()"
          >
            <span aria-hidden="true">X</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- <pre>{{ todo }}</pre> -->
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Email</label>
                  <CmpInputText
                    id="inputEmail"
                    type="email"
                    placeholder="Email"
                    v-model="todo.email"
                    :class="
                      errorField.email
                        ? 'form-control input-lg input-error'
                        : 'form-control input-lg'
                    "
                  />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Name</label>
                  <CmpInputText
                    type="text"
                    placeholder="Name"
                    v-model="todo.name"
                    :class="
                      errorField.name
                        ? 'form-control input-lg input-error'
                        : 'form-control input-lg'
                    "
                    @input="(val) => (todo.name = todo.name)"
                  />
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Password</label>
                  <div class="input-group">
                    <CmpInputText
                      :type="showPassword ? 'text' : 'password'"
                      placeholder="Password"
                      v-model="todo.password"
                      :class="
                        errorField.password
                          ? 'form-control input-lg input-error'
                          : 'form-control input-lg'
                      "
                    />
                    <span
                      class="input-group-addon"
                      style="border-color: #6c6c6c"
                    >
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
              <div class="col-md-6">
                <div class="form-group">
                  <label>Role</label>
                  <CmpInputText
                    id="inputrole"
                    type="role"
                    placeholder="role"
                    v-model="todo.role"
                    :class="
                      errorField.role
                        ? 'form-control input-lg input-error'
                        : 'form-control input-lg'
                    "
                  />
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Status</label>
                  <select
                    v-model="todo.status"
                    :class="
                      errorField.status
                        ? 'form-control input-lg input-error'
                        : 'form-control input-lg'
                    "
                  >
                    <option :value="true">Active</option>
                    <option :value="false">Inactive</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <div class="form-group form-actions">
              <div class="col-xs-12">
                <button
                  v-if="flagButtonAdd"
                  @click="saveTodo()"
                  type="button"
                  class="btn btn-sm btn-primary pull-left"
                  :disabled="
                    $root.flagButtonLoading ||
                    !todo.email ||
                    !todo.name ||
                    !todo.password ||
                    !todo.role
                  "
                >
                  <i
                    v-if="$root.flagButtonLoading"
                    class="fa fa-spinner fa-spin text-default"
                  ></i>
                  SAVE DATA
                </button>

                <button
                  v-if="!flagButtonAdd"
                  @click="editTodo()"
                  type="button"
                  class="btn btn-sm btn-primary pull-left"
                  :disabled="
                    $root.flagButtonLoading || !todo.email || !todo.name
                  "
                >
                  <i
                    v-if="$root.flagButtonLoading"
                    class="fa fa-spinner fa-spin text-default"
                  ></i>
                  UPDATE DATA
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!---------------------------- Modal -->

  <!-- Page content -->
  <div id="page-content" v-if="isLogin == 1" style="min-height: 850px">
    <div class="block">
      <div class="block-title">
        <h2>
          <strong>MENU {{ $root.judulHalaman }}</strong>
        </h2>

        <i v-if="!status_table" class="fa fa-spinner fa-spin text-default"></i>
      </div>

      <div class="block-content">
        <button
          v-if="status_table && $root.accessRoles[access_page].create"
          class="btn btn-sm btn-primary pull-right"
          @click="show_modal()"
        >
          ADD DATA
        </button>

        <div id="wrapper2"></div>
        <div id="box"></div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { markRaw } from "vue";
import { Grid, h, html } from "gridjs";
import "gridjs/dist/theme/mermaid.css";

import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
  data() {
    return {
      access_page: this.$root.decryptData(localStorage.getItem("halaman")),
      isLogin: localStorage.getItem("token") != null ? 1 : 0,
      grid: new Grid(),

      showPassword: false,
      errorField: {
        email: false,
        name: false,
        password: false,
        role: false,
        status: false,
      },

      userid: 0,
      status_table: false,
      modal: false,

      todo: {
        email: "",
        name: "",
        password: "",
        role: "",
        status: "",
      },

      flagButtonAdd: true,
    };
  },
  mounted() {
    this.getTable();
    this.userid = this.$root.get_id_user(localStorage.getItem("unique"));
  },
  methods: {
    resetForm() {
      Object.keys(this.errorField).forEach((key) => {
        this.errorField[key] = false;
        this.todo[key] = "";
      });
      this.isEditingPassword = false;
      this.showPassword = false;
    },
    refreshTable() {
      this.status_table = false;
      $("#wrapper2").remove();
      var e = $('<div id="wrapper2"></div>');
      $("#box").append(e);
      this.getTable();
    },
    saveTodo() {
      Swal.fire({
        title: "Create User Data",
        text: "Are you sure?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        cancelButtonText: "Cancel",
      }).then((result) => {
        if (result.isConfirmed) {
          this.$root.presentLoading();
          this.$root.flagButtonLoading = true;

          axios
            .post(this.$root.apiHost + "api/users", {
              email: this.todo.email,
              password: this.todo.password,
              name: this.todo.name,
              role: this.todo.role,
              status: this.todo.status,
              userid: this.userid,
            })
            .then((res) => {
              Swal.fire("Created!", res.data.message, "success");
              this.$root.stopLoading();
              this.$root.flagButtonLoading = false;
              this.resetForm();
              this.show_modal();
              this.refreshTable();
            })
            .catch((error) => {
              this.$root.flagButtonLoading = false;
              this.$root.stopLoading();
              if (error.response) {
                if (error.response.status == 422) {
                  const errorList = error.response.data;
                  Object.keys(this.errorField).forEach((key) => {
                    this.errorField[key] = false;
                  });
                  Object.keys(errorList).forEach((key) => {
                    toast.error(errorList[key], {
                      theme: "colored",
                      onClose: () => this.show_modal(false),
                      autoClose: 2500,
                    });
                    this.errorField[key] = true;
                  });
                } else {
                  toast.error(error.response.data.message, {
                    theme: "colored",
                    onClose: () => this.show_modal(false),
                    autoClose: 2500,
                  });
                }
              }
            });
        }
      });
    },
    show_modal() {
      this.modal = !this.modal;
      if (!this.modal) {
        this.flagButtonAdd = true;
        this.resetForm();
      }
    },
    toggleShow() {
      this.showPassword = !this.showPassword;
    },
    async jqueryDelEdit() {
      const mythis = this;
      $(document).on("click", "#editData", async function () {
        let id = $(this).data("id");
        await mythis.getData(id);
        mythis.show_modal();
      });
      $(document).on("click", "#deleteData", function () {
        let id = $(this).data("id");
        mythis.deleteTodo(id);
      });
    },
    getTable() {
      this.grid = new Grid({
        pagination: {
          limit: this.$root.pagingTabel1,
          server: {
            url: (prev, page, limit) =>
              `${prev}${prev.includes("?") ? "&" : "?"}limit=${limit}&offset=${
                page * limit
              }`,
          },
        },
        search: {
          server: {
            url: (prev, keyword) => `${prev}?search=${keyword}`,
          },
        },
        columns: [
          { name: "ID", hidden: true },
          "No",
          "Email",
          "Name",
          "Role",
          "Status",
          {
            name: "Action",
            formatter: (_, row) =>
              this.$root.accessRoles[this.access_page].update &&
              this.$root.accessRoles[this.access_page].delete
                ? html(
                    `
                      <button data-id="${row.cells[0].data}" class="btn btn-sm btn-warning text-white" id="editData"><i class="fa fa-pencil-square-o"></i></button>
                      &nbsp;
                      <button data-id="${row.cells[0].data}" class="btn btn-sm btn-danger text-white" id="deleteData"><i class="fa fa-trash-o"></i></button>
                    `
                  )
                : this.$root.accessRoles[this.access_page].update
                ? html(
                    `<button data-id="${row.cells[0].data}" class="btn btn-sm btn-warning text-white" id="editData"><i class="fa fa-pencil-square-o"></i></button>`
                  )
                : this.$root.accessRoles[this.access_page].delete
                ? html(
                    `<button data-id="${row.cells[0].data}" class="btn btn-sm btn-danger text-white" id="deleteData"><i class="fa fa-trash-o"></i></button>`
                  )
                : "",
          },
        ],
        style: {
          table: {
            border: "1px solid #ccc",
          },
          th: {
            "background-color": "rgba(0, 55, 255, 0.1)",
            color: "#000",
            "border-bottom": "1px solid #ccc",
            "text-align": "center",
          },
          td: {
            "text-align": "center",
          },
        },
        server: {
          url: this.$root.apiHost + "api/users",
          then: (data) =>
            data.results.map((user) => [
              user.id,
              data.nomorBaris++,
              html(`<span class="pull-left">${user.email}</span>`),
              html(`<span class="pull-left">${user.name}</span>`),
              html(`<span class="pull-left">${user.role}</span>`),
              html(
                `<span class="label label-${
                  user.status ? "success" : "danger"
                }">${user.status ? "Active" : "Inactive"}</span>`
              ),
            ]),
          total: (data) => data.count,
        },
      });

      this.grid.render(document.getElementById("wrapper2"));
      $(document).off("click", "#editData");
      $(document).off("click", "#deleteData");
      this.jqueryDelEdit();
      this.status_table = true;
    },
    deleteTodo(id) {
      Swal.fire({
        title: "Delete User",
        text: "Are you sure?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        cancelButtonText: "Cancel",
      }).then((result) => {
        if (result.isConfirmed) {
          this.$root.presentLoading();
          axios
            .delete(this.$root.apiHost + `api/users/${id}`, {
              data: { userid: this.userid },
            })
            .then((res) => {
              Swal.fire("Deleted!", "User has been deleted", "success");
              this.$root.stopLoading();
              this.refreshTable();
              this.resetForm();
            })
            .catch((error) => {
              this.$root.stopLoading();
              toast.error(error.response?.data?.message || "Delete failed", {
                theme: "colored",
              });
            });
        }
      });
    },
    async getData(id) {
      this.flagButtonAdd = false;
      this.$root.presentLoading();
      this.todo = {};

      try {
        const response = await axios.get(
          this.$root.apiHost + `api/users/${id}`
        );
        const user = response.data.data;

        this.todo = {
          id: id,
          email: user.email,
          name: user.name,
          role: user.role,
          status: user.status,
          password: "", // Password kosong untuk update
        };

        this.isEditingPassword = false;
        this.showPassword = false;

        document.getElementById("inputEmail").focus();
        this.$root.stopLoading();
      } catch (error) {
        this.$root.stopLoading();
        toast.error(
          error.response?.data?.message || "Failed to get user data",
          {
            theme: "colored",
          }
        );
      }
    },
    editTodo() {
      this.$root.flagButtonLoading = true;

      const updateData = {
        email: this.todo.email,
        name: this.todo.name,
        role: this.todo.role,
        status: this.todo.status,
        userid: this.userid,
      };

      if (this.todo.password) {
        updateData.password = this.todo.password;
      }

      axios
        .put(this.$root.apiHost + "api/users/" + this.todo.id, {
          email: this.todo.email,
          password: this.todo.password || undefined, // Kirim password hanya jika diisi
          name: this.todo.name,
          role: this.todo.role,
          status: this.todo.status,
          updated_by: this.userid,
        })
        .then((res) => {
          Swal.fire("Updated!", res.data.message, "success");
          this.$root.flagButtonLoading = false;
          this.resetForm();
          this.show_modal();
          this.refreshTable();
        })
        .catch((error) => {
          this.$root.flagButtonLoading = false;
          if (error.response) {
            if (error.response.status == 422) {
              const errorList = error.response.data;
              Object.keys(this.errorField).forEach((key) => {
                this.errorField[key] = false;
              });

              Object.keys(errorList).forEach((key) => {
                toast.error(errorList[key], {
                  theme: "colored",
                });
                this.errorField[key] = true;
              });
            } else {
              toast.error(error.response.data.message, {
                theme: "colored",
              });
            }
          }
        });
    },
  },
};
</script>

<style scoped>
.input-error {
  border: red 1px solid;
}
.modal-dialog2 {
  width: 80%;
  margin: 30px auto;
}
</style>
