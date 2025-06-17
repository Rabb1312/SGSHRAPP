<template>
  <div id="page-content" v-if="isLogin == 1" style="min-height: 850px">
    <div class="block">
      <div class="block-title">
        <h2>
          <strong>Detail Kriteria Rapor</strong>
        </h2>
        <i v-if="!status_table" class="fa fa-spinner fa-spin text-default"></i>
      </div>

      <div class="block-content">
        <button
          v-if="status_table"
          class="btn btn-sm btn-primary pull-right"
          @click="show_modal()"
        >
          ADD DETAIL KRITERIA
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
            {{ todo.id ? "EDIT" : "ADD" }} DETAIL KRITERIA RAPOR
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
                  <label>Jabatan</label>
                  <select
                    class="form-control input-lg"
                    v-model="todo.jabatan_id"
                    :class="{ 'input-error': errorField.jabatan_id }"
                  >
                    <option value="">Pilih Jabatan</option>
                    <option
                      v-for="jabatan in jabatanList"
                      :key="jabatan.id"
                      :value="jabatan.id"
                    >
                      {{ jabatan.jabatan }}
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Kriteria</label>
                  <div class="kriteria-list">
                    <div
                      v-for="kriteria in kriteriaList.filter(
                        (item) => item.is_active === '1'
                      )"
                      :key="kriteria.id"
                    >
                      <label>
                        <input
                          type="checkbox"
                          :value="kriteria.id"
                          v-model="todo.kriteria_ids"
                        />
                        {{ kriteria.kriteria }}
                      </label>
                    </div>
                  </div>
                  <div v-if="errorField.kriteria_ids" class="text-danger">
                    Pilih minimal satu kriteria
                  </div>
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
      userid: "",
      status_table: false,
      modal: false,

      jabatanList: [],
      kriteriaList: [],

      todo: {
        id: null,
        jabatan_id: "",
        kriteria_ids: [], // Array untuk menyimpan multiple kriteria
      },

      errorField: {
        jabatan_id: false,
        kriteria_ids: false,
      },

      errorList: "",
    };
  },

  async mounted() {
    this.userid = String(
      this.$root.get_id_user(localStorage.getItem("unique"))
    );
    await this.getKriteriaList();
    await this.getJabatanList();
    this.getTable();
  },

  methods: {
    async toggleStatus(id, status) {
      try {
        this.$root.presentLoading();
        await axios.put(
          `${this.$root.apiHost}api/detail-kriteria-rapor/${id}/toggle-status`,
          {
            is_active: status ? "1" : "0", // Ubah ke string "1" atau "0"
            updated_by: String(this.userid),
          }
        );

        toast.success(`Kriteria ${status ? "diaktifkan" : "dinonaktifkan"}`, {
          theme: "colored",
        });
        this.refreshTable();
      } catch (error) {
        console.error("Error:", error);
        toast.error("Gagal mengubah status", { theme: "colored" });
        // Kembalikan state checkbox jika gagal
        const checkbox = $(`.status-checkbox[data-id="${id}"]`);
        if (checkbox.length) {
          checkbox.prop("checked", !status);
        }
      } finally {
        this.$root.stopLoading();
      }
    },
    async getKriteriaList() {
      try {
        const response = await axios.get(
          this.$root.apiHost + "api/kriteria-rapor?is_active=1"
        );
        this.kriteriaList = response.data.results;
      } catch (error) {
        console.error("Error fetching kriteria:", error);
        toast.error("Gagal mengambil data kriteria");
      }
    },

    async getJabatanList() {
      try {
        const response = await axios.get(
          this.$root.apiHost + "api/jabatanalldata"
        );
        console.log("Response jabatan:", response.data); // Untuk debug
        this.jabatanList = response.data; // Langsung assign response data ke jabatanList
      } catch (error) {
        console.error("Error fetching jabatan:", error);
        toast.error("Gagal mengambil data jabatan");
      }
    },

    getTable() {
      const mythis = this;
      this.grid = new Grid({
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
          "Jabatan",
          "Kriteria",
          {
            name: "Status",
            formatter: (cell, row) => {
              return html(`
      <div class="form-check d-flex justify-content-center">
        <input type="checkbox" 
          class="form-check-input status-checkbox" 
          data-id="${row.cells[0].data}"
          ${cell === "1" ? "checked" : ""}  // Sesuaikan dengan string "1"
          style="cursor: pointer;"
        />
      </div>
    `);
            },
          },
          {
            name: "Action",
            formatter: (_, row) => {
              if (!row.cells?.[0]?.data) return "";
              return html(`
            <button data-id="${row.cells[0].data}" class="btn btn-sm btn-warning text-white" id="editData">
              <i class="fa fa-pencil-square-o"></i>
            </button>
            &nbsp;
            <button data-id="${row.cells[0].data}" class="btn btn-sm btn-danger text-white" id="deleteData">
              <i class="fa fa-trash-o"></i>
            </button>
          `);
            },
          },
        ],
        server: {
          url: this.$root.apiHost + "api/detail-kriteria-rapor?is_active=1",
          then: (data) => {
            if (!data?.results) return [];

            // Group by jabatan_id
            const grouped = {};
            data.results.forEach((item) => {
              if (!grouped[item.jabatan_id]) {
                grouped[item.jabatan_id] = {
                  id: item.id,
                  jabatan: item.jabatan?.jabatan,
                  kriteria: [],
                  is_active: item.is_active,
                };
              }
              if (item.kriteria) {
                grouped[item.jabatan_id].kriteria.push(item.kriteria.kriteria);
              }
            });

            // Convert to array format
            return Object.values(grouped).map((item, index) => [
              item.id,
              index + 1,
              item.jabatan || "-",
              item.kriteria.join(", "),
              item.is_active,
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
        jabatan_id: "",
        kriteria_ids: [],
      };
      this.errorField = {
        jabatan_id: false,
        kriteria_ids: false,
      };
      this.errorList = "";
    },

    validateForm() {
      let isValid = true;

      if (!this.todo.jabatan_id) {
        this.errorField.jabatan_id = true;
        toast.error("Pilih jabatan");
        isValid = false;
      }

      if (this.todo.kriteria_ids.length === 0) {
        this.errorField.kriteria_ids = true;
        toast.error("Pilih minimal satu kriteria");
        isValid = false;
      }

      return isValid;
    },

    async saveTodo() {
      if (!this.validateForm()) return;

      try {
        this.$root.flagButtonLoading = true;

        const response = await axios.post(
          this.$root.apiHost + "api/detail-kriteria-rapor",
          {
            jabatan_id: this.todo.jabatan_id,
            kriteria_ids: this.todo.kriteria_ids,
            created_by: String(this.userid),
          }
        );

        if (response.data.status === "success") {
          await Swal.fire("Success!", response.data.message, "success");
          this.resetForm();
          this.show_modal(false);
          this.refreshTable();
        }
      } catch (error) {
        if (
          error.response?.status === 422 &&
          error.response.data.message ===
            "Kombinasi jabatan dan kriteria sudah ada"
        ) {
          await Swal.fire("Error", "Jabatan Sudah Ada", "error");
        } else {
          this.handleError(error);
        }
      } finally {
        this.$root.flagButtonLoading = false;
      }
    },

    async editTodo() {
      if (!this.validateForm()) return;

      try {
        this.$root.flagButtonLoading = true;

        const response = await axios.put(
          `${this.$root.apiHost}api/detail-kriteria-rapor/${this.todo.id}`,
          {
            jabatan_id: this.todo.jabatan_id,
            kriteria_ids: this.todo.kriteria_ids,
            is_active: "1",
            updated_by: String(this.userid),
          }
        );

        if (response.data.status === "success") {
          await Swal.fire("Updated!", response.data.message, "success");
          this.resetForm();
          this.show_modal(false);
          this.refreshTable();
        }
      } catch (error) {
        console.error("Update error:", error.response?.data);
        this.handleError(error);
      } finally {
        this.$root.flagButtonLoading = false;
      }
    },

    async getData(id) {
      try {
        this.$root.presentLoading();
        const response = await axios.get(
          `${this.$root.apiHost}api/detail-kriteria-rapor/${id}?is_active=1`
        );

        console.log("Edit data response:", response.data); // Untuk debug

        if (response.data.status === "success") {
          this.todo = {
            id: response.data.data.id,
            jabatan_id: response.data.data.jabatan_id,
            kriteria_ids: response.data.data.kriteria_ids || [],
            is_active: response.data.data.is_active,
          };
        }
      } catch (error) {
        console.error("Error fetching data:", error);
        toast.error("Gagal mengambil data");
      } finally {
        this.$root.stopLoading();
      }
    },

    async deleteTodo(id) {
      const result = await Swal.fire({
        title: "Hapus Detail Kriteria",
        text: "Apakah anda yakin?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya",
        cancelButtonText: "Batal",
      });

      if (result.isConfirmed) {
        try {
          this.$root.presentLoading();
          await axios.delete(
            `${this.$root.apiHost}api/detail-kriteria-rapor/${id}`,
            {
              data: { deleted_by: String(this.userid) },
            }
          );

          this.$root.stopLoading();
          await Swal.fire("Deleted!", "Data berhasil dihapus", "success");
          this.refreshTable();
        } catch (error) {
          this.$root.stopLoading();
          console.error("Error:", error);
          toast.error("Gagal menghapus data");
        }
      }
    },

    handleError(error) {
      if (error.response?.status === 422) {
        const errors = error.response.data;
        Object.keys(errors).forEach((key) => {
          this.errorField[key] = true;
          toast.error(errors[key][0], { theme: "colored" });
        });
      } else {
        toast.error(error.response?.data?.message || "Terjadi kesalahan", {
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
  border: 1px solid red;
}

.modal-dialog {
  width: 600px;
  margin: 30px auto;
}

.kriteria-list {
  max-height: 200px;
  overflow-y: auto;
  border: 1px solid #ddd;
  padding: 10px;
  border-radius: 4px;
}

.kriteria-item {
  margin-bottom: 8px;
}

.kriteria-item:last-child {
  margin-bottom: 0;
}

.text-danger {
  color: red;
  font-size: 12px;
  margin-top: 5px;
}
</style>
