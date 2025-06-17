<template>
  <div id="page-content" v-if="isLogin == 1" style="min-height: 850px">
    <div class="block">
      <div class="block-title">
        <h2>
          <strong>Master Kriteria Rapor</strong>
        </h2>
        <i v-if="!status_table" class="fa fa-spinner fa-spin text-default"></i>
      </div>

      <div class="block-content">
        <button
          v-if="status_table"
          class="btn btn-sm btn-primary pull-right"
          @click="show_modal()"
        >
          ADD KRITERIA
        </button>

        <div id="wrapper2"></div>
        <div id="box"></div>
      </div>
    </div>

    <!-- Modal Add/Edit -->
    <div
      :class="modal ? 'modal fade in' : 'modal fade'"
      tabindex="-1"
      role="dialog"
      aria-hidden="true"
      data-keyboard="false"
      data-backdrop="static"
      :style="{ display: modal ? 'block' : 'none' }"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            {{ todo.id ? "EDIT" : "ADD" }} KRITERIA RAPOR
            <button
              type="button"
              class="close"
              @click="show_modal(false)"
              aria-label="Close"
            >
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row mb-3">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Nama Kriteria</label>
                  <input
                    type="text"
                    class="form-control input-lg"
                    v-model="todo.kriteria"
                    :class="{ 'input-error': errorField.kriteria }"
                  />
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label>
                    <input type="checkbox" v-model="todo.is_active" /> Set sebagai kriteria aktif
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-sm btn-primary"
              @click="todo.id ? editTodo() : saveTodo()"
              :disabled="$root.flagButtonLoading"
            >
              <i
                v-if="$root.flagButtonLoading"
                class="fa fa-spinner fa-spin text-default"
              ></i>
              {{ todo.id ? "Update" : "Save" }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { Grid, h, html } from "gridjs";
import "gridjs/dist/theme/mermaid.css";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
  data() {
    return {
      isLogin: localStorage.getItem("token") != null ? 1 : 0,
      grid: new Grid(),
      userid: 0,
      status_table: false,
      modal: false,

      todo: {
        id: null,
        kriteria: "",
        is_active: true
      },

      errorField: {
        kriteria: false,
      },

      errorList: "",
    };
  },

  mounted() {
    this.getTable();
    this.userid = String(this.$root.get_id_user(localStorage.getItem("unique")));  },

  methods: {
    handleIsActiveChange(e) {
      // Konversi boolean ke string "1" atau "0"
      this.todo.is_active = e.target.checked ? "1" : "0";
    },
    getTable() {
      const mythis = this;
      this.grid = new Grid();
      this.grid.updateConfig({
        pagination: {
          limit: mythis.$root.pagingTabel1,
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
          "Nama Kriteria",
          {
            name: "Status",
            formatter: (cell, row) => {
              return html(`
                <div class="form-check d-flex justify-content-center">
                    <input type="checkbox" 
                        class="form-check-input status-checkbox" 
                        data-id="${row.cells[0].data}"
                        ${cell ? "checked" : ""} 
                        style="cursor: pointer;"
                    />
                </div>
            `);
            },
          },
          {
            name: "Action",
            formatter: (_, row) => {
              if (
                !row.cells ||
                !row.cells[0] ||
                row.cells[0].data === undefined
              ) {
                return "";
              }
              return html(`
                <button data-id="${row.cells[0].data}" class="btn btn-sm btn-warning text-white" id="editData" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil-square-o"></i></button>
                &nbsp;&nbsp;&nbsp;
                <button data-id="${row.cells[0].data}" class="btn btn-sm btn-danger text-white" id="deleteData" data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o"></i></button>
              `);
            },
          },
        ],
        style: {
          table: {
            border: "1px solid #ccc",
            width: "100%",
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
          url: this.$root.apiHost + "api/kriteria-rapor",
          then: (data) => {
            if (!data || !data.results) return [];
            return data.results.map((item, index) => [
              item.id,
              index + 1,
              item.kriteria,
              item.is_active == "1",
            ]);
          },
          total: (data) => data?.count || 0,
        },
      });

      this.grid.render(document.getElementById("wrapper2"));
      this.jqueryDelEdit();
      this.status_table = true;
    },

    show_modal(isOpen = true) {
      this.modal = isOpen;
      if (!isOpen) {
        this.resetForm();
      }
    },

    resetForm() {
      this.todo = {
        id: null,
        kriteria: "",
        is_active: true,
      };

      this.errorField.kriteria = false;
      this.errorList = "";
    },

    async saveTodo() {
      try {
        this.$root.flagButtonLoading = true;
        
        const response = await axios.post(
          this.$root.apiHost + "api/kriteria-rapor",
          {
            kriteria: this.todo.kriteria,
            is_active: this.todo.is_active,
            created_by: String(this.userid)
          }
        );

        if (response.data.status === 'success') {
          await Swal.fire("Success!", response.data.message, "success");
          this.resetForm();
          this.show_modal(false);
          this.refreshTable();
        }
      } catch (error) {
        this.handleError(error);
      } finally {
        this.$root.flagButtonLoading = false;
      }
    },

    async editTodo() {
      try {
        this.$root.flagButtonLoading = true;

        const response = await axios.put(
          `${this.$root.apiHost}api/kriteria-rapor/${this.todo.id}`,
          {
            ...this.todo,
            is_active: this.todo.is_active ? "1" : "0",
            updated_by: this.userid,
          }
        );

        await Swal.fire("Updated!", response.data.message, "success");
        this.resetForm();
        this.show_modal(false);
        this.refreshTable();
      } catch (error) {
        this.handleError(error);
      } finally {
        this.$root.flagButtonLoading = false;
      }
    },

    async getData(id) {
      try {
        this.$root.presentLoading();
        const response = await axios.get(
          `${this.$root.apiHost}api/kriteria-rapor/${id}`
        );

        if (response.data.data) {
          this.todo = {
            ...response.data.data,
            is_active: response.data.data.is_active == "1"
          };
        }
      } catch (error) {
        console.error("Error fetching data:", error);
        toast.error("Gagal mengambil data", { theme: "colored" });
      } finally {
        this.$root.stopLoading();
      }
    },

    async deleteTodo(id) {
      const result = await Swal.fire({
        title: "Delete Kriteria",
        text: "Are you sure?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        cancelButtonText: "Cancel",
      });

      if (result.isConfirmed) {
        try {
          this.$root.presentLoading();
          await axios.delete(`${this.$root.apiHost}api/kriteria-rapor/${id}`, {
            data: { deleted_by: this.userid },
          });

          await Swal.fire("Deleted!", "Data has been deleted", "success");
          this.refreshTable();
        } catch (error) {
          console.error("Error deleting data:", error);
          toast.error("Gagal menghapus data", { theme: "colored" });
        } finally {
          this.$root.stopLoading();
        }
      }
    },

    async toggleStatus(id, status) {
      try {
        this.$root.presentLoading();
        await axios.put(`${this.$root.apiHost}api/kriteria-rapor/${id}/toggle-status`, {
          is_active: status ? "1" : "0",
          updated_by: String(this.userid)
        });
        
        this.refreshTable();
        toast.success("Status berhasil diubah", { theme: "colored" });
      } catch (error) {
        console.error("Error:", error);
        toast.error("Gagal mengubah status", { theme: "colored" });
      } finally {
        this.$root.stopLoading();
      }
    },

    handleError(error) {
      if (error.response?.status === 422) {
        this.errorList = error.response.data;
        Object.keys(this.errorField).forEach((key) => {
          this.errorField[key] = false;
        });
        Object.keys(this.errorList).forEach((key) => {
          toast.error(this.errorList[key], { theme: "colored" });
          this.errorField[key] = true;
        });
      } else {
        toast.error(error.response?.data?.message || "An error occurred", {
          theme: "colored",
        });
      }
    },

    jqueryDelEdit() {
      const mythis = this;
      $(document).off("click", "#editData");
      $(document).off("click", "#deleteData");
      $(document).off("change", ".status-checkbox");

      $(document).on("click", "#editData", async (e) => {
        const id = $(e.currentTarget).data("id");
        await this.getData(id);
        this.show_modal(true);
      });

      $(document).on("click", "#deleteData", (e) => {
        const id = $(e.currentTarget).data("id");
        this.deleteTodo(id);
      });

      $(document).on("change", ".status-checkbox", function (e) {
        const id = $(this).data("id");
        const isChecked = $(this).prop("checked");
        mythis.toggleStatus(id, isChecked);
      });
    },

    refreshTable() {
      $("#wrapper2").remove();
      const e = $('<div id="wrapper2"></div>');
      $("#box").append(e);
      this.getTable();
    },
  },
};
</script>

<style scoped>
.input-error {
  border: red 1px solid;
}

.modal-dialog {
  width: 600px;
  margin: 30px auto;
}

.modal-body {
  padding: 20px;
}

.modal-content {
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  position: sticky;
  top: 0;
  background-color: white;
  z-index: 1;
}

.modal-footer {
  position: sticky;
  bottom: 0;
  background-color: white;
  z-index: 1;
}

.form-check {
  margin: 0;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: center;
}

.form-check-input {
  margin: 0;
  cursor: pointer;
  width: 18px;
  height: 18px;
}

.form-check-input:checked {
  background-color: #0037ff;
  border-color: #0037ff;
}
</style>